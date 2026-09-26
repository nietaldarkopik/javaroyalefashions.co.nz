<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\ReviewInvitationStatus;
use App\Mail\ReviewInvitationMail;
use App\Models\Product;
use App\Models\ReviewInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

class ReviewInvitationTest extends TestCase
{
    use RefreshDatabase, ReviewTestHelpers;

    public function test_admin_can_send_one_invitation_for_a_paid_order(): void
    {
        $order = $this->orderWith(Product::factory()->count(3)->create()->all());

        $token = $this->sendInvitation($order);

        Mail::assertSent(ReviewInvitationMail::class, 1);
        Mail::assertSent(ReviewInvitationMail::class, function (ReviewInvitationMail $mail) use ($order) {
            $html = $mail->render();

            return $mail->hasTo('jane@example.test')
                && $mail->subject === 'How was your purchase? Review your products'
                && substr_count($html, 'href="'.$mail->reviewUrl.'"') === 1
                && str_contains($html, $order->order_number)
                && str_contains($html, 'Jane Customer');
        });

        $invitation = $this->invitationFor($order);
        $this->assertSame(64, strlen($token));
        $this->assertSame(hash('sha256', $token), $invitation->token_hash);
        $this->assertNotSame($token, $invitation->token_hash);
        $this->assertNotNull($invitation->sent_at);
        $this->assertTrue($invitation->expires_at->isFuture());
        $this->assertSame(ReviewInvitationStatus::Sent, $invitation->status());
    }

    public static function ineligibleStatuses(): array
    {
        return [
            'pending payment' => [OrderStatus::PendingPayment],
            'waiting verification' => [OrderStatus::WaitingVerification],
            'cancelled' => [OrderStatus::Cancelled],
        ];
    }

    #[DataProvider('ineligibleStatuses')]
    public function test_invitation_is_refused_for_orders_without_confirmed_payment(OrderStatus $status): void
    {
        Mail::fake();
        $order = $this->orderWith([Product::factory()->create()], $status);

        $this->actingAs($this->admin())
            ->post(route('admin.orders.review-invitation', $order))
            ->assertSessionHasErrors('review_invitation');

        Mail::assertNothingSent();
        $this->assertDatabaseCount('review_invitations', 0);
    }

    public function test_duplicate_invitation_is_refused_unless_resending(): void
    {
        $order = $this->orderWith([Product::factory()->create()]);
        $this->sendInvitation($order);

        Mail::fake();
        $this->actingAs($this->admin())
            ->post(route('admin.orders.review-invitation', $order))
            ->assertSessionHasErrors('review_invitation');

        Mail::assertNothingSent();
        $this->assertDatabaseCount('review_invitations', 1);
    }

    public function test_resend_issues_a_new_link_and_retires_the_old_one(): void
    {
        $order = $this->orderWith([Product::factory()->create()]);
        $oldToken = $this->sendInvitation($order);
        $newToken = $this->sendInvitation($order, resend: true);

        $this->assertNotSame($oldToken, $newToken);
        $this->assertDatabaseCount('review_invitations', 1);
        $this->assertSame(2, $this->invitationFor($order)->send_count);

        $this->get(route('reviews.order.show', $oldToken))->assertNotFound();
        $this->get(route('reviews.order.show', $newToken))->assertOk();
    }

    public function test_mail_failure_is_recorded_and_reported(): void
    {
        $order = $this->orderWith([Product::factory()->create()]);
        Mail::shouldReceive('to')->andThrow(new RuntimeException('SMTP unreachable'));

        $this->actingAs($this->admin())
            ->post(route('admin.orders.review-invitation', $order))
            ->assertSessionHasErrors('review_invitation');

        $invitation = $this->invitationFor($order);
        $this->assertNull($invitation->sent_at);
        $this->assertNotNull($invitation->last_failed_at);
        $this->assertStringContainsString('SMTP unreachable', $invitation->last_error);
        $this->assertSame(ReviewInvitationStatus::NotSent, $invitation->status());
    }

    public function test_a_failed_resend_keeps_the_existing_link_working(): void
    {
        $order = $this->orderWith([Product::factory()->create()]);
        $token = $this->sendInvitation($order);

        Mail::shouldReceive('to')->andThrow(new RuntimeException('SMTP unreachable'));
        $this->actingAs($this->admin())
            ->post(route('admin.orders.review-invitation', $order), ['resend' => 1])
            ->assertSessionHasErrors('review_invitation');
        auth()->logout();

        $this->get(route('reviews.order.show', $token))->assertOk();
    }

