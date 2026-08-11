@extends('layouts.front')

@section('title', ($product->meta_title ?: $product->name).' — '.$siteSetting->site_name)
@section('meta_description', $product->meta_description ?: $product->short_description)

@section('content')

<div class="wrap breadcrumb">
  <a href="{{ route('home') }}">Home</a> /
  <a href="{{ route('products.index') }}">Products</a> /
  @if ($product->category)
  <a href="{{ route('categories.show', $product->category->slug) }}">{{ $product->category->name }}</a> /
  @endif
  <span>{{ $product->name }}</span>
</div>

<div class="wrap">
  <div class="product-detail">

    <div class="gallery">
      <div class="gallery-main">
        <div class="ph">
          @if ($product->image_path)
          <img id="main-product-image" src="{{ Storage::disk('public')->url($product->image_path) }}" alt="{{ $product->name }}">
          @else
          <span>MAIN PHOTO</span>
          @endif
        </div>
      </div>
      @if ($product->images->isNotEmpty())
      <div class="gallery-thumbs">
        <div class="ph active thumb" data-full-image="{{ $product->image_path ? Storage::disk('public')->url($product->image_path) : '' }}">
          <span>1</span>
        </div>
        @foreach ($product->images as $index => $image)
        <div class="ph thumb" data-full-image="{{ Storage::disk('public')->url($image->image_path) }}">
          <span>{{ $index + 2 }}</span>
        </div>
        @endforeach
      </div>
      @endif
    </div>

    <div class="pd-info">
      @if ($product->category)
      <span class="eyebrow">{{ $product->category->name }}</span>
      @endif
      <h1>{{ $product->name }}</h1>
      <div class="pd-price">
        @if ($product->is_on_sale)
        <span class="was">${{ number_format($product->price, 2) }}</span> ${{ number_format($product->sale_price, 2) }}
        @else
        ${{ number_format($product->price, 2) }}
        @endif
        <span style="font-size:12px; color:var(--ink-soft);">{{ $siteSetting->currency_code }}</span>
      </div>
      @if ($product->short_description)
      <p class="pd-desc">{{ $product->short_description }}</p>
      @endif

      @php
        $variants = $product->variants;
        $sizes = $variants->pluck('size')->filter()->unique()->values();
        $colors = $variants->pluck('color')->filter()->unique()->values();
        $attributeName = $variants->pluck('attribute_name')->filter()->first();
        $attributeValues = $attributeName ? $variants->pluck('attribute_value')->filter()->unique()->values() : collect();
      @endphp

      <form action="{{ route('cart.add') }}" method="POST" id="add-to-cart-form">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="variant_id" id="selected-variant-id" value="">

        @if ($product->hasVariants())
        <script type="application/json" id="variant-data">
          {!! $variants->map(fn ($v) => [
              'id' => $v->id,
              'size' => $v->size,
              'color' => $v->color,
              'attribute_value' => $v->attribute_value,
              'price' => (float) $v->effective_price,
              'stock' => $v->stock_quantity,
              'image' => $v->image_path ? Storage::disk('public')->url($v->image_path) : null,
          ])->values()->toJson() !!}
        </script>

        @if ($sizes->isNotEmpty())
        <div class="option-group">
          <div class="option-label"><span>Size</span><span class="selected" id="selected-size">—</span></div>
          <div class="swatches" data-option="size">
            @foreach ($sizes as $size)
            <div class="size-opt" data-value="{{ $size }}">{{ $size }}</div>
            @endforeach
          </div>
        </div>
        @endif

        @if ($colors->isNotEmpty())
        <div class="option-group">
          <div class="option-label"><span>Color</span><span class="selected" id="selected-color">—</span></div>
          <div class="swatches" data-option="color">
            @foreach ($colors as $color)
            <div class="size-opt" data-value="{{ $color }}">{{ $color }}</div>
            @endforeach
          </div>
        </div>
        @endif

        @if ($attributeValues->isNotEmpty())
        <div class="option-group">
          <div class="option-label"><span>{{ $attributeName }}</span><span class="selected" id="selected-attribute">—</span></div>
          <div class="swatches" data-option="attribute">
            @foreach ($attributeValues as $value)
            <div class="size-opt" data-value="{{ $value }}">{{ $value }}</div>
            @endforeach
          </div>
        </div>
        @endif

        <p id="variant-status" style="font-size:13px; color:var(--ink-soft); margin-bottom:16px;">
          Select options above to see price and availability.
        </p>
        @endif

        <div class="qty-row">
          <span class="option-label" style="margin:0;">Quantity</span>
          <div class="qty-stepper">
            <button type="button" class="qty-minus"><i class="fa-solid fa-minus"></i></button>
            <input type="number" name="quantity" id="add-to-cart-qty" value="1" min="1" max="{{ $product->stock_quantity }}"
                   style="width:40px; text-align:center; border:none; font-family:var(--font-mono); font-weight:600;">
            <button type="button" class="qty-plus"><i class="fa-solid fa-plus"></i></button>
          </div>
        </div>

        <div class="pd-actions">
          <button type="submit" class="btn btn--rust" id="add-to-cart-btn" {{ $product->hasVariants() || !$product->is_in_stock ? 'disabled' : '' }} style="{{ $product->hasVariants() || !$product->is_in_stock ? 'opacity:0.5;' : '' }}">
            {{ $product->is_in_stock ? 'Add to Cart' : 'Out of Stock' }}
          </button>
          <button type="button" class="wishlist-btn d-none btn btn--outline"><i class="fa-regular fa-heart"></i></button>
        </div>
      </form>

      @unless ($product->hasVariants())
      <p style="font-size:12px; color:var(--sage); margin-top:-14px; margin-bottom:22px;">
        @if ($product->is_in_stock)
        <i class="fa-solid fa-circle-check"></i> In stock — {{ $product->stock_quantity }} available
        @else
        <i class="fa-solid fa-circle-xmark"></i> Out of stock
        @endif
      </p>
      @endunless

      <div class="pd-meta">
        <span><i class="fa-solid fa-truck"></i> Flat-rate NZ shipping</span>
        <span><i class="fa-solid fa-building-columns"></i> Pay by bank transfer</span>
      </div>

      <div class="accordion" style="margin-top:32px;">
        @if ($product->description)
        <div class="acc-item open">
          <button class="acc-head">Description <span class="plus">+</span></button>
          <div class="acc-body"><p>{{ $product->description }}</p></div>
        </div>
        @endif
        <div class="acc-item @if (!$product->description) open @endif">
          <button class="acc-head">Shipping &amp; Payment <span class="plus">+</span></button>
          <div class="acc-body"><p>Orders ship at a flat rate of ${{ number_format($siteSetting->shipping_urban_rate, 2) }} (urban) or ${{ number_format($siteSetting->shipping_rural_rate, 2) }} (rural). Payment is by manual bank transfer — account details are shown after checkout.</p></div>
        </div>
      </div>
    </div>

  </div>
