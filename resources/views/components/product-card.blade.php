@props(['product'])

<div class="product-card">
    <div class="ph">
        @if ($product->image_path)
        <img src="{{ Storage::disk('public')->url($product->image_path) }}" alt="{{ $product->name }}">
        @else
        <span>PHOTO 3:4</span>
        @endif

        @if ($product->is_on_sale && !$product->hasVariants())
        <span class="sale-badge">Sale</span>
        @endif

        <button class="wishlist-btn d-none" type="button" aria-label="Add to wishlist"><i class="fa-regular fa-heart"></i></button>

        @if ($product->hasVariants())
        <a href="{{ route('products.show', $product->slug) }}" class="quick-add">Choose Options</a>
        @elseif ($product->is_in_stock)
        <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="quantity" value="1">
            <button type="submit" class="quick-add">Quick Add</button>
        </form>
        @else
        <span class="quick-add quick-add--disabled">Out of Stock</span>
        @endif
    </div>

    <div class="product-info">
        <a href="{{ route('products.show', $product->slug) }}"><h4>{{ $product->name }}</h4></a>
        <div class="price">
            @if ($product->hasVariants())
            From ${{ number_format($product->price_range['min'], 2) }}
            @elseif ($product->is_on_sale)
            <span class="was">${{ number_format($product->price, 2) }}</span>${{ number_format($product->sale_price, 2) }}
            @else
            ${{ number_format($product->price, 2) }}
            @endif
        </div>
    </div>
</div>