    public function test_review_page_lists_every_product_in_the_order_and_nothing_else(): void
    {
        [$a, $b] = Product::factory()->count(2)->create()->all();
        $other = Product::factory()->create();
        $order = $this->orderWith([$a, $b]);
        $this->orderWith([$other]);

        $token = $this->sendInvitation($order);

        $this->get(route('reviews.order.show', $token))
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertHeader('Referrer-Policy', 'no-referrer')
            ->assertSee('Review Your Purchase')
            ->assertSee($order->order_number)
            ->assertSee($a->name)
            ->assertSee($b->name)
            ->assertDontSee($other->name)
            ->assertDontSee('jane@example.test')
            ->assertSee('Submit All Reviews')
            ->assertSee('value="Jane C."', false);
    }

    public function test_same_product_in_several_variants_gets_a_single_review_form(): void
    {
        $product = Product::factory()->create();
        $order = $this->orderWith([$product, $product]);
        $order->items[0]->update(['variant_label' => 'Red / M']);
        $order->items[1]->update(['variant_label' => 'Blue / L']);

        $token = $this->sendInvitation($order);

        $response = $this->get(route('reviews.order.show', $token))->assertOk()->assertSee('Red / M, Blue / L');
        $this->assertSame(1, substr_count($response->getContent(), 'name="reviews['.$product->id.'][comment]"'));
    }

    public function test_invalid_tokens_are_rejected(): void
    {
        $order = $this->orderWith([Product::factory()->create()]);
        $this->sendInvitation($order);

        $this->get('/review/order/'.str_repeat('a', 64))->assertNotFound();
        $this->get('/review/order/short-token')->assertNotFound();
        $this->post('/review/order/'.str_repeat('a', 64), ['reviews' => []])->assertNotFound();
    }

    public function test_expired_links_are_rejected(): void
    {
        $product = Product::factory()->create();
        $order = $this->orderWith([$product]);
        $token = $this->sendInvitation($order);
        $this->invitationFor($order)->update(['expires_at' => now()->subMinute()]);

        $this->get(route('reviews.order.show', $token))->assertStatus(410)->assertSee('expired');
        $this->post(route('reviews.order.store', $token), [
            'reviews' => [$product->id => $this->reviewPayload()],
        ])->assertStatus(410);

        $this->assertDatabaseCount('product_reviews', 0);
        $this->assertSame(ReviewInvitationStatus::Expired, $this->invitationFor($order)->status());
    }

    public function test_links_stop_working_if_the_order_is_cancelled_after_sending(): void
    {
        $product = Product::factory()->create();
        $order = $this->orderWith([$product]);
        $token = $this->sendInvitation($order);
        $order->update(['status' => OrderStatus::Cancelled]);

        $this->get(route('reviews.order.show', $token))->assertStatus(410);
        $this->post(route('reviews.order.store', $token), [
            'reviews' => [$product->id => $this->reviewPayload()],
        ])->assertStatus(410);

        $this->assertDatabaseCount('product_reviews', 0);
    }

    public function test_an_unsent_invitation_link_does_not_work(): void
    {
        $order = $this->orderWith([Product::factory()->create()]);
        $token = str_repeat('b', 64);
        ReviewInvitation::query()->create([
            'order_id' => $order->id,
            'customer_email' => $order->customer_email,
            'token_hash' => ReviewInvitation::hashToken($token),
            'expires_at' => now()->addDay(),
        ]);

        $this->get(route('reviews.order.show', $token))->assertNotFound();
    }

    public function test_admin_order_page_shows_invitation_tracking(): void
    {
        [$a, $b] = Product::factory()->count(2)->create()->all();
        $order = $this->orderWith([$a, $b]);

        $this->actingAs($this->admin())
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee('Send Review Invitation');

        $this->sendInvitation($order);

        $this->actingAs($this->admin())
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee('Resend Review Invitation')
            ->assertSee('Not reviewed');

        $this->actingAs($this->admin())
            ->get(route('admin.review-invitations.index', ['status' => 'sent']))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('0 / 2');
    }
}
