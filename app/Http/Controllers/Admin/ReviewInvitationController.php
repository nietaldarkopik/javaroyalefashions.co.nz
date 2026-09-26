<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReviewInvitationStatus;
use App\Exceptions\ReviewInvitationException;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ReviewInvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewInvitationController extends Controller
{
    public function __construct(
        private readonly ReviewInvitationService $invitations,
    ) {}

    public function index(Request $request): View
    {
        return view('admin.review-invitations.index', [
            'invitations' => $this->invitations->paginateForAdmin($request->only(['status', 'search'])),
            'statuses' => ReviewInvitationStatus::options(),
        ]);
    }

    public function store(Request $request, Order $order): RedirectResponse
    {
        $resend = $request->boolean('resend');

        try {
            $this->invitations->send($order, $resend);
        } catch (ReviewInvitationException $e) {
            return back()->withErrors(['review_invitation' => $e->getMessage()]);
        }

        return back()->with('status', $resend
            ? "A new review link was emailed to {$order->customer_email}. The previous link no longer works."
            : "Review invitation emailed to {$order->customer_email}.");
    }
}
