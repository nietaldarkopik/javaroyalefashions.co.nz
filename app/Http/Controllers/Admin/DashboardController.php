<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly CustomerRepositoryInterface $customers,
        private readonly ProductRepositoryInterface $products,
    ) {}

    public function index(): View
    {
        $statusCounts = collect(OrderStatus::cases())
            ->mapWithKeys(fn (OrderStatus $status) => [$status->value => $this->orders->countByStatus($status)]);

        return view('admin.dashboard.index', [
            'totalSales' => $this->orders->totalSales(),
            'statusCounts' => $statusCounts,
            'pendingReviewCount' => $statusCounts->get(OrderStatus::WaitingVerification->value, 0),
            'customerCount' => $this->customers->count(),
            'recentOrders' => $this->orders->recent(8),
        ]);
    }
}
