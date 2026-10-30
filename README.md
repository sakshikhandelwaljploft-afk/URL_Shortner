# Multi-Tenant URL Shortener

A multi-tenant URL shortening service built with **Laravel 11** and **PHP 8.2+**. Provides role-based link shortening, click tracking, company isolation, and an invitation system for onboarding team members.

---

## Features

- **Multi-Tenant Isolation**: Supports multiple organizations (companies) with scoped data access.
- **Role-Based Permissions**:
  - `SuperAdmin`: System administrator (seeded via raw SQL; cannot create or view company short links).
  - `Admin`: Manages company team members and views external partner links.
  - `Sales` & `Manager`: Holds link creation permissions for company shortlinks.
  - `Member`: Views shared organization links created by team members.
- **Short URL Resolution**:
  - Auto-generated 6-character random slugs or optional custom aliases.
  - Redirect click tracking counter.
  - Protected redirect resolution (authenticated access required).
- **Team Invitations**: Token-based invitation workflow to onboard new users into organizations.
- **Automated Tests**: 15 feature tests covering authorization policies, scoping rules, authentication, and resolution.

---

## Getting Started

### Prerequisites

- PHP >= 8.2 with `pdo_sqlite` or MySQL
- Composer 2.x

### Setup

1. **Install dependencies**:
   ```bash
   composer install
   ```

2. **Environment configuration**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database migrations & seeders**:
   ```bash
   touch database/database.sqlite
   php artisan migrate:fresh --seed
   ```

4. **Run the local dev server**:
   ```bash
   php artisan serve
   ```
   Open [http://localhost:8000](http://localhost:8000) (or via XAMPP at `http://localhost/url_shortener/`).

---

## Test Accounts

The database seeder provisions sample accounts for local testing (default password: `password123`):

| Role | Email | Company | Notes |
| :--- | :--- | :--- | :--- |
| **SuperAdmin** | `superadmin@example.com` | System | Seeded via raw SQL. System-level administration. |
| **Admin** | `admin.acme@example.com` | Acme Corporation | Views external shortlinks. |
| **Sales** | `sales.acme@example.com` | Acme Corporation | Can create and view short URLs. |
| **Manager** | `manager.acme@example.com` | Acme Corporation | Can create and view short URLs. |
| **Member** | `member1.acme@example.com` | Acme Corporation | Views links created by other members. |
| **Admin (Beta)**| `admin.beta@example.com` | Beta Industries | Cross-company verification account. |

---

## Running Tests

Run the full automated test suite (15 passed tests):

```bash
php artisan test
```

---

## AI Tools Usage Declaration

In accordance with the acceptable AI usage policy, AI assistance was utilized solely for syntax lookups and server configuration references:
- **ChatGPT**: Looked up Apache `.htaccess` rewrite rules and `RewriteBase` configuration for routing XAMPP subfolder requests cleanly without `/public`.
- **Cursor / Copilot**: Auto-complete and syntax lookup for standard Laravel validation rules (`alpha_num`, `unique:short_urls,code`).
- **PHP Docs / AI Lookup**: Checked syntax for PHP 8.2 Backed Enums (`Role::cases()`, `Role::values()`).

All core authorization policies, multi-tenant query scopes, database design, and test suites were logically structured, implemented, and tested manually.
