@extends('layouts.front')

@section('title', $siteSetting->site_name.' — '.$siteSetting->site_tagline)

@section('content')

<section class="hero">
  @forelse ($heroSlides as $slide)
  <div class="hero-slide @if ($loop->first) active @endif">
    @if ($slide->image_path)
    <div class="ph"><img src="{{ Storage::disk('public')->url($slide->image_path) }}" alt="{{ $slide->heading }}"></div>
    @else
    <div class="ph"><span>HERO PHOTO — 1600×900</span></div>
    @endif
    <div class="hero-content">
      @if ($slide->eyebrow)
      <span class="eyebrow">{{ $slide->eyebrow }}</span>
      @endif
      <h1>{{ $slide->heading }}</h1>
      @if ($slide->subheading)
      <p>{{ $slide->subheading }}</p>
      @endif
      @if ($slide->button_text && $slide->button_url)
      <a href="{{ $slide->button_url }}" class="btn">{{ $slide->button_text }}</a>
      @endif
    </div>
  </div>
  @empty
  <div class="hero-slide active">
    <div class="ph"><span>HERO PHOTO — 1600×900</span></div>
    <div class="hero-content">
      <span class="eyebrow">{{ $siteSetting->site_tagline }}</span>
      <h1>Quality goods,<br>shipped across NZ.</h1>
      <p>Browse the catalog, add what you need to your cart, and check out as a guest — no account required.</p>
      <a href="{{ route('products.index') }}" class="btn">Shop All Products</a>
    </div>
  </div>
  @endforelse
  <div class="hero-dots"></div>
</section>

<div class="stitch"></div>

@if ($categories->isNotEmpty())
<section class="section reveal" id="kategori">
  <div class="wrap">
    <div class="section-head">
      <div>
        <span class="eyebrow">Shop By</span>
        <h2>Category</h2>
      </div>
      <div class="carousel-arrows">
        <button class="carousel-arrow" data-carousel-prev aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>
        <button class="carousel-arrow" data-carousel-next aria-label="Next"><i class="fa-solid fa-chevron-right"></i></button>
      </div>
    </div>
    <div class="carousel-wrap">
      <div class="cat-grid carousel-track">
        @foreach ($categories as $category)
        <a class="cat-card" href="{{ route('categories.show', $category->slug) }}" style="width:170px;">
          <div class="ph">
            @if ($category->image_path)
            <img src="{{ Storage::disk('public')->url($category->image_path) }}" alt="{{ $category->name }}">
            @else
            <span>{{ strtoupper($category->name) }}</span>
            @endif
          </div>
          <h3>{{ $category->name }}</h3>
        </a>
        @endforeach
      </div>
    </div>
  </div>
</section>

<div class="stitch stitch--rust"></div>
@endif

@if ($featuredProducts->isNotEmpty())
<section class="section reveal" id="featured">
  <div class="wrap">
    <div class="section-head">
      <div>
        <span class="eyebrow">Featured</span>
        <h2>Popular Right Now</h2>
      </div>
      <a href="{{ route('products.index') }}" class="view-all">View All <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="product-grid">
      @foreach ($featuredProducts as $product)
      <x-product-card :product="$product" />
      @endforeach
    </div>
  </div>
</section>
@endif

@forelse ($contentBanners as $banner)
<x-content-banner :banner="$banner" />
@empty
<section class="video-banner reveal">
  <div class="ph"><span>BANNER PHOTO — 1920×1080</span></div>
  <div class="video-banner-content">
    <span class="eyebrow">Our Story</span>
    <h2>Quality You Can Trust</h2>
    <p>Learn more about who we are, how we work, and why customers keep coming back.</p>
    <a href="{{ route('pages.show', 'about') }}" class="btn">Read Our Story</a>
  </div>
</section>
@endforelse

<section class="badges reveal">
  <div class="wrap badge-grid">
    <div class="badge-item"><div class="icon"><i class="fa-solid fa-star"></i></div><p>Carefully selected, quality products</p></div>
    <div class="badge-item"><div class="icon"><i class="fa-solid fa-truck"></i></div><p>Flat-rate shipping, urban &amp; rural NZ</p></div>
    <div class="badge-item"><div class="icon"><i class="fa-solid fa-shield-halved"></i></div><p>Secure checkout, no account needed</p></div>
    <div class="badge-item"><div class="icon"><i class="fa-solid fa-building-columns"></i></div><p>Simple manual bank transfer payment</p></div>
  </div>
</section>

@endsection
