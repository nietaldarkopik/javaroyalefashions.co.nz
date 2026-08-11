<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\ShippingArea;
use App\Mail\InvoiceMail;
use App\Models\Order;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RuntimeException;

class CheckoutService
{
    public function __construct(
        private readonly CartService $cart,
        private readonly CustomerRepositoryInterface $customers,
        private readonly OrderRepositoryInterface $orders,
        private readonly SettingService $settings,
    ) {}

    /**
     * @param  array{name: string, email: string, phone: string}  $customerData
     * @param  array{address_line1: string, address_line2?: string, suburb?: string, city: string, region?: string, postcode: string, area: string}  $shippingData
     */
    public function checkout(array $customerData, array $shippingData, ?string $notes = null): Order
    {
        $items = $this->cart->items();

        if ($items->isEmpty()) {
            throw new RuntimeException('Cannot checkout with an empty cart.');
        }

        $area = ShippingArea::from($shippingData['area']);
        $subtotal = round((float) $items->sum('line_total'), 2);
        $shippingCost = $this->settings->shippingRateFor($area);
        $grandTotal = round($subtotal + $shippingCost, 2);
        $currency = $this->settings->current()->currency_code;

        $order = DB::transaction(function () use (
            $customerData, $shippingData, $notes, $items, $area, $subtotal, $shippingCost, $grandTotal, $currency
        ) {
            $customer = $this->customers->firstOrCreateByEmail($customerData);

            $order = $this->orders->create([
                'order_number' => $this->generateOrderNumber(),
                'customer_id' => $customer->id,
                'customer_name' => $customerData['name'],
                'customer_email' => $customerData['email'],
                'customer_phone' => $customerData['phone'],
                'shipping_address_line1' => $shippingData['address_line1'],
                'shipping_address_line2' => $shippingData['address_line2'] ?? null,
                'shipping_suburb' => $shippingData['suburb'] ?? null,
                'shipping_city' => $shippingData['city'],
                'shipping_region' => $shippingData['region'] ?? null,
                'shipping_postcode' => $shippingData['postcode'],
                'shipping_area' => $area,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'grand_total' => $grandTotal,
                'currency' => $currency,
                'status' => OrderStatus::PendingPayment,
                'customer_notes' => $notes,
            ]);

            foreach ($items as $line) {
                $order->items()->create([
                    'product_id' => $line['product']->id,
                    'product_variant_id' => $line['variant']?->id,
                    'product_name' => $line['product']->name,
                    'variant_label' => $line['variant']?->label,
                    'product_sku' => $line['variant']?->sku ?? $line['product']->sku,
                    'unit_price' => $line['unit_price'],
                    'quantity' => $line['quantity'],
                    'line_total' => $line['line_total'],
                ]);
            }

            return $order;
        });

        $this->sendInvoice($order);
        $this->cart->clear();

        return $order->fresh(['items', 'customer']);
    }

    public function sendInvoice(Order $order): void
    {
        #Mail::to($order->customer_email)->send(new InvoiceMail($order->fresh(['items'])));

        $order->update(['invoice_sent_at' => now()]);
    }

    private function generateOrderNumber(): string
    {
        do {
            $candidate = 'NZ'.now()->format('ymd').'-'.Str::upper(Str::random(5));
        } while ($this->orders->findByOrderNumber($candidate) !== null);

        return $candidate;
    }
}
