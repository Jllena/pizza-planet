# Pizza Planet

Laravel 12 + Livewire demo that lets customers order preset pizzas or build their own (up to 4 toppings) while logging mock payments. Jetstream provides authentication so staff can review recent orders on the dashboard.

## Requirements
- PHP 8.2+ with Composer
- Node.js 18+ with npm
- SQLite (default) or another database supported by Laravel

## Quick start
1. Copy environment settings: `cp .env.example .env` (defaults to SQLite).
2. Create the SQLite database file: `touch database/database.sqlite`.
3. Install backend dependencies: `composer install`.
4. Install frontend dependencies: `npm install`.
5. Run `npm run build`.
6. Generate the app key: `php artisan key:generate`.
7. Run migrations and seed the menu + test user: `php artisan migrate --seed`.
8. Run `php artisan serve` to test project..

## What’s included
- Seeded menu and toppings:
  - Margherita (£10), Romana (£13), Americana ($13), Mexicana (£15), Custom Pizza (£10).
  - All toppings are £1 and reusable on custom pizzas (max 4).
- Order flow:
  - Visit `/` to pick a preset pizza or build your own, choose Card or PayPal, and place the order.
  - Payment is mocked by logging the method/amount to Laravel logs; no real gateway is called.
  - Confirmation page shows the pizza, toppings, and price breakdown.
- Dashboard:
  - Authenticated dashboard at `/dashboard` lists recent orders with search by customer name or order ID.
  - Test login from the seeder: `test@example.com` / `password`.

## Tests
- Run `php artisan test` (or `composer test`) for the feature and Livewire tests that cover pricing, validation, and dashboard filtering.

## Project notes
- Default storage uses SQLite; switch `DB_CONNECTION` in `.env` if you prefer MySQL/PostgreSQL.
- Tailwind + Vite handle styling/assets; Livewire powers the order form and dashboard table.
