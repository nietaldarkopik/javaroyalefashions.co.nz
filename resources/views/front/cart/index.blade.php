@extends('layouts.front')

@section('title', 'Your Cart — '.$siteSetting->site_name)

@section('content')

<div class="wrap breadcrumb">
  <a href="{{ route('home') }}">Home</a> / <span>Your Cart</span>
</div>

<div class="page-header reveal">
  <span class="eyebrow">Step 1</span>
  <h1>Your Cart</h1>
</div>

<div class="wrap" style="padding-bottom:80px;">
  @if ($items->isEmpty())
  <div class="page-header" style="padding:0 0 40px;">
    <p>Your cart is empty. <a href="{{ route('products.index') }}" style="text-decoration:underline; color:var(--ink);">Browse products <i class="fa-solid fa-arrow-right"></i></a></p>
  </div>
  @else
  <div class="checkout-layout">
    <div class="checkout-main">
      <div class="checkout-section">
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
            <h5><a href="{{ route('products.show', $line['product']->slug) }}" style="color:inherit;">{{ $line['product']->name }}</a></h5>
            @if ($line['variant'])
            <div class="meta">{{ $line['variant']->label }}</div>
            @endif
            <div class="meta">${{ number_format($line['unit_price'], 2) }} each</div>
            <div class="price">${{ number_format($line['line_total'], 2) }}</div>

            <form action="{{ route('cart.update') }}" method="POST" style="display:flex; align-items:center; gap:10px; margin-top:8px;">
              @csrf
              <input type="hidden" name="line_key" value="{{ $line['line_key'] }}">
              <input type="number" name="quantity" value="{{ $line['quantity'] }}" min="1" max="{{ $line['variant']->stock_quantity ?? $line['product']->stock_quantity }}"
                     style="width:56px; padding:6px; border:1px solid var(--line);">
              <button type="submit" class="chip">Update</button>
            </form>
            <form action="{{ route('cart.remove', $line['line_key']) }}" method="POST" style="margin-top:6px;">
              @csrf @method('DELETE')
              <button type="submit" class="cart-remove">Remove</button>
            </form>
          </div>
        </div>
        @endforeach
      </div>
    </div>

    <aside class="order-summary">
      <h2>Order Summary</h2>
      <div class="summary-totals" style="border-top:none; padding-top:0; margin-top:0;">
        <div class="summary-row"><span>Subtotal</span><span>${{ number_format($subtotal, 2) }}</span></div>
      </div>
      <p class="checkout-note" style="text-align:left; margin-top:10px;">Shipping (flat rate, urban/rural) is calculated at checkout.</p>
      <a href="{{ route('checkout.index') }}" class="btn btn--rust checkout-pay-btn">Proceed to Checkout</a>
    </aside>
  </div>
  @endif
</div>

@endsection