</div>

<div class="stitch"></div>

@if ($relatedProducts->isNotEmpty())
<section class="section reveal" style="padding-top:56px;">
  <div class="wrap">
    <div class="section-head">
      <div>
        <span class="eyebrow">You May Also Like</span>
        <h2>Related Products</h2>
      </div>
    </div>
    <div class="product-grid">
      @foreach ($relatedProducts as $related)
      <x-product-card :product="$related" />
      @endforeach
    </div>
  </div>
</section>
@endif

@if ($product->hasVariants())
@push('scripts')
<script>
(function () {
    const dataEl = document.getElementById('variant-data');
    if (!dataEl) return;

    const variants = JSON.parse(dataEl.textContent);
    const selected = { size: null, color: null, attribute: null };

    const variantIdInput = document.getElementById('selected-variant-id');
    const statusEl = document.getElementById('variant-status');
    const addBtn = document.getElementById('add-to-cart-btn');
    const qtyInput = document.getElementById('add-to-cart-qty');
    const mainImage = document.getElementById('main-product-image');

    function currency(n) {
        return '$' + n.toFixed(2);
    }

    function findMatch() {
        const needed = ['size', 'color', 'attribute'].filter((key) =>
            variants.some((v) => key === 'size' ? v.size : key === 'color' ? v.color : v.attribute_value)
        );

        return variants.find((v) => needed.every((key) => {
            const value = key === 'size' ? v.size : key === 'color' ? v.color : v.attribute_value;
            return value === selected[key];
        })) || null;
    }

    function refresh() {
        const match = findMatch();

        if (!match) {
            statusEl.textContent = 'Select options above to see price and availability.';
            statusEl.style.color = 'var(--ink-soft)';
            variantIdInput.value = '';
            addBtn.disabled = true;
            addBtn.style.opacity = '0.5';
            return;
        }

        variantIdInput.value = match.id;

        if (match.stock > 0) {
            statusEl.innerHTML = currency(match.price) + ' — <i class="fa-solid fa-circle-check"></i> In stock (' + match.stock + ' available)';
            statusEl.style.color = 'var(--sage)';
            addBtn.disabled = false;
            addBtn.style.opacity = '1';
            qtyInput.max = match.stock;
            if (parseInt(qtyInput.value, 10) > match.stock) qtyInput.value = match.stock;
        } else {
            statusEl.innerHTML = currency(match.price) + ' — <i class="fa-solid fa-circle-xmark"></i> Out of stock for this option';
            statusEl.style.color = 'var(--rust)';
            addBtn.disabled = true;
            addBtn.style.opacity = '0.5';
        }

        if (match.image && mainImage) {
            mainImage.src = match.image;
        }
    }

    document.querySelectorAll('.swatches[data-option]').forEach((group) => {
        const key = group.dataset.option;
        const labelEl = document.getElementById('selected-' + key);

        group.querySelectorAll('[data-value]').forEach((opt) => {
            opt.addEventListener('click', () => {
                group.querySelectorAll('[data-value]').forEach((o) => o.classList.remove('active'));
                opt.classList.add('active');
                selected[key] = opt.dataset.value;
                if (labelEl) labelEl.textContent = opt.dataset.value;
                refresh();
            });
        });
    });

    refresh();
})();
</script>
@endpush
@endif

@endsection
