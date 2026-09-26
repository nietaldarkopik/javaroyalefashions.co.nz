<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Mail\ReviewInvitationMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\ReviewInvitation;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

trait ReviewTestHelpers
{
    protected function admin(): User
    {
        return User::query()->create([
            'name' => 'Admin',
            'email' => 'admin'.Str::random(6).'@example.test',
            'password' => 'password',
            'role' => 'admin',
        ]);
    }

    /**
     * @param  array<int, Product>  $products
     */
    protected function orderWith(array $products, OrderStatus $status = OrderStatus::Paid): Order
    {
        $order = Order::factory()->create([
            'status' => $status,
            'customer_name' => 'Jane Customer',
            'customer_email' => 'jane@example.test',
        ]);

        foreach ($products as $product) {
            $order->items()->create([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'unit_price' => 50,
                'quantity' => 1,
                'line_total' => 50,
            ]);
        }

        return $order->fresh('items');
    }

    /**
     * Send an invitation through the admin endpoint and return the plain
     * token from the emailed link.
     */
    protected function sendInvitation(Order $order, bool $resend = false): string
    {
        Mail::fake();

        $this->actingAs($this->admin())
            ->post(route('admin.orders.review-invitation', $order), $resend ? ['resend' => 1] : [])
            ->assertSessionHasNoErrors();

        $token = null;
        Mail::assertSent(ReviewInvitationMail::class, function (ReviewInvitationMail $mail) use (&$token) {
            $token = Str::afterLast($mail->reviewUrl, '/');

            return true;
        });

        // The customer opens the link in their own browser: drop the admin
        // login and its flash messages before the next request.
        auth()->logout();
        $this->flushSession();

        return $token;
    }

    /**
     * Backdate/expire an invitation without going through the mailer.
     */
    protected function invitationFor(Order $order): ReviewInvitation
    {
        return ReviewInvitation::query()->where('order_id', $order->id)->firstOrFail();
    }

    protected function reviewPayload(string $comment = 'Lovely quality, fits well.', int $rating = 5, ?string $title = 'Great'): array
    {
        return [
            'rating' => $rating,
            'title' => $title,
            'comment' => $comment,
            'customer_name' => 'Jane C.',
        ];
    }
}
