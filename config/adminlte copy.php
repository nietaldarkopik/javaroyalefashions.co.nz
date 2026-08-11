<?php

// jeroennoten/laravel-adminlte config. This package merges its own internal
// defaults with whatever keys are present here, so only the keys the admin
// panel actually needs to override are listed — everything else (colors,
// plugin toggles, layout classes, etc.) falls back to the package default.
//
// After `composer require jeroennoten/laravel-adminlte` and
// `php artisan adminlte:install`, re-check this file against the freshly
// published one for any package-version-specific key changes, then keep the
// `menu` block below — it's the one piece specific to this app.

return [

    'title' => env('APP_NAME', 'NZ Product Catalog').' Admin',
    'title_prefix' => '',
    'title_postfix' => '',

    'logo' => '<b>NZ</b> Catalog',
    'logo_img' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,

    'menu' => [
        [
            'text' => 'Dashboard',
            'url' => 'admin/dashboard',
            'icon' => 'fas fa-fw fa-tachometer-alt',
        ],
        ['text' => 'Catalog', 'header' => true],
        [
            'text' => 'Categories',
            'url' => 'admin/categories',
            'icon' => 'fas fa-fw fa-tags',
        ],
        [
            'text' => 'Products',
            'url' => 'admin/products',
            'icon' => 'fas fa-fw fa-box-open',
        ],
        ['text' => 'Sales', 'header' => true],
        [
            'text' => 'Orders',
            'url' => 'admin/orders',
            'icon' => 'fas fa-fw fa-receipt',
        ],
        [
            'text' => 'Customers',
            'url' => 'admin/customers',
            'icon' => 'fas fa-fw fa-users',
        ],
        ['text' => 'Content', 'header' => true],
        [
            'text' => 'Pages',
            'url' => 'admin/pages',
            'icon' => 'fas fa-fw fa-file-alt',
        ],
        [
            'text' => 'Settings',
            'url' => 'admin/settings',
            'icon' => 'fas fa-fw fa-cog',
        ],
        ['text' => 'View Storefront', 'header' => true],
        [
            'text' => 'Visit Site',
            'url' => '/',
            'icon' => 'fas fa-fw fa-external-link-alt',
            'target' => '_blank',
        ],
    ],

    'user_menu' => [
        'enabled' => true,
    ],

];
