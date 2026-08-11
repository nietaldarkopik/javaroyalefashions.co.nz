<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Session-backed shopping cart. There's no database table for this on
 * purpose: checkout is guest-only (no login), so there is no account to
 * persist a cart against between visits. If customer accounts are added
 * later, a `carts`/`cart_items` pair can be introduced then.
 *
 * Each line is keyed by a string ("p12" for a plain product, "v34" for a
 * specific variant) rather than the product id alone, so the same product
 * in two different variants (e.g. Red/M and Blue/L) sits as two separate
 * cart lines instead of colliding.
 */
class CartService
{
    private const SESSION_KEY = 'cart';

    public function __construct(
        private readonly ProductRepositoryInterface $products,
    ) {}

    public function add(int $productId, int $quantity = 1, ?int $variantId = null): void
    {
        $key = $this->lineKey($productId, $variantId);
        $cart = $this->raw();

        $cart[$key] = [
            'product_id' => $productId,
            'variant_id' => $variantId,
            'quantity' => ($cart[$key]['quantity'] ?? 0) + max(1, $quantity),
        ];

        $this->put($cart);
    }

    public function update(string $lineKey, int $quantity): void
    {
        $cart = $this->raw();

        if (! isset($cart[$lineKey])) {
            return;
        }

        if ($quantity < 1) {
            unset($cart[$lineKey]);
        } else {
            $cart[$lineKey]['quantity'] = $quantity;
        }

        $this->put($cart);
    }

    public function remove(string $lineKey): void
    {
        $cart = $this->raw();
        unset($cart[$lineKey]);
        $this->put($cart);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function isEmpty(): bool
    {
        return empty($this->raw());
    }

    /**
     * Resolved cart lines, silently dropping any product/variant that has
     * since been deactivated or deleted rather than erroring the whole
     * cart out.
     *
     * @return Collection<int, array{line_key: string, product: Product, variant: ?ProductVariant, quantity: int, unit_price: float, line_total: float}>
     */
    public function items(): Collection
    {
        return collect($this->raw())
            ->map(function (array $line, string $key) {
                $product = $this->products->findById($line['product_id']);

                if (! $product || ! $product->is_active) {
                    return null;
                }

                $variant = null;
                if (! empty($line['variant_id'])) {
                    $variant = ProductVariant::query()->active()->find($line['variant_id']);

                    if (! $variant) {
                        return null;
                    }
                }

                $unitPrice = $variant ? $variant->effective_price : (float) $product->current_price;
                $availableStock = $variant ? $variant->stock_quantity : $product->stock_quantity;
                $quantity = min($line['quantity'], max($availableStock, 1));

                return [
                    'line_key' => $key,
                    'product' => $product,
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => round($unitPrice * $quantity, 2),
                ];
            })
            ->filter()
            ->values();
    }

    public function totalQuantity(): int
    {
        return (int) collect($this->raw())->sum('quantity');
    }

    public function subtotal(): float
    {
        return round((float) $this->items()->sum('line_total'), 2);
    }

    private function lineKey(int $productId, ?int $variantId): string
    {
        return $variantId ? "v{$variantId}" : "p{$productId}";
    }

    /**
     * @return array<string, array{product_id: int, variant_id: ?int, quantity: int}>
     */
    private function raw(): array
    {
        return session(self::SESSION_KEY, []);
    }

    private function put(array $cart): void
    {
        session([self::SESSION_KEY => $cart]);
    }
}
