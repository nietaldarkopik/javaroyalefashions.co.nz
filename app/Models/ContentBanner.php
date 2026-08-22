<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentBanner extends Model
{
    use HasFactory;

    /**
     * Fixed set of front-end pages a banner can be placed on. Kept as a
     * small code-defined enum (rather than a database-backed list) since
     * the storefront only has these few top-level pages to place content
     * on; label is what the admin checklist shows.
     */
    public const PAGES = [
        'home' => 'Home',
        'products' => 'All Products',
        'about' => 'About',
        'contact' => 'Contact',
    ];

    protected $fillable = [
        'eyebrow',
        'heading',
        'body',
        'button_text',
        'button_url',
        'image_path',
        'pages',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'pages' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeForPage($query, string $page)
    {
        return $query->whereJsonContains('pages', $page);
    }
}
