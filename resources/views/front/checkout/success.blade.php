@php($step = 3)
@extends('layouts.checkout')

@section('title', 'Order '.$order->order_number.' — '.$siteSetting->site_name)

@section('content')

<div class="wrap">
  <div class="confirm-hero">
    <div class="confirm-icon"><i class="fa-solid fa-check"></i></div>
    <span class="eyebrow" style="display:block; text-align:center;">Order Confirmed</span>
    <h1>Thank you, {{ $order->customer_name }}!</h1>
    <p>Your order has been received and an invoice has been sent to {{ $order->customer_email }}. We'll email you again once your payment is verified.</p>
    <div class="confirm-order-no">Order No: {{ $order->order_number }}</div>

    <div class="confirm-meta">
      <div>
        <div class="label">Shipping To</div>
        <div class="value">{{ $order->shipping_area->label() }}</div>
      </div>
      <div>
        <div class="label">Payment Method</div>
        <div class="value">Bank Transfer</div>
      </div>
      <div>
        <div class="label">Total</div>
        <div class="value">${{ number_format($order->grand_total, 2) }} {{ $order->currency }}</div>
      </div>
    </div>

    <div class="confirm-actions">
      <a href="{{ route('products.index') }}" class="btn btn--outline">Continue Shopping</a>
    </div>
  </div>

  <div class="checkout-layout" style="padding-top:0;">
    <div class="checkout-main">
      <div class="checkout-section">
        <h2>Order Summary</h2>
        @foreach ($order->items as $item)
        <div class="cart-line">
          <div class="ph"><span>PHOTO</span></div>
          <div class="cart-line-info">
            <h5>{{ $item->product_name }}</h5>
            @if ($item->variant_label)
            <div class="meta">{{ $item->variant_label }}</div>
            @endif
            <div class="meta">Qty {{ $item->quantity }}</div>
            <div class="price">${{ number_format($item->line_total, 2) }}</div>
          </div>
        </div>
        @endforeach
        <div class="summary-totals">
          <div class="summary-row"><span>Subtotal</span><span>${{ number_format($order->subtotal, 2) }}</span></div>
          <div class="summary-row"><span>Shipping ({{ $order->shipping_area->label() }})</span><span>${{ number_format($order->shipping_cost, 2) }}</span></div>
          <div class="summary-row total"><span>Total</span><span>${{ number_format($order->grand_total, 2) }} {{ $order->currency }}</span></div>
        </div>
      </div>
    </div>

    <aside class="order-summary">
      <h2><i class="fa-solid fa-building-columns"></i> Bank Transfer Details</h2>
      <div class="form-row"><label>Bank</label><div>{{ $setting->bank_name }}</div></div>
      <div class="form-row"><label>Account Name</label><div>{{ $setting->bank_account_name }}</div></div>
      <div class="form-row"><label>Account Number</label><div>{{ $setting->bank_account_number }}</div></div>
      @if ($setting->bank_swift_code)
      <div class="form-row"><label>SWIFT / BIC</label><div>{{ $setting->bank_swift_code }}</div></div>
      @endif
      <div class="form-row">
        <label>Reference</label>
        <div><strong>{{ $order->order_number }}</strong> — please use this as your transfer reference</div>
      </div>

      <hr style="border:none; border-top:1px solid var(--line); margin:24px 0;">

      <h2>Upload Proof of Payment</h2>

      @if ($order->paymentProofs->isNotEmpty())
      <div style="margin-bottom:18px;">
        @foreach ($order->paymentProofs as $proof)
        <div style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid var(--line); font-size:13px;">
          <span>{{ $proof->original_filename }}</span>
          @if ($proof->is_verified)
          <span style="color:var(--sage); font-weight:600;">Verified <i class="fa-solid fa-check"></i></span>
          @else
          <span style="color:var(--ink-soft);">Pending Review</span>
          @endif
        </div>
        @endforeach
      </div>
      @endif

      <form action="{{ route('checkout.proof', $order->order_number) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-row">
          <input type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf" required>
          <div style="font-size:12px; color:var(--ink-soft); margin-top:8px;">JPG, PNG, or PDF — max 5MB.</div>
          @error('proof')<div style="color:var(--rust-deep); font-size:12px; margin-top:6px;">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn--rust" style="width:100%; justify-content:center;">Upload Proof</button>
      </form>
    </aside>
  </div>
</div>

@endsection
