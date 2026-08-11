@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('main_content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>${{ number_format($totalSales, 2) }}</h3>
                <p>Total Sales (Paid+)</p>
            </div>
            <div class="icon"><i class="fas fa-dollar-sign"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $pendingReviewCount }}</h3>
                <p>Awaiting Payment Review</p>
            </div>
            <div class="icon"><i class="fas fa-hourglass-half"></i></div>
            <a href="{{ route('admin.orders.index', ['status' => 'waiting_verification']) }}" class="small-box-footer">
                Review now <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $customerCount }}</h3>
                <p>Customers</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
            <a href="{{ route('admin.customers.index') }}" class="small-box-footer">
                View all <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>{{ $statusCounts->sum() }}</h3>
                <p>Total Orders</p>
            </div>
            <div class="icon"><i class="fas fa-receipt"></i></div>
            <a href="{{ route('admin.orders.index') }}" class="small-box-footer">
                View all <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Orders by Status</h3></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    @foreach (\App\Enums\OrderStatus::cases() as $status)
                    <tr>
                        <td><span class="badge bg-{{ $status->badgeColor() }}">{{ $status->label() }}</span></td>
                        <td class="text-right">{{ $statusCounts->get($status->value, 0) }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Recent Orders</h3></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr><th>Order #</th><th>Customer</th><th>Status</th><th class="text-right">Total</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($recentOrders as $order)
                        <tr>
                            <td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                            <td>{{ $order->customer_name }}</td>
                            <td><span class="badge bg-{{ $order->status->badgeColor() }}">{{ $order->status->label() }}</span></td>
                            <td class="text-right">${{ number_format($order->grand_total, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
