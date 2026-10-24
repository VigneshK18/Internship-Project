# TechKart – E-commerce Platform with Laravel

Capstone project of the AICTE–EduSkills Virtual Internship *Full Stack PHP Development With Project*
(June–August 2026), by Vignesh Kadiyala.

An online store where customers browse products, manage a cart and place orders, and an
administrator manages the catalogue and the orders. Built with **Laravel 12**, **MySQL**, **Blade**,
**Eloquent**, **Breeze** authentication and a **Sanctum**-protected REST API.

---

## Features

| Module | What it does |
|---|---|
| **Authentication** | Register, log in, log out, password reset and profile (Laravel Breeze). Administrator role enforced with a Gate (`can:admin`) and an `OrderPolicy`. |
| **Product catalogue** | 20 seeded products in 7 categories, search, category filter, sorting, pagination and product pages with related items. |
| **Shopping cart** | Add, update and remove items; quantities are capped at the stock. Guests' carts live in the session; logged-in carts are saved in `cart_items`, and a guest cart is merged on login. |
| **Checkout & orders** | Delivery address form with validation (Indian mobile number and PIN code), Cash on Delivery, free delivery above ₹999. The order is created **inside a database transaction** with row locks, stock is decremented and the cart is cleared. Customers can view their order history and cancel pending orders (stock is restored). |
| **Admin panel** | Dashboard (revenue, orders, low stock), CRUD for products (with image upload) and categories, order list with filters and status updates (pending → processing → shipped → delivered / cancelled). |
| **REST API** | JSON endpoints for products (public) and orders (token required) using Laravel Sanctum. |
| **Queues** | The order-confirmation e-mail is a queued notification (`ShouldQueue`), processed by `php artisan queue:work`. |
| **Scheduling** | `orders:cancel-stale` runs daily and cancels pending orders older than 48 hours, returning their items to stock. |
| **Tests** | 64 PHPUnit feature and unit tests (shop, cart, checkout, orders, admin, API, scheduler, Breeze auth). |

## Requirements

* PHP **8.2 or newer** with the extensions XAMPP already enables (pdo_mysql, mbstring, openssl, fileinfo, gd)
* MySQL / MariaDB (XAMPP) – or SQLite for a quick test
* **Composer and Node.js are not required**: the `vendor/` folder and the compiled CSS/JS (`public/build`) are included.

## Setup on XAMPP (Windows)

1. Copy the `2-laravel-ecommerce` folder somewhere convenient, for example `C:\xampp\htdocs\laravel-ecommerce`.
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open <http://localhost/phpmyadmin> and create an empty database named **`techkart`**.
4. Open a terminal (Command Prompt) in the project folder and run:

   ```bat
   copy .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   php artisan serve
   ```

   If `php` is not recognised, use XAMPP's PHP directly, e.g. `C:\xampp\php\php.exe artisan serve`
   (or add `C:\xampp\php` to your PATH).

5. Open <http://localhost:8000>.

Alternatively, without `php artisan serve`, browse to <http://localhost/laravel-ecommerce/public/> (Apache's
`mod_rewrite` is enabled in XAMPP by default).

### Demo accounts

| Role | E-mail | Password |
|---|---|---|
| Administrator | `admin@techkart.test` | `password` |
| Customer (with 3 past orders) | `customer@techkart.test` | `password` |

Or register a new customer account.

### Optional: run the queue worker and the scheduler

```bat
php artisan queue:work        :: sends queued order-confirmation e-mails
php artisan schedule:work     :: runs orders:cancel-stale every day
```

With `MAIL_MAILER=log`, e-mails are written to `storage/logs/laravel.log` instead of being sent.

### Quick start with SQLite instead of MySQL

In `.env`, comment out the `DB_*` MySQL lines and set `DB_CONNECTION=sqlite`, then create an empty file
`database/database.sqlite` and run `php artisan migrate --seed`.

## REST API

Base URL: `http://localhost:8000/api` – always send `Accept: application/json`.

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| POST | `/tokens` | – | Body `{"email", "password", "device_name"}` → returns a Bearer token |
| GET | `/products?q=&category=&sort=&per_page=` | – | Paginated product list |
| GET | `/products/{slug}` | – | One product |
| GET | `/user` | Bearer | The token's user |
| GET | `/orders` | Bearer | The customer's orders with items |
| GET | `/orders/{order_number}` | Bearer | One order (only your own) |
| DELETE | `/tokens/current` | Bearer | Revoke the token |

Example:

```bash
curl -X POST http://localhost:8000/api/tokens -H "Accept: application/json" \
     -H "Content-Type: application/json" \
     -d '{"email":"customer@techkart.test","password":"password"}'

curl http://localhost:8000/api/orders -H "Accept: application/json" -H "Authorization: Bearer <token>"
```

A ready-made Postman collection is in `docs/TechKart-API.postman_collection.json` (import it, run **Get token**
first; the token is stored automatically).

## Run the tests

```bat
php artisan test
```

Tests use an in-memory SQLite database, so they never touch your MySQL data. Expected: **64 passed**.

## Project structure (main files)

```
app/
├── Enums/OrderStatus.php                 # pending, processing, shipped, delivered, cancelled
├── Models/                               # User, Category, Product, CartItem, Order, OrderItem
├── Services/CartService.php              # session cart for guests, cart_items for users
├── Services/OrderService.php             # placeOrder() in a DB transaction, cancel() restocks
├── Http/Controllers/                     # Shop, Cart, Checkout, Order, Dashboard
├── Http/Controllers/Admin/               # Dashboard, Product (image upload), Category, Order
├── Http/Controllers/Api/                 # Auth (tokens), Product, Order
├── Http/Requests/                        # CheckoutRequest, Admin/ProductRequest, Admin/CategoryRequest
├── Http/Resources/                       # JSON shape of products and orders
├── Policies/OrderPolicy.php              # view / cancel / updateStatus
├── Listeners/MergeSessionCart.php        # guest cart -> database on login
├── Notifications/OrderPlaced.php         # queued order-confirmation e-mail
└── Console/Commands/CancelStaleOrders.php
database/migrations/                      # categories, products, cart_items, orders, order_items, is_admin
database/seeders/                         # catalogue, admin + customer, sample orders
resources/views/                          # Blade: shop, cart, checkout, orders, admin, components
routes/web.php, routes/api.php, routes/console.php (schedule)
tests/Feature, tests/Unit
public/images/products/                   # product images used by the seeder
public/uploads/                           # images uploaded from the admin panel
```

### Database

| Table | Key columns |
|---|---|
| `users` | name, email, password, `is_admin` |
| `categories` | name, slug |
| `products` | category_id → categories, name, slug, description, price, stock, image, is_active |
| `cart_items` | user_id → users, product_id → products, quantity (unique per user + product) |
| `orders` | user_id → users, order_number, status, subtotal, shipping, total, delivery address, payment_method |
| `order_items` | order_id → orders, product_id → products (nullable), product_name, unit_price, quantity, line_total |

Relationships: Category 1–N Product · User 1–N Order · Order N–N Product through `order_items`.

## Deployment notes

For a production server: set `APP_ENV=production`, `APP_DEBUG=false` and the real `APP_URL` and database
credentials in `.env`, point the web server's document root at `public/`, then run

```bash
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

and keep `php artisan queue:work` running (e.g. with Supervisor) plus a cron entry
`* * * * * php /path/to/artisan schedule:run`.

To change the frontend styles, install Node.js and run `npm install && npm run build`.
