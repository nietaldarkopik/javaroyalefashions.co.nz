@extends('layouts.admin')

@section('title', 'Orders')
@section('page_title', 'Manage Orders')

@section('main_content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Order #, customer name or email" value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    @foreach ($statuses as $option)
                    <option value="{{ $option['value'] }}" @selected(request('status') === $option['value'])>{{ $option['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-secondary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0 align-middle">
            <thead>
                <tr><th>Order #</th><th>Customer</th><th>Total</th><th>Status</th><th>Placed</th><th class="text-right">Actions</th></tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->customer_name }}<br><span class="text-muted small">{{ $order->customer_email }}</span></td>
                    <td>${{ number_format($order->grand_total, 2) }}</td>
                    <td><span class="badge bg-{{ $order->status->badgeColor() }}">{{ $order->status->label() }}</span></td>
                    <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                    <td class="text-right">
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-3">No orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $orders->links() }}</div>
@endsection
