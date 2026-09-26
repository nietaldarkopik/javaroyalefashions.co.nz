<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\SubmitOrderReviewsRequest;
use App\Models\ProductReview;
use App\Models\ReviewInvitation;
use App\Services\ProductReviewService;
use App\Services\ReviewInvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

/**
 * The one page a customer reaches from their review invitation email:
 * every product in the order, reviewed together. The token in the URL is
 * the only credential — there's no customer login.
 */
class OrderReviewController extends Controller
{
    public function __construct(
        private readonly ReviewInvitationService $invitations,
        private readonly ProductReviewService $reviews,
    ) {}

    public function show(string $token): Response
    {
        $invitation = $this->resolve($token);

        if (! $this->reviews->canSubmit($invitation)) {
            return $this->unavailable($invitation);
        }

        $order = $invitation->order->load('items.product', 'items.variant');

        return $this->private(response()->view('front.reviews.order', [
            'token' => $token,
            'invitation' => $invitation,
            'order' => $order,
            'lines' => $order->reviewableItems(),
            'existingReviews' => ProductReview::query()->where('order_id', $order->id)->get()->keyBy('product_id'),
            'defaultName' => $this->displayName($order->customer_name),
        ]));
    }

    public function store(string $token): RedirectResponse|Response
    {
        $invitation = $this->resolve($token);

        if (! $this->reviews->canSubmit($invitation)) {
            return $this->unavailable($invitation);
        }

        // Resolved here rather than type-hinted so the token is checked
        // before any validation runs — a bad link should 404, not bounce
        // back with form errors.
        $request = app(SubmitOrderReviewsRequest::class);

        $result = $this->reviews->submit($invitation, $request->validated('reviews'));

        $message = $result['created'] > 0
            ? 'Thank you! Your '.($result['created'] === 1 ? 'review has' : "{$result['created']} reviews have")
                .' been received and will appear once approved.'
            : 'We already have your review for those products — thank you!';

        return redirect()->route('reviews.order.show', $token)->with('review_status', $message);
    }

    private function resolve(string $token): ReviewInvitation
    {
        $invitation = $this->invitations->findByToken($token);

        // Unknown, replaced and malformed tokens all look the same from
        // outside, so the response gives nothing away to someone guessing.
        abort_if(! $invitation || ! $invitation->sent_at, 404);

        return $invitation->load('order');
    }

    private function unavailable(ReviewInvitation $invitation): Response
    {
        return $this->private(response()->view('front.reviews.unavailable', [
            'expired' => $invitation->isExpired(),
        ], 410));
    }

    /**
     * The URL itself is the credential: keep it out of search indexes and
     * out of the Referer header sent to fonts/CDNs/links on the page.
     */
    private function private(Response $response): Response
    {
        return $response
            ->header('X-Robots-Tag', 'noindex, nofollow')
            ->header('Referrer-Policy', 'no-referrer')
            ->header('Cache-Control', 'no-store, private');
    }

    /**
     * "Jane Doe" → "Jane D." — a friendly default that doesn't publish the
     * customer's full name unless they choose to.
     */
    private function displayName(string $fullName): string
    {
        $parts = preg_split('/\s+/', trim($fullName)) ?: [];
        $first = array_shift($parts) ?? '';
        $last = array_pop($parts);

        return $last ? $first.' '.mb_strtoupper(mb_substr($last, 0, 1)).'.' : $first;
    }
}
