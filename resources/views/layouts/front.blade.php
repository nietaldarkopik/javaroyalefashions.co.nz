<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', $siteSetting->site_name.' — '.$siteSetting->site_tagline)</title>
<meta name="description" content="@yield('meta_description', $siteSetting->meta_description)">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
@if ($siteSetting->favicon_path)
<link rel="icon" href="{{ Storage::disk('public')->url($siteSetting->favicon_path) }}">
@endif
</head>
<body>

<div class="promo-popup-backdrop"></div>
<div class="promo-popup">
  <button class="promo-popup-close" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
  <div class="ph"><span>PROMO PHOTO — 800×500</span></div>
  <div class="promo-popup-body">
    <span class="eyebrow">New Here?</span>
    <h3>Get 15% Off<br>Your First Order</h3>
    <p>Sign up now for an exclusive discount code on your first purchase.</p>
    <form class="promo-popup-form">
      <input type="email" placeholder="Your email address" required>
      <button type="submit">Claim</button>
    </form>
    <a href="#" class="promo-popup-skip" data-promo-skip>Maybe later</a>
  </div>
</div>

<div class="announce">
  FLAT RATE SHIPPING &nbsp;·&nbsp; URBAN ${{ number_format($siteSetting->shipping_urban_rate, 2) }} &nbsp;·&nbsp; RURAL ${{ number_format($siteSetting->shipping_rural_rate, 2) }}
</div>

<header class="site">
  <div class="wrap header-inner px-5">
    <button class="menu-toggle" aria-label="Open menu"><i class="fa-solid fa-bars"></i></button>
    <x-site-logo :setting="$siteSetting" />
    <nav class="main-nav">
      <a href="{{ route('home') }}">Home</a>
      <a href="{{ route('products.index') }}">All Products</a>
      <div class="nav-item">
        <button class="nav-link">Categories <span class="caret"><i class="fa-solid fa-chevron-down"></i></span></button>
        <div class="dropdown">
          @forelse ($navCategories as $navCategory)
          <a href="{{ route('categories.show', $navCategory->slug) }}">{{ $navCategory->name }}</a>
          @empty
          <a href="{{ route('products.index') }}">All Products</a>
          @endforelse
        </div>
      </div>
      <a href="{{ route('pages.show', 'about') }}">About</a>
      <a href="{{ route('pages.show', 'contact') }}">Contact</a>
    </nav>
    <div class="header-icons">
      <button data-search-open aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
      <button class="icon-btn" data-cart-open aria-label="Cart"><i class="fa-solid fa-cart-shopping"></i><span class="cart-count">{{ $cartCount }}</span></button>
    </div>
  </div>

  <div class="search-overlay">
    <div class="search-inner">
      <form action="{{ route('products.index') }}" method="GET" style="flex:1; display:flex;">
        <input type="text" name="search" placeholder="Search products…" value="{{ request('search') }}">
      </form>
      <button class="search-close" aria-label="Close search"><i class="fa-solid fa-xmark"></i></button>
    </div>
  </div>
</header>

<div class="mobile-nav-backdrop"></div>
<div class="mobile-nav">
  <div class="mobile-nav-head">
    <x-site-logo :setting="$siteSetting" />
    <button class="mobile-nav-close" aria-label="Close menu"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <a href="{{ route('home') }}">Home</a>
  <a href="{{ route('products.index') }}">All Products</a>
  <div class="m-nav-item">
    <button class="m-nav-link">Categories <span class="caret"><i class="fa-solid fa-chevron-down"></i></span></button>
    <div class="m-submenu">
      @foreach ($navCategories as $navCategory)
      <a href="{{ route('categories.show', $navCategory->slug) }}">{{ $navCategory->name }}</a>
      @endforeach
    </div>
  </div>
  <a href="{{ route('pages.show', 'about') }}">About</a>
  <a href="{{ route('pages.show', 'contact') }}">Contact</a>
</div>

<div class="drawer-backdrop"></div>
<div class="cart-drawer">
  <div class="cart-head">
    <h3>Your Cart</h3>
    <button class="cart-close" aria-label="Close cart"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <div class="cart-body">
    @include('front.cart._drawer_body')
  </div>
  <div class="cart-foot">
    @include('front.cart._drawer_foot')
  </div>
</div>

@if (session('status'))
<div class="wrap" style="padding-top:16px;">
  <div class="chip" style="background:var(--ink); color:var(--paper); cursor:default;">{{ session('status') }}</div>
</div>
@endif

@yield('content')

<section class="newsletter">
  <div class="wrap">
    <span class="eyebrow">Stay Connected</span>
    <h2>Be the first to know about new arrivals</h2>
    <p>Sign up for exclusive offers, early access, and the stories behind what we sell.</p>
    <form class="news-form">
      <input type="email" placeholder="Your email address" required>
      <button type="submit">Subscribe</button>
    </form>
  </div>
</section>

<footer class="site">
  <div class="wrap">
    <div class="footer-grid">
      <div>
        <x-site-logo :setting="$siteSetting" class="footer-logo" />
        <p>{{ $siteSetting->site_tagline }}</p>
      </div>
      <div>
        <h5>Shop</h5>
        <ul>
          <li><a href="{{ route('products.index') }}">All Products</a></li>
          @foreach ($navCategories->take(4) as $navCategory)
          <li><a href="{{ route('categories.show', $navCategory->slug) }}">{{ $navCategory->name }}</a></li>
          @endforeach
        </ul>
      </div>
      <div>
        <h5>Help</h5>
        <ul>
          <li><a href="{{ route('pages.show', 'about') }}">About Us</a></li>
          <li><a href="{{ route('pages.show', 'contact') }}">Contact Us</a></li>
          <li><a href="{{ route('pages.show', 'privacy-policy') }}">Privacy Policy</a></li>
          <li><a href="{{ route('pages.show', 'terms') }}">Terms &amp; Conditions</a></li>
        </ul>
      </div>
      <div>
        <h5>Get in Touch</h5>
        <ul>
          @if ($siteSetting->address)<li>{{ $siteSetting->address }}</li>@endif
          <li><a href="mailto:{{ $siteSetting->contact_email }}">{{ $siteSetting->contact_email }}</a></li>
          @if ($siteSetting->contact_phone)<li>{{ $siteSetting->contact_phone }}</li>@endif
          @if ($siteSetting->contact_whatsapp)
          <li><a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siteSetting->contact_whatsapp) }}" target="_blank">WhatsApp Us</a></li>
          @endif
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>&copy; {{ date('Y') }} {{ $siteSetting->site_name }}. All rights reserved.</span>
      <span>Manual bank transfer only &middot; Guest checkout, no account needed.</span>
    </div>
  </div>
</footer>

<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
