<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'size',
        'color',
        'attribute_name',
        'attribute_value',
        'price',
        'stock_quantity',
        'image_path',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductVariantImage::class)->orderBy('sort_order');
    }

    /**
     * This variant's price if it overrides the parent, otherwise the
     * parent product's own current (sale-aware) price.
     */
    protected function effectivePrice(): Attribute
    {
        return Attribute::get(
            fn () => $this->price !== null ? (float) $this->price : (float) $this->product->current_price
        );
    }

    protected function isInStock(): Attribute
    {
        return Attribute::get(fn () => $this->stock_quantity > 0);
    }

    /**
     * Human-readable combo label, e.g. "Red / M / Cotton" — only the
     * attributes actually set are included.
     */
    protected function label(): Attribute
    {
        return Attribute::get(fn () => implode(' / ', array_filter([
            $this->size,
            $this->color,
            $this->attribute_value,
        ])));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
