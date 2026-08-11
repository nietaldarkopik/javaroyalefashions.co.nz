<?php

namespace App\Providers;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\PageRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Eloquent\CustomerRepository;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Eloquent\PageRepository;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\SettingRepository;
use App\Services\CartService;
use App\Services\CategoryService;
use App\Services\SettingService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Repository interface => Eloquent implementation. This is the seam
     * controllers/services are built against and the one future tests
     * mock — swapping a data layer later means changing a binding here,
     * not every call site.
     *
     * @var array<class-string, class-string>
     */
    private const REPOSITORY_BINDINGS = [
        PageRepositoryInterface::class => PageRepository::class,
        CategoryRepositoryInterface::class => CategoryRepository::class,
        ProductRepositoryInterface::class => ProductRepository::class,
        OrderRepositoryInterface::class => OrderRepository::class,
        CustomerRepositoryInterface::class => CustomerRepository::class,
        SettingRepositoryInterface::class => SettingRepository::class,
    ];

    public function register(): void
    {
        foreach (self::REPOSITORY_BINDINGS as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }

    public function boot(): void
    {
        // Some MySQL/MariaDB installs still default to an index key-prefix
        // limit of 767 bytes (older InnoDB row format without the
        // large-prefix option enabled). utf8mb4 uses up to 4 bytes per
        // character, so Laravel's default 255-char string column blows
        // past that limit the moment it's unique()'d or indexed — this
        // caps it to 191 chars (191 * 4 = 764 bytes) so migrations don't
        // fail with "Specified key was too long" on those setups.
        Schema::defaultStringLength(191);

        // Admin index pages call ->links() with no explicit view, so this
        // is what they get: a compact Shopee-style pager instead of
        // Laravel's Tailwind-based default (which doesn't match AdminLTE's
        // Bootstrap 4 base and rendered unstyled). The storefront's own
        // pagination passes 'components.pagination' explicitly per call,
        // so it's unaffected by this default.
        Paginator::defaultView('vendor.pagination.admin-shopee');

        // Data every storefront page needs (site name/contact info for the
        // header & footer, nav categories, cart badge count) lives here
        // once instead of being re-fetched in every Front controller.
        //
        // Bound to BOTH 'layouts.front' and 'front.*' — a child view like
        // front.home.index runs its own top-level code (e.g. the argument
        // to @section('title', $siteSetting->...)) *before* @extends hands
        // off to the parent layout, so composing only the parent leaves
        // $siteSetting undefined at that point. Composing every front.*
        // view too makes it available in the child's own scope as well.
        View::composer(['layouts.front', 'layouts.checkout', 'front.*'], function ($view) {
            $cart = app(CartService::class);

            $view->with([
                'siteSetting' => app(SettingService::class)->current(),
                'navCategories' => app(CategoryService::class)->allActive(),
                'cartCount' => $cart->totalQuantity(),
                'miniCartItems' => $cart->items(),
                'miniCartSubtotal' => $cart->subtotal(),
            ]);
        });

        // Admin panel branding (sidebar logo + <title>) comes from the same
        // Setting row the "Website Settings" admin page edits — there's no
        // separate "admin logo" field, one brand identity for both. Bound
        // to 'adminlte::page' specifically (not every admin.* view) so this
        // DB read only happens on actual admin page loads, and fires early
        // enough — the composer runs before that view's own PHP executes,
        // which is before its @extends('adminlte::master') chain reads any
        // of these config values.
        View::composer(['adminlte::page', 'layouts.admin'], function ($view) {
            $setting = app(SettingService::class)->current();

            Config::set('adminlte.title', $setting->site_name);
            Config::set('adminlte.title_prefix', '');
            Config::set('adminlte.title_postfix', ' Admin');
            Config::set('adminlte.logo', '<b>'.e($setting->site_name).'</b>');

            if ($setting->logo_path) {
                // Setting::logo_path is relative to the 'public' disk root
                // (public_path('uploads')), but this config is wrapped in
                // asset() relative to the public/ webroot itself — hence
                // the 'uploads/' prefix.
                Config::set('adminlte.logo_img', 'uploads/'.$setting->logo_path);
                Config::set('adminlte.logo_img_class', 'brand-image');
            }

            $view->with('setting', $setting);
        });
    }
}
