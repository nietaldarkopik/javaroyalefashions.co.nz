<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Lifetime spend across paid/processing/completed orders. Computed on
     * demand (withSum in the repository), never stored, so it can't go
     * stale as order statuses change.
     */
    public function scopeWithOrderStats($query)
    {
        return $query->withCount('orders')->withSum('orders', 'grand_total');
    }
}
