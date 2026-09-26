@extends('layouts.admin')

@section('title', 'Review Invitations')
@section('page_title', 'Review Invitations')

@section('main_content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row">
            <div class="col-md-5 mb-2 mb-md-0">
                <input type="text" name="search" class="form-control" placeholder="Order #, customer name or email" value="{{ request('search') }}">
            </div>
            <div class="col-md-4 mb-2 mb-md-0">
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
        <p class="text-muted small mb-0 mt-2">Invitations are sent from an order's page. Orders without an invitation don't appear here.</p>
    </div>
</div>

<div class="card">
    <div class="card-body p-0 table-responsive">
        <table class="table mb-0 align-middle">
            <thead>
                <tr><th>Order #</th><th>Customer</th><th>Reviewed</th><th>Status</th><th>Sent</th><th>Expires</th><th class="text-right">Actions</th></tr>
            </thead>
            <tbody>
                @forelse ($invitations as $invitation)
                @php
                    $status = $invitation->status();
                    $order = $invitation->order;
                    $total = $order?->reviewableItems()->count() ?? 0;
                @endphp
                <tr>
                    <td>
                        @if ($order)
                        <a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a>
                        @endif
                    </td>
                    <td>{{ $order?->customer_name }}<br><span class="text-muted small">{{ $invitation->customer_email }}</span></td>
                    <td>
                        {{ $invitation->reviews_count }} / {{ $total }}
                        @if ($invitation->reviews_count > 0 && $order)
                        <br><a href="{{ route('admin.reviews.index', ['order' => $order->order_number]) }}" class="small">View reviews</a>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-{{ $status->badgeColor() }}">{{ $status->label() }}</span>
                        @if ($invitation->last_error && (! $invitation->sent_at || $invitation->last_failed_at?->gt($invitation->sent_at)))
                        <br><span class="text-danger small" title="{{ $invitation->last_error }}">Last send failed</span>
                        @endif
                    </td>
                    <td>
                        {{ $invitation->sent_at?->format('d M Y H:i') ?? '—' }}
                        @if ($invitation->send_count > 1)<br><span class="text-muted small">{{ $invitation->send_count }} times</span>@endif
                    </td>
                    <td>{{ $invitation->expires_at->format('d M Y') }}</td>
                    <td class="text-right text-nowrap">
                        @if ($order && ! $invitation->completed_at && $order->isReviewable())
                        <form action="{{ route('admin.orders.review-invitation', $order) }}" method="POST" class="d-inline"
                              onsubmit="return confirm({{ Js::from('Email a new review link to '.$order->customer_email.'? Any previous link will stop working.') }});">
                            @csrf
                            <input type="hidden" name="resend" value="1">
                            <button type="submit" class="btn btn-sm btn-outline-primary"><i class="fas fa-redo"></i> {{ $invitation->sent_at ? 'Resend' : 'Send' }}</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-3">No review invitations found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $invitations->links() }}</div>
@endsection
