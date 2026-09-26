<?php

namespace App\Services;

use App\Exceptions\ReviewInvitationException;
use App\Mail\ReviewInvitationMail;
use App\Models\Order;
use App\Models\ReviewInvitation;
use App\Repositories\Contracts\ReviewInvitationRepositoryInterface;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class ReviewInvitationService
{
    public function __construct(
        private readonly ReviewInvitationRepositoryInterface $invitations,
        private readonly SettingService $settings,
    ) {}

    /**
     * Email the customer one link covering every product in the order.
     *
     * An order gets at most one invitation. Sending again requires an
     * explicit $resend — and since only the token's hash is stored, the
     * old link can't be re-sent: a resend issues a fresh token (the old
     * link stops working) and restarts the expiry window. An invitation
     * whose first send failed can be retried without $resend, because
     * the customer never received it.
     *
     * @throws ReviewInvitationException
     */
    public function send(Order $order, bool $resend = false): ReviewInvitation
    {
        $order->loadMissing('items');

        if (! $order->isReviewable()) {
            throw new ReviewInvitationException('Review invitations can only be sent for paid orders that have not been cancelled.');
        }

        if ($order->reviewableItems()->isEmpty()) {
            throw new ReviewInvitationException('This order has no products that can still be reviewed.');
        }

        $token = Str::random(64);
        $tokenHash = ReviewInvitation::hashToken($token);
        $expiresAt = now()->addDays(config('reviews.invitation_expiry_days'));

        try {
            $invitation = DB::transaction(function () use ($order, $resend, $tokenHash, $expiresAt) {
                $invitation = ReviewInvitation::query()->where('order_id', $order->id)->lockForUpdate()->first();

                if ($invitation?->isCompleted()) {
                    throw new ReviewInvitationException('Every product in this order has already been reviewed.');
                }

                if ($invitation?->sent_at && ! $resend) {
                    throw new ReviewInvitationException('A review invitation has already been sent for this order. Use "Resend" to send a new link.');
                }

                // A brand-new invitation gets the new token straight away
                // (no link exists yet). An existing one keeps its current
                // token until the new email has actually gone out, so a
                // failed resend doesn't kill the link the customer has.
                return $invitation ?? ReviewInvitation::query()->create([
                    'order_id' => $order->id,
                    'customer_email' => $order->customer_email,
                    'token_hash' => $tokenHash,
                    'expires_at' => $expiresAt,
                ]);
            });
        } catch (UniqueConstraintViolationException) {
            // Two admins (or a double click) raced to create the first
            // invitation — the other request won.
            throw new ReviewInvitationException('A review invitation is already being sent for this order. Refresh the page to see it.');
        }

        try {
            Mail::to($order->customer_email)->send(new ReviewInvitationMail(
                $order,
                route('reviews.order.show', $token),
                $this->settings->current()->site_name ?: config('app.name'),
                $expiresAt,
            ));
        } catch (Throwable $e) {
            Log::error('Review invitation email failed', [
                'order_id' => $order->id,
                'invitation_id' => $invitation->id,
                'error' => $e->getMessage(),
            ]);

            $invitation->update([
                'last_failed_at' => now(),
                'last_error' => Str::limit($e->getMessage(), 490),
            ]);

            throw new ReviewInvitationException('The review invitation email could not be sent. Please check the mail settings and try again.', 0, $e);
        }

        $invitation->update([
            'customer_email' => $order->customer_email,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt,
            'sent_at' => now(),
            'send_count' => $invitation->send_count + 1,
            'last_error' => null,
        ]);

        return $invitation->fresh();
    }

    public function findByToken(string $token): ?ReviewInvitation
    {
        return $this->invitations->findByToken($token);
    }

    /**
     * @param  array{status?: string, search?: string}  $filters
     */
    public function paginateForAdmin(array $filters)
    {
        return $this->invitations->paginateForAdmin($filters);
    }
}
