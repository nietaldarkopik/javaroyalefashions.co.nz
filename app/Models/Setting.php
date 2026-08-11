<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'site_name',
        'site_tagline',
        'logo_path',
        'show_site_name_with_logo',
        'favicon_path',
        'contact_email',
        'contact_phone',
        'contact_whatsapp',
        'address',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'bank_swift_code',
        'shipping_urban_rate',
        'shipping_rural_rate',
        'currency_code',
        'social_facebook',
        'social_instagram',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'shipping_urban_rate' => 'decimal:2',
            'shipping_rural_rate' => 'decimal:2',
            'show_site_name_with_logo' => 'boolean',
        ];
    }
}
