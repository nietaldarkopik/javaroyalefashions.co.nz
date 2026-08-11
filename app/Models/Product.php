<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'price',
        'sale_price',
        'stock_quantity',
        'image_path',
        'is_active',
        'is_featured',
        'weight_kg',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'weight_kg' => 'decimal:2',
            'stock_quantity' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('size')->orderBy('color');
    }

    /**
     * Whether this product is sold via variants (size/color/etc.) rather
     * than its own single price+stock. Checked off the loaded relation
     * when available so listing pages don't trigger an extra query per
     * product.
     */
    public function hasVariants(): bool
    {
        return $this->relationLoaded('variants')
            ? $this->variants->where('is_active', true)->isNotEmpty()
            : $this->variants()->active()->exists();
    }

    /**
     * Min/max effective price across active variants, for "from $X" style
     * display on listings. Null when the product has no variants.
     *
     * @return array{min: float, max: float}|null
     */
    protected function priceRange(): Attribute
    {
        return Attribute::get(function () {
            $variants = ($this->relationLoaded('variants') ? $this->variants : $this->variants()->get())
                ->where('is_active', true);

            if ($variants->isEmpty()) {
                return null;
            }

            $prices = $variants->map(
                fn (ProductVariant $v) => $v->price !== null ? (float) $v->price : (float) $this->current_price
            );

            return ['min' => $prices->min(), 'max' => $prices->max()];
        });
    }

    /**
     * The price actually charged: the sale price when one is set and lower
     * than the regular price, otherwise the regular price. Derived, not
     * stored — storing it would just invite the two to drift apart.
     */
    protected function currentPrice(): Attribute
    {
        return Attribute::get(function () {
            if ($this->sale_price !== null && (float) $this->sale_price < (float) $this->price) {
                return $this->sale_price;
            }

            return $this->price;
        });
    }

    protected function isOnSale(): Attribute
    {
        return Attribute::get(fn () => $this->sale_price !== null && (float) $this->sale_price < (float) $this->price
        );
    }

    protected function isInStock(): Attribute
    {
        return Attribute::get(fn () => $this->stock_quantity > 0);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInCategory($query, ?string $categorySlug)
    {
        return $categorySlug
            ? $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug))
            : $query;
    }

    public function scopeSearch($query, ?string $term)
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%")
                ->orWhere('short_description', 'like', "%{$term}%");
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
