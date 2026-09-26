<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\ShippingArea;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address_line1',
        'shipping_address_line2',
        'shipping_suburb',
        'shipping_city',
        'shipping_region',
        'shipping_postcode',
        'shipping_area',
        'subtotal',
        'shipping_cost',
        'grand_total',
        'currency',
        'status',
        'payment_method',
        'customer_notes',
        'admin_notes',
        'verified_at',
        'verified_by',
        'invoice_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'shipping_area' => ShippingArea::class,
            'status' => OrderStatus::class,
            'subtotal' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'verified_at' => 'datetime',
            'invoice_sent_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function paymentProofs(): HasMany
    {
        return $this->hasMany(PaymentProof::class)->latest();
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function reviewInvitation(): HasOne
    {
        return $this->hasOne(ReviewInvitation::class);
    }

    public function productReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function latestPaymentProof(): ?PaymentProof
    {
        return $this->paymentProofs->first();
    }

    public function isReviewable(): bool
    {
        return $this->status->isReviewable();
    }

    /**
     * The order's reviewable lines, one per product: lines for the same
     * product bought in several variants collapse into one (the first
     * line), since a review is per product per order. Lines whose product
     * has since been deleted are skipped — there's no page to show a
     * review on.
     *
     * @return Collection<int, array{item: OrderItem, variant_labels: Collection<int, string>}> keyed by product_id
     */
    public function reviewableItems(): Collection
    {
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->with('product')->get();

        return collect($items)
            ->filter(fn (OrderItem $item) => $item->product_id !== null)
            ->sortBy('id')
            ->groupBy('product_id')
            ->map(fn (Collection $lines) => [
                'item' => $lines->first(),
                'variant_labels' => $lines->pluck('variant_label')->filter()->unique()->values(),
            ]);
    }

    public function scopeStatus($query, ?string $status)
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function shippingAddressLines(): array
    {
        return array_filter([
            $this->shipping_address_line1,
            $this->shipping_address_line2,
            $this->shipping_suburb,
            "{$this->shipping_city} {$this->shipping_postcode}",
            $this->shipping_region,
        ]);
    }
}
