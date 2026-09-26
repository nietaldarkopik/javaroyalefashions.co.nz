@extends('layouts.admin')

@section('title', 'Order '.$order->order_number)
@section('page_title', 'Order '.$order->order_number)
@section('page_actions')
    <span class="badge bg-{{ $order->status->badgeColor() }}" style="font-size:1rem;">{{ $order->status->label() }}</span>
@endsection

@section('main_content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">Items</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>Product</th><th>SKU</th><th>Unit Price</th><th>Qty</th><th class="text-right">Line Total</th></tr></thead>
                    <tbody>
                        @foreach ($order->items as $item)
                        <tr>
                            <td>
                                {{ $item->product_name }}
                                @if ($item->variant_label)
                                <br><span class="text-muted small">{{ $item->variant_label }}</span>
                                @endif
                            </td>
                            <td>{{ $item->product_sku ?? '—' }}</td>
                            <td>${{ number_format($item->unit_price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td class="text-right">${{ number_format($item->line_total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr><td colspan="4" class="text-right">Subtotal</td><td class="text-right">${{ number_format($order->subtotal, 2) }}</td></tr>
                        <tr><td colspan="4" class="text-right">Shipping ({{ $order->shipping_area->label() }})</td><td class="text-right">${{ number_format($order->shipping_cost, 2) }}</td></tr>
                        <tr class="font-weight-bold"><td colspan="4" class="text-right">Grand Total</td><td class="text-right">${{ number_format($order->grand_total, 2) }} {{ $order->currency }}</td></tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Payment Proofs</div>
            <div class="card-body">
                @forelse ($order->paymentProofs as $proof)
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <div>
                        <a href="{{ Storage::disk('public')->url($proof->file_path) }}" target="_blank">{{ $proof->original_filename }}</a>
                        <span class="text-muted small d-block">Uploaded {{ $proof->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <div>
                        @if ($proof->is_verified)
                        <span class="badge bg-success">Verified</span>
                        @else
                        <span class="badge bg-secondary">Pending</span>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-muted mb-0">No payment proof uploaded yet.</p>
                @endforelse

                @if ($order->paymentProofs->isNotEmpty() && ! $order->paymentProofs->first()->is_verified)
                <form action="{{ route('admin.orders.verify', $order) }}" method="POST" class="mt-3" onsubmit="return confirm('Mark this order as Paid?');">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check"></i> Verify Payment &amp; Mark Paid</button>
                </form>
                @endif
            </div>
        </div>

        @if ($order->customer_notes)
        <div class="card mb-3">
            <div class="card-header">Customer Notes</div>
            <div class="card-body">{{ $order->customer_notes }}</div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">Customer</div>
            <div class="card-body">
                <p class="mb-1"><strong>{{ $order->customer_name }}</strong></p>
                <p class="mb-1">{{ $order->customer_email }}</p>
                <p class="mb-1">{{ $order->customer_phone }}</p>
                <a href="{{ route('admin.customers.show', $order->customer_id) }}" class="small">View customer profile &rarr;</a>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Shipping Address</div>
            <div class="card-body">
                @foreach ($order->shippingAddressLines() as $line)
                <div>{{ $line }}</div>
                @endforeach
                <span class="badge bg-light text-dark border mt-2">{{ $order->shipping_area->label() }}</span>
            </div>
        </div>

        @php
            $invitation = $order->reviewInvitation;
            $invitationStatus = $invitation?->status() ?? \App\Enums\ReviewInvitationStatus::NotSent;
            $reviewLines = $order->reviewableItems();
            $reviewsByProduct = $order->productReviews->keyBy('product_id');
        @endphp
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Product Reviews</span>
                <span class="badge bg-{{ $invitationStatus->badgeColor() }}">{{ $invitationStatus->label() }}</span>
            </div>
            <div class="card-body">
                @if ($invitation)
                <dl class="small mb-3">
                    @if ($invitation->sent_at)
                    <dt>Last sent</dt>
                    <dd>{{ $invitation->sent_at->format('d M Y H:i') }} to {{ $invitation->customer_email }}
                        @if ($invitation->send_count > 1)<span class="text-muted">({{ $invitation->send_count }} times)</span>@endif
                    </dd>
                    <dt>Link expires</dt>
                    <dd>{{ $invitation->expires_at->format('d M Y H:i') }}</dd>
                    @endif
                    @if ($invitation->completed_at)
                    <dt>Completed</dt>
                    <dd>{{ $invitation->completed_at->format('d M Y H:i') }}</dd>
                    @endif
                    @if ($invitation->last_error && (! $invitation->sent_at || $invitation->last_failed_at?->gt($invitation->sent_at)))
                    <dt class="text-danger">Last send failed</dt>
                    <dd class="text-danger">{{ $invitation->last_failed_at?->format('d M Y H:i') }} &mdash; {{ $invitation->last_error }}</dd>
                    @endif
                </dl>
                @endif

                <ul class="list-unstyled small mb-3">
                    @forelse ($reviewLines as $productId => $line)
                    @php
                        $review = $reviewsByProduct->get($productId);
                    @endphp
                    <li class="d-flex justify-content-between align-items-center border-bottom py-1">
                        <span>{{ $line['item']->product_name }}</span>
                        @if ($review)
                        <a href="{{ route('admin.reviews.index', ['order' => $order->order_number]) }}" class="badge bg-{{ $review->status->badgeColor() }}">
                            {{ $review->rating }}&#9733; &middot; {{ $review->status->label() }}
                        </a>
                        @else
                        <span class="badge bg-light text-dark border">Not reviewed</span>
                        @endif
                    </li>
                    @empty
                    <li class="text-muted">No products in this order can be reviewed.</li>
                    @endforelse
                </ul>

                @if (! $order->isReviewable())
                <p class="text-muted small mb-0">Review invitations can be sent once payment is confirmed (Paid, Processing or Completed).</p>
                @elseif ($reviewLines->isNotEmpty() && ! $invitation?->completed_at)
                    @if ($invitation?->sent_at)
                    <form action="{{ route('admin.orders.review-invitation', $order) }}" method="POST"
                          onsubmit="return confirm({{ Js::from('Email a new review link to '.$order->customer_email.'? The previous link will stop working.') }});">
                        @csrf
                        <input type="hidden" name="resend" value="1">
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100"><i class="fas fa-redo"></i> Resend Review Invitation</button>
                    </form>
                    @else
                    <form action="{{ route('admin.orders.review-invitation', $order) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fas fa-envelope"></i> Send Review Invitation</button>
                    </form>
                    @endif
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">Update Status</div>
            <div class="card-body">
                <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            @foreach ($statuses as $option)
                            <option value="{{ $option['value'] }}" @selected($order->status->value === $option['value'])>{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Admin Notes</label>
                        <textarea name="admin_notes" rows="3" class="form-control">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update Order</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
