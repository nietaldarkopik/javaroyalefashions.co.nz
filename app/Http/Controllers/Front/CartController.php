<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\AddToCartRequest;
use App\Http\Requests\Front\UpdateCartRequest;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
    ) {}

    public function index(): View
    {
        return view('front.cart.index', [
            'items' => $this->cart->items(),
            'subtotal' => $this->cart->subtotal(),
        ]);
    }

    public function add(AddToCartRequest $request): RedirectResponse|JsonResponse
    {
        $this->cart->add(
            $request->integer('product_id'),
            $request->integer('quantity', 1),
            $request->filled('variant_id') ? $request->integer('variant_id') : null,
        );

        if ($request->wantsJson()) {
            return response()->json($this->drawerPayload());
        }

        return back()->with('status', 'Added to cart.');
    }

    public function update(UpdateCartRequest $request): RedirectResponse
    {
        $this->cart->update($request->string('line_key')->toString(), $request->integer('quantity'));

        return back()->with('status', 'Cart updated.');
    }

    public function remove(string $lineKey): RedirectResponse
    {
        $this->cart->remove($lineKey);

        return back()->with('status', 'Item removed from cart.');
    }

    private function drawerPayload(): array
    {
        $items = $this->cart->items();
        $subtotal = $this->cart->subtotal();

        return [
            'count' => $this->cart->totalQuantity(),
            'bodyHtml' => view('front.cart._drawer_body', ['miniCartItems' => $items])->render(),
            'footHtml' => view('front.cart._drawer_foot', [
                'miniCartItems' => $items,
                'miniCartSubtotal' => $subtotal,
            ])->render(),
        ];
    }
}
