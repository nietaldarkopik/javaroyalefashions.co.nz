@php($step = 2)
@extends('layouts.checkout')

@section('title', 'Checkout — '.$siteSetting->site_name)

@section('content')

<div class="wrap">
  <form class="checkout-layout" action="{{ route('checkout.store') }}" method="POST">
    @csrf

    <div class="checkout-main">

      <div class="checkout-section">
        <h2><span class="step-num">1</span> Contact Details</h2>
        <div class="form-row">
          <label>Email Address</label>
          <input type="email" name="email" value="{{ old('email') }}" placeholder="you@email.com" required>
          @error('email')<div style="color:var(--rust-deep); font-size:12px; margin-top:6px;">{{ $message }}</div>@enderror
        </div>
        <div class="form-row" style="margin-bottom:0;">
          <label>Phone / WhatsApp Number</label>
          <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="02x xxx xxxx" required>
          @error('phone')<div style="color:var(--rust-deep); font-size:12px; margin-top:6px;">{{ $message }}</div>@enderror
        </div>
        <p style="font-size:12px; color:var(--ink-soft); margin-top:14px;">No account needed — check out as a guest. Your order confirmation goes to the email/phone above.</p>
      </div>

      <div class="checkout-section">
        <h2><span class="step-num">2</span> Shipping Address</h2>
        <div class="form-grid">
          <div class="form-row span-2">
            <label>Full Name</label>
            <input type="text" name="name" value="{{ old('name') }}" placeholder="Recipient's full name" required>
            @error('name')<div style="color:var(--rust-deep); font-size:12px; margin-top:6px;">{{ $message }}</div>@enderror
          </div>
          <div class="form-row span-2">
            <label>Street Address</label>
            <textarea name="address_line1" placeholder="Street number and name" required>{{ old('address_line1') }}</textarea>
            @error('address_line1')<div style="color:var(--rust-deep); font-size:12px; margin-top:6px;">{{ $message }}</div>@enderror
          </div>
          <div class="form-row">
            <label>City / Town</label>
            <input type="text" name="city" value="{{ old('city') }}" placeholder="e.g. Auckland" required>
            @error('city')<div style="color:var(--rust-deep); font-size:12px; margin-top:6px;">{{ $message }}</div>@enderror
          </div>
          <div class="form-row">
            <label>Region</label>
            <input type="text" name="region" value="{{ old('region') }}" placeholder="e.g. Auckland Region">
          </div>
          <div class="form-row">
            <label>Postcode</label>
            <input type="text" name="postcode" value="{{ old('postcode') }}" placeholder="e.g. 1010" required>
            @error('postcode')<div style="color:var(--rust-deep); font-size:12px; margin-top:6px;">{{ $message }}</div>@enderror
          </div>
          <div class="form-row">
            <label>Order Notes (optional)</label>
            <input type="text" name="notes" value="{{ old('notes') }}" placeholder="e.g. leave at front door">
          </div>
        </div>
      </div>

      <div class="checkout-section">
        <h2><span class="step-num">3</span> Shipping Method</h2>

        <label class="radio-card active" data-rate="{{ $urbanRate }}">
          <div class="radio-card-head">
            <input type="radio" name="area" value="urban" checked required>
            <div class="label">
              <div><strong>Urban Area</strong><br><span class="meta">Main cities &amp; towns</span></div>
              <span class="price-tag">${{ number_format($urbanRate, 2) }}</span>
            </div>
          </div>
        </label>

        <label class="radio-card" data-rate="{{ $ruralRate }}">
          <div class="radio-card-head">
            <input type="radio" name="area" value="rural">
            <div class="label">
              <div><strong>Rural Area</strong><br><span class="meta">Rural delivery address</span></div>
              <span class="price-tag">${{ number_format($ruralRate, 2) }}</span>
            </div>
          </div>
        </label>
      </div>

      <div class="checkout-section">
        <h2><span class="step-num">4</span> Payment Method</h2>
        <label class="radio-card active">
          <div class="radio-card-head">
            <input type="radio" name="payment" checked disabled>
            <div class="label"><strong>Manual Bank Transfer</strong></div>
          </div>
          <div class="radio-card-body" style="max-height:200px; border-top-color:var(--line);">
            <div class="radio-card-body-inner">
              Bank account details will be shown on the next screen once your order is placed. You'll be able to upload your proof of payment there too.
            </div>
          </div>
        </label>
      </div>

    </div>

    <aside class="order-summary">
      <h2>Order Summary</h2>

      @foreach ($items as $line)
      <div class="cart-line">
        <div class="ph">
          @if ($line['variant']?->image_path ?? $line['product']->image_path)
          <img src="{{ Storage::disk('public')->url($line['variant']->image_path ?? $line['product']->image_path) }}" alt="{{ $line['product']->name }}">
          @else
          <span>PHOTO</span>
          @endif
        </div>
        <div class="cart-line-info">
          <h5>{{ $line['product']->name }}</h5>
          @if ($line['variant'])
          <div class="meta">{{ $line['variant']->label }}</div>
          @endif
          <div class="meta">Qty {{ $line['quantity'] }}</div>
          <div class="price">${{ number_format($line['line_total'], 2) }}</div>
        </div>
      </div>
      @endforeach

      <div class="summary-totals">
        <div class="summary-row"><span>Subtotal</span><span id="sum-subtotal" data-value="{{ $subtotal }}">${{ number_format($subtotal, 2) }}</span></div>
        <div class="summary-row"><span>Shipping</span><span id="sum-shipping">${{ number_format($urbanRate, 2) }}</span></div>
        <div class="summary-row total"><span>Total</span><span id="sum-total">${{ number_format($subtotal + $urbanRate, 2) }}</span></div>
      </div>
      <span style="font-size:11px; color:var(--ink-soft);">{{ $currency }}</span>

      <button type="submit" class="btn btn--rust checkout-pay-btn">Place Order</button>
      <p class="checkout-note">By placing this order you agree to our Terms &amp; Conditions and Privacy Policy.</p>
    </aside>

  </form>
</div>

@endsection
