<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Checkout — '.$siteSetting->site_name)</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
@if ($siteSetting->favicon_path)
<link rel="icon" href="{{ Storage::disk('public')->url($siteSetting->favicon_path) }}">
@endif
</head>
<body>

<header class="checkout-header">
  <div class="wrap">
    <x-site-logo :setting="$siteSetting" />
    <div class="checkout-steps">
      <span @class(['current' => ($step ?? 1) === 1])>Cart</span><span class="sep">—</span>
      <span @class(['current' => ($step ?? 1) === 2])>Checkout</span><span class="sep">—</span>
      <span @class(['current' => ($step ?? 1) === 3])>Done</span>
    </div>
    <div class="checkout-secure"><span class="lock"><i class="fa-solid fa-lock"></i></span> Secure Checkout</div>
  </div>
</header>

@if (session('status'))
<div class="wrap" style="padding-top:16px;">
  <div class="chip" style="background:var(--ink); color:var(--paper); cursor:default;">{{ session('status') }}</div>
</div>
@endif

@yield('content')

<footer class="site" style="padding:32px 0;">
  <div class="wrap footer-bottom" style="border:none; padding:0;">
    <span>&copy; {{ date('Y') }} {{ $siteSetting->site_name }}. All rights reserved.</span>
    <span>Manual bank transfer only &middot; Guest checkout, no account needed.</span>
  </div>
</footer>

<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
