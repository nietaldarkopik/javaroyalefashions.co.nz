<?php

namespace App\Mail;

use App\Models\Order;
use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Deliberately NOT ShouldQueue: the review URL carries the plain token,
 * and a queued mailable would persist it in the jobs table — defeating
 * the point of storing only its hash. Sending inline also lets the admin
 * see a delivery failure straight away.
 */
class ReviewInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Order $order,
        public readonly string $reviewUrl,
        public readonly string $siteName,
        public readonly CarbonInterface $expiresAt,
    ) {}

    public function build(): self
    {
        return $this->subject('How was your purchase? Review your products')
            ->view('emails.review-invitation');
    }
}
