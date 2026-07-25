<h1 align="center">GharProperty.pk</h1>

<p align="center">
  <strong>An original Laravel and Vue property marketplace for Pakistan, created and developed by Muhammad Ali Nawaz.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+" />
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12" />
  <img src="https://img.shields.io/badge/Vue-3-42B883?style=for-the-badge&logo=vuedotjs&logoColor=white" alt="Vue 3" />
  <img src="https://img.shields.io/badge/Inertia.js-3-9553E9?style=for-the-badge" alt="Inertia.js 3" />
  <img src="https://img.shields.io/badge/MySQL-MariaDB-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL and MariaDB" />
</p>

## Screenshots

| Homepage | Login |
|---|---|
| ![GharProperty.pk homepage](docs/screenshots/homepage.png) | ![GharProperty.pk login](docs/screenshots/login.png) |

| Property Workspace | List Property |
|---|---|
| ![GharProperty.pk property workspace](docs/screenshots/dashboard.png) | ![GharProperty.pk create property form](docs/screenshots/list_property.png) |

## Overview

GharProperty.pk is a responsive full-stack property marketplace built with Laravel, Vue, Inertia.js, Tailwind CSS, and MySQL. It supports public property discovery, verified user accounts, seller property management, image galleries, secure offer workflows, notifications, soft deletion and restoration, and deterministic local demonstration data.

The project demonstrates practical Laravel development through authorization policies, Form Request validation, Eloquent relationships, database constraints, transactions, row locking, secure image uploads, automated tests, CI verification, and a responsive Vue interface.

## Features

### Public marketplace

- Responsive property catalogue
- Filtering by price, bedrooms, bathrooms, and area
- Localized Lakh and Crore price formatting
- Property posting time in human-readable form
- Full property detail pages
- Click-to-open multi-image gallery
- Stable fallback property images
- Monthly payment estimate

### Authentication and security

- Registration and login
- Email verification
- Login rate limiting
- Session regeneration
- CSRF protection
- Secure password hashing
- Policy-based authorization
- Public owner-data privacy

### Property management

- Create, view, edit, and soft-delete properties
- Restore archived properties
- Upload and manage property images
- Secure MIME, extension, size, and dimension validation
- Transaction-safe file persistence and cleanup
- Administrator access to the complete property workspace
- Regular users restricted to their own properties

### Offers and notifications

- Buyer offer submission
- Duplicate-offer prevention
- Owner and administrator authorization
- Atomic offer acceptance with database row locking
- Automatic competing-offer rejection
- Idempotent offer acceptance
- Database and mail notifications

### Quality assurance

- Feature and unit tests
- Laravel Pint formatting checks
- ESLint checks
- Vite production build
- Composer and NPM security audits
- GitHub Actions CI workflow

## Technology stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| Frontend | Vue 3, Inertia.js 3 |
| Styling | Tailwind CSS 3 |
| Database | MySQL or MariaDB |
| Authentication | Laravel session authentication and email verification |
| Asset compilation | Vite 8 |
| Testing | PHPUnit 11, Laravel feature tests |
| Code style | Laravel Pint and ESLint |
| Package management | Composer and NPM |

## Project structure

```text
app/
├── Http/Controllers/
├── Http/Requests/
├── Models/
├── Notifications/
├── Policies/
└── Providers/

database/
├── factories/
├── migrations/
└── seeders/

resources/
├── css/
├── js/
│   ├── Components/
│   ├── Layouts/
│   ├── Pages/
│   └── Support/
└── views/

tests/
├── Feature/
└── Unit/
```

## Requirements

- PHP 8.2 or newer
- Composer 2
- Node.js 22 and NPM
- MySQL or MariaDB
- Required PHP extensions, including PDO MySQL, mbstring, fileinfo, OpenSSL, and XML

## Installation

### 1. Clone the repository

```bash
git clone YOUR_REPOSITORY_URL
cd GharProperty
```

### 2. Install dependencies

```powershell
composer install
npm ci
```

### 3. Configure the environment

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Create a MySQL or MariaDB database named `gharproperty_pk`, then update the `DB_*` values in `.env` when necessary.

### 4. Prepare the application

```powershell
php artisan migrate
php artisan storage:link
npm run build
```

### 5. Start the application

```powershell
php artisan serve --host=127.0.0.1 --port=8000
```

Open:

```text
http://127.0.0.1:8000
```

For frontend development with live reload, run this in a second terminal:

```powershell
npm run dev
```

Continue using the Laravel URL on port `8000`.

## Optional local demonstration data

Demonstration seeding is blocked outside local and testing environments and requires an explicit opt-in and strong password.

> `migrate:fresh` deletes all existing database tables and data. Use it only with a disposable local database.

```powershell
$env:APP_ENV = "local"
$env:ALLOW_DEMO_SEEDING = "true"
$env:DEMO_PASSWORD = "PortfolioDemo!2026"

try {
    php artisan migrate:fresh --seed --force --no-interaction
    php artisan storage:link
}
finally {
    Remove-Item Env:APP_ENV -ErrorAction SilentlyContinue
    Remove-Item Env:ALLOW_DEMO_SEEDING -ErrorAction SilentlyContinue
    Remove-Item Env:DEMO_PASSWORD -ErrorAction SilentlyContinue
}
```

### Demo accounts

All demonstration accounts use the password `PortfolioDemo!2026`.

| Role | Email |
|---|---|
| Administrator | `admin@example.com` |
| Property seller | `seller@gharproperty.test` |
| Property agent | `agent@gharproperty.test` |
| Buyer | `buyer@gharproperty.test` |
| Investor | `investor@gharproperty.test` |

## Verification

```powershell
php artisan optimize:clear
php artisan test
.\vendor\bin\pint.bat --test
npm run lint
npm run build
composer validate --strict
composer audit --locked
npm audit --audit-level=moderate
```

Last locally verified result:

- 63 tests passed
- 385 assertions passed
- Laravel Pint passed across 109 files
- ESLint passed
- Vite production build completed

## Production checklist

- Set `APP_ENV=production`
- Set `APP_DEBUG=false`
- Use a secure production `APP_KEY`
- Use dedicated database credentials
- Configure HTTPS and secure cookies
- Configure a production mail provider
- Configure a queue worker for asynchronous jobs
- Keep `ALLOW_DEMO_SEEDING=false`
- Keep `DEMO_PASSWORD` empty
- Run `php artisan optimize`
- Serve the application from the `public/` directory
- Never commit `.env`, logs, runtime uploads, dependencies, or generated build files

## Author and copyright

**Muhammad Ali Nawaz**  
PHP / Laravel Developer

Copyright © 2026 Muhammad Ali Nawaz. All project source code and original project assets are released under the MIT License.

## License

This project is open-source software licensed under the [MIT License](LICENSE).
