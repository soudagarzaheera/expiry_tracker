# Smart Expiry Management System (Expiry Tracker)

A complete college-level Laravel 12 web project for managing product inventory and expiry dates. It uses Blade, Bootstrap 5, PHP 8.2+, and a local SQLite database—no external database server or Node.js build is required.

## Features

- Registration, login, logout, hashed passwords, validation, and email verification
- Separate Admin and Normal User dashboards
- Secure product ownership enforced by a Laravel policy
- Product CRUD with category, batch, quantity, unit, manufacture/expiry dates, owner, notes, and disposal status
- Dynamic status rules: Safe (>30 days), Expiring Soon (0–30 days), and Expired (date passed)
- Automatic alerts with navigation count; administrators can dismiss and restore alerts
- Product renewal and expired-product disposal flow
- Admin user activation/deactivation and role management
- Admin category management and access to all products
- Inventory, safe, expiring-soon, expired, and disposed reports
- Date/category filters, printable report, and CSV export
- Responsive Bootstrap 5 interface loaded from CDN
- Seeded categories, products, and verified demo accounts
- Feature tests covering authentication, authorization, status rules, CRUD, and reports

## Quick start

### macOS

```bash
chmod +x setup.sh run.sh
./setup.sh
./run.sh
```

See [INSTALL_MAC.md](INSTALL_MAC.md) for prerequisites.

### Windows

Install [Laravel Herd for Windows](https://herd.laravel.com/windows) as the easiest PHP and Composer prerequisite. Restart VS Code or Command Prompt after installation, then run:

```bat
setup.bat
run.bat
```

See [INSTALL_WINDOWS.md](INSTALL_WINDOWS.md) for prerequisites.

Then open `http://127.0.0.1:8000`.

## Demo accounts

| Role | Email | Password |
|---|---|---|
| Admin | `admin@expirytracker.test` | `Admin@123` |
| Normal User | `user@expirytracker.test` | `User@123` |

Both demo accounts are already email verified.

## Local email verification

The default mailer is `log`, so registration works without SMTP. Laravel writes the verification email to `storage/logs/laravel.log`. Search the newest log entry for `Verify Email Address`, copy the signed `http://.../email/verify/...` URL, and open it in the same browser.

For real email later, edit `.env` with an SMTP provider’s values:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_SCHEME=tls
MAIL_FROM_ADDRESS=noreply@example.com
```

Then run `php artisan config:clear`. Do not commit real SMTP passwords.

## Useful commands

```bash
php artisan serve
php artisan migrate --seed
php artisan test
php artisan route:list --except-vendor
```

The database is stored at `database/database.sqlite`. Bootstrap and Bootstrap Icons are loaded from jsDelivr, so the application functions offline but needs internet access for full styling unless those static files are downloaded locally later.

## Main project folders

- `app/Http/Controllers` — authentication, product, alert, report, and admin workflows
- `app/Models` and `app/Policies` — data relationships, status calculation, and ownership rules
- `database/migrations`, `factories`, and `seeders` — SQLite schema and demonstration data
- `resources/views` — responsive Blade user interface
- `tests/Feature` and `tests/Unit` — automated verification
- `requirements` — original synopsis and diagrams
