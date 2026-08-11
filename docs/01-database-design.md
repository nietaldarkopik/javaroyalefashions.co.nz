# Database Design — NZ Product Catalog

Scope: catalog + guest checkout + manual bank transfer. No customer login, no payment
gateway, no coupons/wishlist/reviews — those are explicitly out of scope for this build.
Everything below is sized to the requirements only; fields marked "future-proofing" are
cheap now (a nullable column) and expensive to retrofit later (a new migration + backfill).

## Entity overview

```
categories 1───* products 1───* product_images
                    │
                    * (order_items.product_id, nullable — snapshot on delete)
                    │
customers 1───* orders 1───* order_items
                    │
                    1───* payment_proofs
                    │
                    * verified_by ──1 users

pages        (standalone CMS content)
settings     (single-row site config)
users        (admin/staff auth only — never customer-facing)
```

## Tables

### `categories`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| name | string(120) | |
| slug | string(150) unique | for `/category/{slug}` and filter querystrings |
| description | text nullable | shown on category listing page |
| image_path | string nullable | category tile image |
| is_active | boolean default true | inactive categories hidden from frontend, kept in admin |
| sort_order | unsigned int default 0 | manual ordering in nav/listing |
| timestamps | | |

No `parent_id` — the spec has flat categories only (no subcategory requirement). Adding
a nullable `parent_id` later is a one-line migration if that changes.

### `products`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| category_id | bigint FK → categories, nullable, `on delete set null` | product survives category deletion |
| name | string(180) | |
| slug | string(200) unique | `/products/{slug}` |
| sku | string(60) unique nullable | manual or admin-entered |
| short_description | string(255) nullable | listing/card blurb |
| description | longtext nullable | full detail-page copy |
| price | decimal(10,2) | NZD, GST-inclusive display handled at presentation layer |
| sale_price | decimal(10,2) nullable | when set and < price, shown as the active price |
| stock_quantity | unsigned int default 0 | simple stock count, no per-variant stock (no variants in spec) |
| image_path | string nullable | primary/listing image (denormalized for fast listing queries — avoids a join to `product_images` on every catalog page) |
| is_active | boolean default true | |
| is_featured | boolean default false | for a "featured products" home section |
| weight_kg | decimal(6,2) nullable | **future-proofing**: unused today, needed the day a shipping-rate API is added |
| meta_title | string nullable | |
| meta_description | string nullable | |
| timestamps | | |

`current_price` (sale_price if set and lower, else price) is a model accessor, not a
stored column — it's derived, storing it would just invite drift.

### `product_images`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| product_id | bigint FK → products, `on delete cascade` | |
| image_path | string | |
| sort_order | unsigned int default 0 | |
| timestamps | | |

Gallery images beyond the primary one, for the product detail page.

### `customers`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| name | string(150) | |
| email | string(150) unique | dedup key — checkout does `firstOrCreate(['email' => ...])` |
| phone | string(30) | doubles as WhatsApp number per spec ("phone/WhatsApp") |
| timestamps | | |

Deliberately thin: this is a follow-up contact record, not an account. Shipping address
lives on `orders` (it can legitimately differ order to order), not here — putting it here
would mean overwriting a customer's history every time they ship somewhere new. Order
counts / lifetime value shown in the admin customer view are computed via
`withCount`/`withSum` on the `orders` relationship, not stored, so they can never go stale.

### `orders`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| order_number | string(30) unique | e.g. `NZ20260801-0007`, generated in `CheckoutService` |
| customer_id | bigint FK → customers, `on delete restrict` | orders must not vanish if a customer record is ever removed |
| customer_name | string(150) | **snapshot** at time of order |
| customer_email | string(150) | **snapshot** |
| customer_phone | string(30) | **snapshot** |
| shipping_address_line1 | string(200) | |
| shipping_address_line2 | string(200) nullable | |
| shipping_suburb | string(120) nullable | common in NZ addressing |
| shipping_city | string(120) | |
| shipping_region | string(120) nullable | e.g. Auckland, Canterbury |
| shipping_postcode | string(10) | |
| shipping_area | string(10) | backed by `ShippingArea` enum: `urban` \| `rural` — determines the flat rate applied |
| subtotal | decimal(10,2) | sum of `order_items.line_total` |
| shipping_cost | decimal(10,2) | 13.00 or 18.00 at time of order (snapshot — rate changes later must not alter historical orders) |
| grand_total | decimal(10,2) | subtotal + shipping_cost |
| currency | string(3) default 'NZD' | **future-proofing** for multi-currency, unused now |
| status | string(30) default 'pending_payment' | backed by `OrderStatus` enum, see below |
| payment_method | string(30) default 'bank_transfer' | **future-proofing**: column already exists for when a gateway is added, no migration needed then |
| customer_notes | text nullable | delivery instructions from checkout form |
| admin_notes | text nullable | internal notes, e.g. why an order was cancelled |
| verified_at | timestamp nullable | when admin confirmed the bank transfer |
| verified_by | bigint FK → users, nullable | which admin verified it |
| invoice_sent_at | timestamp nullable | guards against double-sending the invoice email |
| timestamps | | |

