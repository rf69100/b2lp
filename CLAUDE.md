# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**B2LP** is a Laravel 11 REST API backend for a blog application (Lyon Palme). It serves a React Native thin client. There is no traditional web frontend — Vite/Tailwind are present but the app is purely API-driven.

## Commands

```bash
# Start all services (API server + queue + log watcher + Vite)
composer run dev

# Run tests
php artisan test

# Run a single test
php artisan test --filter=TestName

# Static analysis (PHPStan level 8)
composer run phpstan

# Update baseline after intentional type issues
composer run phpstan:baseline

# Code formatting
./vendor/bin/pint

# Seed the database (run in order)
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=BilletSeeder
php artisan db:seed --class=CommentaireSeeder
```

## Architecture

### Authentication
Sanctum token-based auth. Tokens are returned as `auth_token` in the JSON response and must be sent as a Bearer token. Routes requiring auth are grouped under `middleware('auth:sanctum')` in `routes/api.php`. The API is stateless — session cookies are disabled for `/api/*` via `$middleware->statefulApi()`.

### Middleware Stack (`bootstrap/app.php`)
- `ForceSubpathUrl` — prepended, handles subpath URL rewriting
- `SecurityHeadersMiddleware` — appended, sets all security headers (CSP, X-Frame-Options, HSTS, cookie flags). HSTS is only applied in `production` environment. Cookie `Secure` flag follows the same condition.

### Data Model
Three main models with French column naming conventions:
- `Billet` — blog posts (`BIL_DATE`, `BIL_TITRE`, `BIL_CONTENU`), hasMany `Commentaire`
- `Commentaire` — comments (`COM_DATE`, `COM_CONTENU`, `billet_id`, `user_id`), belongsTo `User`
- `User` — standard Laravel user with Sanctum tokens

### API Resources & Form Requests
Controllers use dedicated classes in `app/Http/Resources/` (transform model output) and `app/Http/Requests/` (validate input). Add new endpoints following this pattern.

### Logging
Application errors use a custom `projectLog` channel → `storage/logs/project.log` (level: error). Usage: `Log::channel('projectLog')->error('...')`. Standard Laravel log goes to `storage/logs/laravel.log`.

### Database
MariaDB in development and production. The `phpunit.xml` SQLite/in-memory config is intentionally commented out — tests run against a real database connection by default.
