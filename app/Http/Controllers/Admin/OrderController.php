<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly OrderService $orderService,
    ) {}

    public function index(Request $request): View
    {
        return view('admin.orders.index', [
            'orders' => $this->orders->paginateForAdmin($request->only(['status', 'search'])),
            'statuses' => OrderStatus::options(),
        ]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', [
            'order' => $order->load(['customer', 'items.product', 'paymentProofs', 'verifiedBy']),
            'statuses' => OrderStatus::options(),
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $next = OrderStatus::from($request->validated('status'));

        try {
            $this->orderService->updateStatus($order, $next, $request->validated('admin_notes'));
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return back()->with('status', "Order marked as {$next->label()}.");
    }

    public function verifyPayment(Order $order): RedirectResponse
    {
        $this->orderService->verifyPayment($order, Auth::user());

        return back()->with('status', 'Payment verified — order marked as Paid.');
    }
}
