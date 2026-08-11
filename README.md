# NZ Product Catalog

Laravel 12 / PHP 8.4 product catalog with guest checkout and manual bank transfer.
Not a full e-commerce build — no payment gateway, no customer accounts, no
coupons/wishlist/reviews. See [`docs/01-database-design.md`](docs/01-database-design.md)
and [`docs/02-folder-structure.md`](docs/02-folder-structure.md) for the design behind it.

This code was hand-authored (no `composer`/`php` available in the environment that
generated it), so the very first thing to do is get it actually installed and booted —
none of it has been run yet.

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Edit `.env`: set `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` for a MySQL database you've
already created, then:

```bash
php artisan migrate --seed
php artisan storage:link
php artisan adminlte:install
```

`adminlte:install` (from `jeroennoten/laravel-adminlte`, already in `composer.json`)
publishes the package's own `config/adminlte.php` and copies the AdminLTE front-end
assets into `public/vendor/adminlte`. **This project's `config/adminlte.php` only
overrides `title`, `logo`, `menu`, and `user_menu`** — re-check it against whatever the
install command publishes in case a newer package version renamed a key, then keep the
`menu` block as-is; it's what wires the sidebar to this app's admin routes.

```bash
php artisan serve
```

- Storefront: `http://localhost:8000`
- Admin panel: `http://localhost:8000/admin/login`
  — seeded login: `admin@example.co.nz` / `password` (change this immediately — see
  `database/seeders/UserSeeder.php`)

## What's using a CDN vs. what needs a build step

The storefront layout (`resources/views/layouts/front.blade.php`) and the admin login
page load **Bootstrap 5 / Bootstrap Icons / AdminLTE's login CSS from a CDN** directly —
there's no `npm install`/Vite build required to see either of those. The *authenticated*
admin panel, though, extends `adminlte::page` from the `jeroennoten/laravel-adminlte`
package, which serves its own bundled AdminLTE assets from `public/vendor/adminlte` — that
only exists after `php artisan adminlte:install` has run.

## Mail

Order invoices send through whatever `MAIL_MAILER` is configured in `.env`. For local
testing, point it at [Mailtrap](https://mailtrap.io) or run
`php artisan tinker` and check `storage/logs/laravel.log` after setting
`MAIL_MAILER=log`.

## Tests

`tests/Feature` and `tests/Unit` directories exist but are currently empty — the
`database/factories/*` are in place (`ProductFactory`, `CategoryFactory`,
`CustomerFactory`, `OrderFactory`) so feature tests for the checkout flow and admin CRUD
can be added directly on top of them.

## Known gaps / deliberate omissions

See the "Design decisions worth flagging" section in `docs/01-database-design.md` —
no cart table (session-based by design), no coupons/wishlist/reviews, no payment
gateway. `payment_method`, `currency`, and `weight_kg` columns already exist
unused specifically so those upgrades don't need a new migration later.
