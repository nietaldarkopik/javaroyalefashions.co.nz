<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\CheckoutRequest;
use App\Http\Requests\Front\UploadPaymentProofRequest;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\OrderService;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly CheckoutService $checkout,
        private readonly OrderService $orderService,
        private readonly OrderRepositoryInterface $orders,
        private readonly SettingService $settings,
    ) {}

    public function index(): View|RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Your cart is empty.');
        }

        $setting = $this->settings->current();

        return view('front.checkout.index', [
            'items' => $this->cart->items(),
            'subtotal' => $this->cart->subtotal(),
            'urbanRate' => (float) $setting->shipping_urban_rate,
            'ruralRate' => (float) $setting->shipping_rural_rate,
            'currency' => $setting->currency_code,
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Your cart is empty.');
        }

        $data = $request->validated();

        $order = $this->checkout->checkout(
            customerData: [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ],
            shippingData: [
                'address_line1' => $data['address_line1'],
                'address_line2' => $data['address_line2'] ?? null,
                'suburb' => $data['suburb'] ?? null,
                'city' => $data['city'],
                'region' => $data['region'] ?? null,
                'postcode' => $data['postcode'],
                'area' => $data['area'],
            ],
            notes: $data['notes'] ?? null,
        );

        return redirect()->route('checkout.success', $order->order_number);
    }

    public function success(string $orderNumber): View
    {
        $order = $this->orders->findByOrderNumber($orderNumber) ?? abort(404);
        $setting = $this->settings->current();

        return view('front.checkout.success', [
            'order' => $order,
            'setting' => $setting,
        ]);
    }

    public function uploadProof(UploadPaymentProofRequest $request, string $orderNumber): RedirectResponse
    {
        $order = $this->orders->findByOrderNumber($orderNumber) ?? abort(404);

        $this->orderService->attachPaymentProof($order, $request->file('proof'));

        return redirect()->route('checkout.success', $order->order_number)
            ->with('status', 'Payment proof uploaded — we will verify it shortly.');
    }
}
