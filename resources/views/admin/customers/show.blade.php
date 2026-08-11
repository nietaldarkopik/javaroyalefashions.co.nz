@extends('layouts.admin')

@section('title', 'Customer — '.$customer->name)
@section('page_title', $customer->name)

@section('main_content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Contact Details</div>
            <div class="card-body">
                <p class="mb-1"><strong>Email:</strong> {{ $customer->email }}</p>
                <p class="mb-1"><strong>Phone / WhatsApp:</strong> {{ $customer->phone }}</p>
                <p class="mb-1"><strong>Customer Since:</strong> {{ $customer->created_at->format('d M Y') }}</p>
                <p class="mb-0"><strong>Total Orders:</strong> {{ $customer->orders_count }}</p>
                <p class="mb-0"><strong>Total Spent:</strong> ${{ number_format($customer->orders_sum_grand_total ?? 0, 2) }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Order History</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>Order #</th><th>Status</th><th>Date</th><th class="text-right">Total</th></tr></thead>
                    <tbody>
                        @forelse ($customer->orders as $order)
                        <tr>
                            <td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                            <td><span class="badge bg-{{ $order->status->badgeColor() }}">{{ $order->status->label() }}</span></td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
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
