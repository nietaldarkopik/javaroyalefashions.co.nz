@extends('layouts.front')

@section('title', (isset($activeCategory) ? $activeCategory->name : 'All Products').' — '.$siteSetting->site_name)

@section('content')

<div class="wrap breadcrumb">
  <a href="{{ route('home') }}">Home</a> / <span>{{ $activeCategory->name ?? 'All Products' }}</span>
</div>

<div class="page-header reveal">
  <span class="eyebrow">{{ isset($activeCategory) ? 'Category' : 'Browse' }}</span>
  <h1>{{ $activeCategory->name ?? 'All Products' }}</h1>
  @if (isset($activeCategory) && $activeCategory->description)
  <p>{{ $activeCategory->description }}</p>
  @endif
</div>

<div class="wrap">
  <div class="filter-bar reveal">
    <div class="filter-left">
      <a class="chip @if (!isset($activeCategory)) active @endif"
         href="{{ route('products.index', array_filter(['search' => $filters['search'] ?? null, 'sort' => $filters['sort'] ?? null])) }}">
        All
      </a>
      @foreach ($categories as $category)
      <a class="chip @if (isset($activeCategory) && $activeCategory->id === $category->id) active @endif"
         href="{{ route('categories.show', array_merge(['slug' => $category->slug], array_filter(['search' => $filters['search'] ?? null, 'sort' => $filters['sort'] ?? null]))) }}">
        {{ $category->name }}
      </a>
      @endforeach
    </div>

    <form method="GET" style="display:flex; gap:10px;">
      @if (!empty($filters['search']))<input type="hidden" name="search" value="{{ $filters['search'] }}">@endif
      <select class="sort-select" name="sort" onchange="this.form.submit()">
        <option value="newest" @selected(($filters['sort'] ?? 'newest') === 'newest')>Sort: Newest</option>
        <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Price: Low to High</option>
        <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Price: High to Low</option>
        <option value="name_asc" @selected(($filters['sort'] ?? '') === 'name_asc')>Name: A–Z</option>
      </select>
    </form>
  </div>

  <form method="GET" style="max-width:360px; margin-bottom:32px; display:flex; gap:10px;">
    @if (!empty($filters['sort']))<input type="hidden" name="sort" value="{{ $filters['sort'] }}">@endif
    <input type="text" name="search" class="sort-select" style="flex:1;" placeholder="Search products…" value="{{ $filters['search'] ?? '' }}">
    <button type="submit" class="btn btn--outline">Search</button>
  </form>

  @if ($products->isEmpty())
  <p style="color:var(--ink-soft); padding:40px 0; text-align:center;">No products match your filters.</p>
  @else
  <div class="product-grid reveal">
    @foreach ($products as $product)
    <x-product-card :product="$product" />
    @endforeach
  </div>

  {{ $products->onEachSide(1)->links('components.pagination') }}
  @endif
</div>

@foreach ($contentBanners as $banner)
<x-content-banner :banner="$banner" />
@endforeach

@endsection
