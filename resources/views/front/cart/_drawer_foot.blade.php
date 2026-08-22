<div class="cart-subtotal"><span>Subtotal</span><span>${{ number_format($miniCartSubtotal, 2) }}</span></div>
<p class="cart-note">Shipping is calculated at checkout.</p>
<a href="{{ $miniCartItems->isEmpty() ? route('cart.index') : route('checkout.index') }}" class="btn btn--rust" style="width:100%; justify-content:center;">
  {{ $miniCartItems->isEmpty() ? 'View Cart' : 'Checkout' }}
</a>
