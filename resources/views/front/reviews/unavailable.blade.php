@extends('layouts.front')

@section('title', 'Review Link Unavailable — '.$siteSetting->site_name)

@section('content')

<div class="wrap review-order">
  <div class="review-order-head" style="text-align:center; margin:0 auto; padding-bottom:80px;">
    <span class="eyebrow">Product Reviews</span>
    @if ($expired)
    <h1>This review link has expired</h1>
    <p>Review links are only valid for a limited time. If you'd still like to share your thoughts, please get in touch and we'll send you a new one.</p>
    @else
    <h1>This review link is no longer available</h1>
    <p>Reviews can't be submitted for this order right now. If you think this is a mistake, please get in touch.</p>
    @endif
    <div style="margin-top:28px; display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
      <a href="{{ route('products.index') }}" class="btn btn--outline">Continue Shopping</a>
      <a href="{{ route('pages.show', 'contact') }}" class="btn">Contact Us</a>
    </div>
  </div>
</div>

@endsection