Snapshotting `customer_name/email/phone` on the order (rather than only joining to
`customers`) means an order's paper trail never changes even if the customer contacts
support later and their record gets corrected.

**`OrderStatus` enum** (`pending_payment`, `waiting_verification`, `paid`, `processing`,
`completed`, `cancelled`) exactly matches the spec's status list. Modeled as a PHP 8.4
backed enum (`App\Enums\OrderStatus`), cast on the model — gives IDE autocomplete and a
single source of truth for status labels/badge colors in the admin UI, instead of magic
strings scattered across controllers and views.

### `order_items`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| order_id | bigint FK → orders, `on delete cascade` | |
| product_id | bigint FK → products, nullable, `on delete set null` | order line survives product deletion |
| product_name | string(180) | **snapshot** — product may be renamed/deleted later |
| product_sku | string(60) nullable | **snapshot** |
| unit_price | decimal(10,2) | **snapshot** of price paid, independent of later price changes |
| quantity | unsigned int | |
| line_total | decimal(10,2) | `unit_price * quantity`, stored (not computed) so historical order totals never shift if calculation logic changes later |
| timestamps | | |

### `payment_proofs`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| order_id | bigint FK → orders, `on delete cascade` | |
| file_path | string | stored under `storage/app/public/payment-proofs` |
| original_filename | string | |
| mime_type | string(100) | jpg/png/pdf per spec |
| file_size | unsigned int | bytes, for admin display / basic sanity |
| is_verified | boolean default false | |
| verified_at | timestamp nullable | |
| verified_by | bigint FK → users, nullable | |
| timestamps | | |

A table rather than columns-on-`orders` because a customer can re-upload (wrong file,
blurry scan) — history of every attempt stays visible to the admin verifying payment.

### `pages`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| slug | string(150) unique | `about`, `contact`, `privacy-policy`, `terms`, ... |
| title | string(180) | |
| content | longtext | rendered as-is (rich text from admin) |
| meta_title | string nullable | |
| meta_description | string nullable | |
| is_published | boolean default true | |
| timestamps | | |

One table drives every static page — Home is a real Blade view (not CMS-managed) since
it composes featured products/categories rather than flat content.

### `settings`
Single-row config table (`id = 1`, enforced in `SettingService`), not a key-value store:
the field set is small, fixed, and known upfront, so fixed columns give type safety and
autocomplete for something a key-value table would only fake with runtime lookups.

| Column | Type | Notes |
|---|---|---|
| id | bigint PK | always row 1 |
| site_name | string(120) | |
| site_tagline | string(200) nullable | |
| logo_path | string nullable | |
| favicon_path | string nullable | |
| contact_email | string(150) | |
| contact_phone | string(30) nullable | |
| contact_whatsapp | string(30) nullable | |
| address | string(255) nullable | shown in footer / Contact page |
| bank_name | string(120) | shown on the post-checkout payment instructions screen |
| bank_account_name | string(150) | |
| bank_account_number | string(60) | |
| bank_swift_code | string(20) nullable | NZ banks use SWIFT/BIC for reference, not sort codes |
| shipping_urban_rate | decimal(10,2) default 13.00 | admin-editable, per spec's flat-rate model |
| shipping_rural_rate | decimal(10,2) default 18.00 | |
| currency_code | string(3) default 'NZD' | |
| social_facebook | string nullable | |
| social_instagram | string nullable | |
| meta_title | string nullable | site-wide default SEO fallback |
| meta_description | string nullable | |
| timestamps | | |

Shipping rates live here (not hardcoded in `CheckoutService`) because "Website Settings"
in the spec implies the admin can change them without a code deploy.

### `users`
Laravel's default table, used **exclusively for admin/staff login** — customers never
get a row here. Adds one column beyond the framework default:

| Column | Type | Notes |
|---|---|---|
| ...default Laravel columns... | | id, name, email, password, remember_token, timestamps |
| role | string(20) default 'admin' | **future-proofing** for the spec's "ready for future upgrades" — costs nothing now, unblocks staff/admin role separation later without a migration |

## Design decisions worth flagging

- **Cart is session-based, not a database table.** Guest-only checkout with no login
  means there's no user to persist a cart against between visits; a `CartService` backed
  by the session is simpler and matches the actual requirement. If accounts are added
  later, a `carts`/`cart_items` pair can be introduced then — deferring it now avoids
  building persistence nothing currently needs.
- **No `coupons`, `wishlists`, `reviews`, `variants` tables.** Not in the spec; adding
  them now would be scope creep against an explicit "NOT a full e-commerce website."
- **Decimals, not floats**, for every money column — avoids floating-point rounding
  errors in totals.
- **Enums as PHP backed enums cast on the model**, not a `statuses` lookup table — the
  status list is fixed and small (6 values), a lookup table would be pure ceremony.

## Next step

Once this schema is confirmed, the next deliverable is the actual `database/migrations/*`
files, Eloquent models with relationships/casts, and the seeders — see
`02-folder-structure.md` for where each will live.
