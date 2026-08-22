@forelse ($miniCartItems as $line)
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
    <form action="{{ route('cart.remove', $line['line_key']) }}" method="POST">
      @csrf @method('DELETE')
      <button type="submit" class="cart-remove">Remove</button>
    </form>
  </div>
</div>
@empty
<div class="cart-empty">
  <div class="icon"><i class="fa-solid fa-cart-shopping"></i></div>
  <p>Your cart is empty.</p>
</div>
@endforelse
