<?php

namespace Tests\Feature;

use App\Enums\ReviewInvitationStatus;
use App\Enums\ReviewStatus;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductReviewSubmissionTest extends TestCase
{
    use RefreshDatabase, ReviewTestHelpers;

    public function test_customer_can_review_several_products_at_once_and_skip_others(): void
    {
        [$a, $b, $c] = Product::factory()->count(3)->create()->all();
        $order = $this->orderWith([$a, $b, $c]);
        $token = $this->sendInvitation($order);

        $this->post(route('reviews.order.store', $token), [
            'reviews' => [
                $a->id => $this->reviewPayload('Beautiful fabric.', 5),
                $b->id => $this->reviewPayload('Runs a little small.', 3, null),
                $c->id => ['rating' => '', 'title' => '', 'comment' => '', 'customer_name' => 'Jane C.'],
            ],
        ])->assertRedirect(route('reviews.order.show', $token))->assertSessionHasNoErrors();

        $this->assertDatabaseCount('product_reviews', 2);
        $review = ProductReview::query()->where('product_id', $a->id)->firstOrFail();
        $this->assertSame(ReviewStatus::Pending, $review->status);
        $this->assertSame($order->id, $review->order_id);
        $this->assertSame($order->items->firstWhere('product_id', $a->id)->id, $review->order_item_id);
        $this->assertNotNull($review->submitted_at);
        $this->assertNull(ProductReview::query()->where('product_id', $b->id)->value('title'));

        $invitation = $this->invitationFor($order);
        $this->assertNull($invitation->completed_at);
        $this->assertSame(ReviewInvitationStatus::PartiallyReviewed, $invitation->status());

        // Reopening the link marks what's done and still offers the rest.
        $page = $this->get(route('reviews.order.show', $token))->assertOk()->assertSee('Reviewed')->assertSee('Awaiting approval');
        $this->assertStringNotContainsString('name="reviews['.$a->id.'][comment]"', $page->getContent());
        $this->assertStringContainsString('name="reviews['.$c->id.'][comment]"', $page->getContent());

        $this->post(route('reviews.order.store', $token), [
            'reviews' => [$c->id => $this->reviewPayload('Perfect.', 4)],
        ])->assertSessionHasNoErrors();

        $invitation->refresh();
        $this->assertNotNull($invitation->completed_at);
        $this->assertSame(ReviewInvitationStatus::Completed, $invitation->status());
        $this->get(route('reviews.order.show', $token))->assertOk()->assertSee('reviewed every product')->assertDontSee('Submit All Reviews');
    }

    public function test_at_least_one_review_is_required(): void
    {
        [$a, $b] = Product::factory()->count(2)->create()->all();
        $token = $this->sendInvitation($this->orderWith([$a, $b]));

        $this->post(route('reviews.order.store', $token), [
            'reviews' => [
                $a->id => ['rating' => '', 'comment' => '', 'customer_name' => 'Jane C.'],
                $b->id => ['rating' => '', 'comment' => '', 'customer_name' => 'Jane C.'],
            ],
        ])->assertSessionHasErrors('reviews');

        $this->assertDatabaseCount('product_reviews', 0);
    }

    public function test_each_filled_review_needs_a_valid_rating_and_comment(): void
    {
        $product = Product::factory()->create();
        $token = $this->sendInvitation($this->orderWith([$product]));
        $key = "reviews.{$product->id}";

        $this->post(route('reviews.order.store', $token), [
            'reviews' => [$product->id => ['rating' => 5, 'comment' => '', 'customer_name' => 'Jane C.']],
        ])->assertSessionHasErrors("{$key}.comment");

        $this->post(route('reviews.order.store', $token), [
            'reviews' => [$product->id => ['rating' => '', 'comment' => 'Nice and soft.', 'customer_name' => 'Jane C.']],
        ])->assertSessionHasErrors("{$key}.rating");

        $this->post(route('reviews.order.store', $token), [
            'reviews' => [$product->id => $this->reviewPayload(rating: 6)],
        ])->assertSessionHasErrors("{$key}.rating");

        $this->post(route('reviews.order.store', $token), [
            'reviews' => [$product->id => $this->reviewPayload(str_repeat('x', config('reviews.comment_max') + 1))],
        ])->assertSessionHasErrors("{$key}.comment");

        $this->post(route('reviews.order.store', $token), [
            'reviews' => [$product->id => ['title' => str_repeat('t', config('reviews.title_max') + 1)] + $this->reviewPayload()],
        ])->assertSessionHasErrors("{$key}.title");

        $this->assertDatabaseCount('product_reviews', 0);
    }

    public function test_products_outside_the_order_cannot_be_reviewed_and_nothing_is_saved(): void
    {
        $inOrder = Product::factory()->create();
        $notInOrder = Product::factory()->create();
        $token = $this->sendInvitation($this->orderWith([$inOrder]));

        $this->post(route('reviews.order.store', $token), [
            'reviews' => [
                $inOrder->id => $this->reviewPayload(),
                $notInOrder->id => $this->reviewPayload(),
            ],
        ])->assertSessionHasErrors('reviews');

        // All-or-nothing: the valid review in the same submission wasn't kept either.
        $this->assertDatabaseCount('product_reviews', 0);
    }

    public function test_a_token_for_one_order_cannot_review_another_orders_products(): void
    {
        $mine = Product::factory()->create();
        $theirs = Product::factory()->create();
        $token = $this->sendInvitation($this->orderWith([$mine]));
        $this->orderWith([$theirs]);

        $this->post(route('reviews.order.store', $token), [
            'reviews' => [$theirs->id => $this->reviewPayload()],
        ])->assertSessionHasErrors('reviews');

        $this->assertDatabaseCount('product_reviews', 0);
    }

    public function test_resubmitting_does_not_duplicate_or_overwrite_a_review(): void
    {
        $product = Product::factory()->create();
        $token = $this->sendInvitation($this->orderWith([$product]));

        $this->post(route('reviews.order.store', $token), ['reviews' => [$product->id => $this->reviewPayload('First thoughts.')]]);
        $this->post(route('reviews.order.store', $token), ['reviews' => [$product->id => $this->reviewPayload('Changed my mind!', 1)]])
            ->assertSessionHas('review_status', fn ($message) => str_contains($message, 'already'));

        $this->assertDatabaseCount('product_reviews', 1);
        $this->assertSame('First thoughts.', ProductReview::query()->value('comment'));
        $this->assertSame(5, ProductReview::query()->value('rating'));
    }

    public function test_markup_is_stripped_from_submitted_text(): void
    {
        $product = Product::factory()->create();
        $token = $this->sendInvitation($this->orderWith([$product]));

        $this->post(route('reviews.order.store', $token), [
            'reviews' => [$product->id => [
                'rating' => 4,
                'title' => '<b>Bold</b> claim',
                'comment' => '<script>alert(1)</script>Great fit <img src=x onerror=alert(2)>',
                'customer_name' => '<i>Jane</i>',
            ]],
        ])->assertSessionHasNoErrors();

        $review = ProductReview::query()->firstOrFail();
        $this->assertSame('Bold claim', $review->title);
        $this->assertSame('alert(1)Great fit', $review->comment);
        $this->assertSame('Jane', $review->customer_name);
    }

    public function test_a_review_made_only_of_markup_is_rejected(): void
    {
        $product = Product::factory()->create();
        $token = $this->sendInvitation($this->orderWith([$product]));

        $this->post(route('reviews.order.store', $token), [
            'reviews' => [$product->id => ['rating' => 5, 'comment' => '<script></script>', 'customer_name' => 'Jane']],
        ])->assertSessionHasErrors("reviews.{$product->id}.comment");

        $this->assertDatabaseCount('product_reviews', 0);
    }

    public function test_honeypot_submissions_are_rejected(): void
    {
        $product = Product::factory()->create();
        $token = $this->sendInvitation($this->orderWith([$product]));

        $this->post(route('reviews.order.store', $token), [
            'website' => 'http://spam.example',
            'reviews' => [$product->id => $this->reviewPayload()],
        ])->assertSessionHasErrors('website');

        $this->assertDatabaseCount('product_reviews', 0);
    }

    public function test_client_cannot_inject_order_or_status_fields(): void
    {
        $product = Product::factory()->create();
        $order = $this->orderWith([$product]);
        $token = $this->sendInvitation($order);

        $this->post(route('reviews.order.store', $token), [
            'reviews' => [$product->id => $this->reviewPayload() + ['status' => 'approved', 'order_id' => 999]],
        ])->assertSessionHasErrors("reviews.{$product->id}");

        $this->assertDatabaseCount('product_reviews', 0);
    }

    public function test_submissions_are_rate_limited(): void
    {
        $product = Product::factory()->create();
        $token = $this->sendInvitation($this->orderWith([$product]));
        $empty = ['reviews' => [$product->id => ['rating' => '', 'comment' => '', 'customer_name' => 'J']]];

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('reviews.order.store', $token), $empty)->assertRedirect();
        }

        $this->post(route('reviews.order.store', $token), $empty)->assertStatus(429);
    }
}
