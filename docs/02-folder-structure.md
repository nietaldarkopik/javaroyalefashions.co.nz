# Project Folder Structure — NZ Product Catalog

Laravel 12 / PHP 8.4 skeleton (Laravel 11+'s slimmer bootstrap — no `app/Http/Kernel.php`
or `app/Console/Kernel.php`; middleware and console scheduling are registered in
`bootstrap/app.php` / `routes/console.php`), extended with a Repository + Service layer
so controllers stay thin and business rules (order totals, status transitions, invoice
sending) live in one testable place instead of scattered across controller methods.

The directory skeleton has already been created under `F:\ecommerse\catalog`; this
document is the map for what goes in each folder as it's filled in.

```
catalog/
├── app/
│   ├── Console/                        Artisan commands (none needed yet)
│   ├── Enums/
│   │   ├── OrderStatus.php             pending_payment|waiting_verification|paid|processing|completed|cancelled
│   │   └── ShippingArea.php            urban|rural
│   ├── Exceptions/                     custom exceptions (e.g. EmptyCartException)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── PageController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   ├── ProductController.php
│   │   │   │   ├── ProductImageController.php   gallery upload/delete/reorder
│   │   │   │   ├── OrderController.php          list/show/update status/verify payment
│   │   │   │   ├── CustomerController.php        list/show + order history
│   │   │   │   ├── SettingController.php         single edit form
│   │   │   │   └── AuthController.php            admin login/logout (staff-only, no register)
│   │   │   └── Front/
│   │   │       ├── HomeController.php
│   │   │       ├── ProductController.php         listing (+ filter/search) & detail
│   │   │       ├── CategoryController.php        category listing page
│   │   │       ├── PageController.php            renders any `pages` row by slug
│   │   │       ├── CartController.php            session cart: add/update/remove/view
│   │   │       └── CheckoutController.php        checkout form → order → bank details → proof upload
│   │   ├── Requests/
│   │   │   ├── Admin/
│   │   │   │   ├── StorePageRequest.php / UpdatePageRequest.php
│   │   │   │   ├── StoreCategoryRequest.php / UpdateCategoryRequest.php
│   │   │   │   ├── StoreProductRequest.php / UpdateProductRequest.php
│   │   │   │   ├── UpdateOrderStatusRequest.php
│   │   │   │   └── UpdateSettingRequest.php
│   │   │   └── Front/
│   │   │       ├── CheckoutRequest.php           name, email, phone, address, area
│   │   │       └── UploadPaymentProofRequest.php  mimes:jpg,png,pdf + max size
│   │   ├── Middleware/                 custom middleware if needed (admin guard is Laravel's default `auth`)
│   │   └── Resources/                  (unused — no API in scope)
│   ├── Models/
│   │   ├── Page.php
│   │   ├── Category.php
│   │   ├── Product.php
│   │   ├── ProductImage.php
│   │   ├── Customer.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── PaymentProof.php
│   │   ├── Setting.php
│   │   └── User.php                    (admin/staff only)
│   ├── Repositories/
│   │   ├── Contracts/                  one interface per aggregate root
│   │   │   ├── PageRepositoryInterface.php
│   │   │   ├── CategoryRepositoryInterface.php
│   │   │   ├── ProductRepositoryInterface.php
│   │   │   ├── OrderRepositoryInterface.php
│   │   │   ├── CustomerRepositoryInterface.php
│   │   │   └── SettingRepositoryInterface.php
│   │   └── Eloquent/                   concrete implementations, bound in AppServiceProvider
│   │       ├── PageRepository.php
│   │       ├── CategoryRepository.php
│   │       ├── ProductRepository.php
│   │       ├── OrderRepository.php
│   │       ├── CustomerRepository.php
│   │       └── SettingRepository.php
│   ├── Services/
│   │   ├── CartService.php             session-backed cart (add/remove/update/totals)
│   │   ├── CheckoutService.php         customer upsert, order+items creation, totals, triggers invoice mail
│   │   ├── OrderService.php            admin-side: verify payment, transition status
│   │   ├── ProductService.php          listing/filter/search query building
│   │   └── SettingService.php          typed accessor over the single settings row
│   ├── Mail/
│   │   └── InvoiceMail.php             sent to customer after checkout
│   ├── Providers/
│   │   └── AppServiceProvider.php      binds each RepositoryInterface → Eloquent implementation
│   └── View/
│       └── Components/                 e.g. ProductCard, PriceTag Blade components
│
├── bootstrap/
│   └── app.php                         middleware + route file registration (Laravel 12 style)
│
├── config/
│   └── adminlte.php                    published AdminLTE config (branding, menu)
│
├── database/
│   ├── migrations/                     one file per table in 01-database-design.md, in FK-safe order:
│   │                                    categories → products → product_images → customers →
│   │                                    orders → order_items → payment_proofs → pages → settings →
│   │                                    users role column
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── UserSeeder.php              one admin login
│   │   ├── SettingSeeder.php           row 1 with sane NZ defaults (rates, bank placeholder)
│   │   ├── CategorySeeder.php
│   │   ├── ProductSeeder.php
│   │   └── PageSeeder.php              About / Contact / Privacy Policy / Terms placeholders
│   └── factories/
│       ├── ProductFactory.php
│       ├── CategoryFactory.php
│       ├── CustomerFactory.php
│       └── OrderFactory.php            for tests, not production seeding
│
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── front.blade.php         Bootstrap 5 storefront shell (header/nav/footer)
│       │   └── admin.blade.php         AdminLTE shell wrapper
│       ├── front/
│       │   ├── home/index.blade.php
│       │   ├── products/index.blade.php   listing + search/category filter
│       │   ├── products/show.blade.php    detail page
│       │   ├── categories/show.blade.php
│       │   ├── pages/show.blade.php       generic static-page renderer
│       │   ├── cart/index.blade.php
│       │   └── checkout/
│       │       ├── index.blade.php        contact + address + area → totals
│       │       └── success.blade.php      bank details + proof upload form
│       ├── admin/
│       │   ├── dashboard/index.blade.php
│       │   ├── pages/{index,create,edit}.blade.php
│       │   ├── categories/{index,create,edit}.blade.php
│       │   ├── products/{index,create,edit}.blade.php
│       │   ├── orders/{index,show}.blade.php
│       │   ├── customers/{index,show}.blade.php
│       │   └── settings/edit.blade.php
│       ├── emails/
│       │   └── invoice.blade.php
│       └── components/
│           └── product-card.blade.php
│
├── routes/
│   ├── web.php                         storefront routes
│   ├── admin.php                       `/admin/*`, `auth` + `role:admin` middleware group
│   └── console.php
│
├── public/
│   └── uploads/                        symlinked or direct-served: products/, payment-proofs/, settings/
│
├── storage/
│   └── app/public/                     canonical file storage (payment proofs, uploaded images)
│
├── tests/
│   ├── Feature/                        checkout flow, admin CRUD, order status transitions
│   └── Unit/                           CheckoutService totals, OrderStatus transitions
│
└── docs/
    ├── 01-database-design.md
    └── 02-folder-structure.md          (this file)
```

## Why Repository + Service here specifically

- **Repositories** isolate Eloquent query construction (filtering products by category/
  search term, pulling an order with its items/proofs eager-loaded) behind an interface,
  so `ProductController` never touches `Product::query()` directly. Swapping the query
  layer later (e.g. adding Scout search) touches one class, not every controller.
- **Services** hold the actual business rules that don't belong to any single model:
  `CheckoutService` decides how a cart becomes an `Order` + `OrderItem` rows and what
  shipping rate applies; `OrderService` enforces which status transitions are legal.
  Controllers call a service method and render a view — no business logic in the
  controller layer.
- Bound together in `AppServiceProvider::register()`:
  `$this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);`
  (repeated per pair) — this is also the seam future tests mock against.

## Next step

With the schema and structure agreed, the next deliverable is the actual code: migrations
first (in the FK-safe order above), then models with relationships/casts, then the
repository/service pairs, then controllers + Form Requests, then Blade views, then
seeders. Say the word and I'll start generating those in that order.
