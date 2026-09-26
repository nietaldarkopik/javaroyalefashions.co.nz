<?php

namespace Tests\Feature;

use App\Enums\ReviewStatus;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductReviewModerationTest extends TestCase
{
    use RefreshDatabase, ReviewTestHelpers;

    public function test_product_page_invites_the_first_review_when_there_are_none(): void
    {
        $product = Product::factory()->create();

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertSee('Be the first to review this product.');
    }

    public function test_reviews_stay_hidden_until_approved_individually(): void
    {
        [$a, $b] = Product::factory()->count(2)->create()->all();
        $order = $this->orderWith([$a, $b]);
        $token = $this->sendInvitation($order);

        $this->post(route('reviews.order.store', $token), [
            'reviews' => [
                $a->id => $this->reviewPayload('Stunning <em>colour</em>, washes well.', 4, 'Love it'),
                $b->id => $this->reviewPayload('Great bag for the price.', 5),
            ],
        ])->assertSessionHasNoErrors();

        $this->get(route('products.show', $a->slug))
            ->assertOk()
            ->assertDontSee('Stunning colour')
            ->assertSee('Be the first to review this product.');

        $reviewA = ProductReview::query()->where('product_id', $a->id)->firstOrFail();

        $this->actingAs($this->admin())
            ->patch(route('admin.reviews.status', $reviewA), ['status' => 'approved'])
            ->assertSessionHasNoErrors();

        $reviewA->refresh();
        $this->assertSame(ReviewStatus::Approved, $reviewA->status);
        $this->assertNotNull($reviewA->moderated_at);
        // Moderation is per review: the other review from the same order is untouched.
        $this->assertSame(ReviewStatus::Pending, ProductReview::query()->where('product_id', $b->id)->value('status'));

        auth()->logout();
        $this->get(route('products.show', $a->slug))
            ->assertOk()
            ->assertSee('Stunning colour, washes well.')
            ->assertSee('Love it')
            ->assertSee('Jane C.')
            ->assertSee('Based on 1 review')
            ->assertSee('4.0')
            ->assertDontSee('Great bag for the price.');

        // Approved on A only — B's page still shows none.
        $this->get(route('products.show', $b->slug))->assertOk()->assertDontSee('Great bag for the price.');
    }

    public function test_review_content_is_escaped_on_display(): void
    {
        $product = Product::factory()->create();
        $review = ProductReview::query()->create([
            'order_id' => $this->orderWith([$product])->id,
            'product_id' => $product->id,
            'customer_name' => 'Eve',
            'rating' => 5,
            'title' => 'Title',
            // Stored directly (bypassing input sanitising) to prove the
            // page escapes on output too.
            'comment' => '<script>alert("x")</script>',
            'status' => ReviewStatus::Approved,
            'submitted_at' => now(),
        ]);

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertDontSee('<script>alert("x")</script>', false)
            ->assertSee('&lt;script&gt;', false);

        $this->actingAs($this->admin())
            ->get(route('admin.reviews.index'))
            ->assertOk()
            ->assertDontSee('<script>alert("x")</script>', false);

        $this->assertNotNull($review->id);
    }

    public function test_rejected_reviews_are_not_shown(): void
    {
        $product = Product::factory()->create();
        $token = $this->sendInvitation($this->orderWith([$product]));
        $this->post(route('reviews.order.store', $token), ['reviews' => [$product->id => $this->reviewPayload('Spammy text here.')]]);

        $this->actingAs($this->admin())
            ->patch(route('admin.reviews.status', ProductReview::query()->firstOrFail()), ['status' => 'rejected']);
        auth()->logout();

        $this->get(route('products.show', $product->slug))->assertOk()->assertDontSee('Spammy text here.');
    }

    public function test_admin_can_filter_reviews(): void
    {
        [$a, $b] = Product::factory()->count(2)->create()->all();
        $order = $this->orderWith([$a, $b]);
        $token = $this->sendInvitation($order);
        $this->post(route('reviews.order.store', $token), [
            'reviews' => [
                $a->id => $this->reviewPayload('Comment for product A.'),
                $b->id => $this->reviewPayload('Comment for product B.'),
            ],
        ]);
        ProductReview::query()->where('product_id', $b->id)->update(['status' => ReviewStatus::Approved->value]);

        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.reviews.index', ['product_id' => $a->id]))
            ->assertOk()->assertSee('Comment for product A.')->assertDontSee('Comment for product B.');

        $this->actingAs($admin)->get(route('admin.reviews.index', ['status' => 'approved']))
            ->assertOk()->assertSee('Comment for product B.')->assertDontSee('Comment for product A.');

        $this->actingAs($admin)->get(route('admin.reviews.index', ['order' => $order->order_number]))
            ->assertOk()->assertSee('Comment for product A.')->assertSee('Comment for product B.');

        $this->actingAs($admin)->get(route('admin.reviews.index', ['order' => 'NO-SUCH-ORDER']))
            ->assertOk()->assertSee('No reviews found.');
    }

    public function test_deleting_a_review_reopens_that_product_on_the_invitation(): void
    {
        $product = Product::factory()->create();
        $order = $this->orderWith([$product]);
        $token = $this->sendInvitation($order);
        $this->post(route('reviews.order.store', $token), ['reviews' => [$product->id => $this->reviewPayload()]]);
        $this->assertNotNull($this->invitationFor($order)->completed_at);

        $this->actingAs($this->admin())
            ->delete(route('admin.reviews.destroy', ProductReview::query()->firstOrFail()))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('product_reviews', 0);
        $this->assertNull($this->invitationFor($order)->completed_at);
    }

    public function test_review_admin_requires_login(): void
    {
        $this->get(route('admin.reviews.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.review-invitations.index'))->assertRedirect(route('admin.login'));
    }
}
