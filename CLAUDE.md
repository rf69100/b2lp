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

# Static analysis (PHPStan level 8 via Larastan)
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
Sanctum token-based auth. Tokens must be sent as a Bearer token in the `Authorization` header. Routes requiring auth are grouped under `middleware('auth:sanctum')` in `routes/api.php`. The API is stateless — session cookies are disabled for `/api/*` via `$middleware->statefulApi()`.

**Token format inconsistency:** `POST /api/register` returns `{"access_token": "...", "token_type": "Bearer"}`, while `POST /api/login` returns the plain token string directly (not wrapped in JSON).

### API Endpoints

| Method | URL | Auth | Description |
|--------|-----|------|-------------|
| POST | `/api/register` | No | Create account, returns `access_token` |
| POST | `/api/login` | No | Login, returns plain token string |
| POST | `/api/user/logout` | Yes | Deletes all user tokens |
| GET | `/api/user` | Yes | Returns authenticated user info |
| GET | `/api/billets` | No | List all posts (no comments) |
| GET | `/api/billets/{id}` | Yes | Single post with embedded comments and author names |
| POST | `/api/commentaires` | Yes | Create a comment — body: `COM_CONTENU`, `billet_id`, `user_id`; returns `CommentaireResource` (Date, Auteur, Contenu) |

### Middleware Stack (`bootstrap/app.php`)
- `ForceSubpathUrl` — prepended, handles subpath URL rewriting
- `SecurityHeadersMiddleware` — appended, rewrites all response cookies to enforce `HttpOnly=true`, `SameSite=Lax`, and `Secure=true` only in `production` (false otherwise)
- Global `NotFoundHttpException` handler returns `{"message": "Ressource non trouvée."}` with HTTP 404 as JSON

### Data Model
Three main models with French column naming conventions:
- `Billet` — blog posts (`BIL_DATE`, `BIL_TITRE`, `BIL_CONTENU`), hasMany `Commentaire`
- `Commentaire` — comments (`COM_DATE`, `COM_CONTENU`, `billet_id`, `user_id`), belongsTo `User`
- `User` — standard Laravel user with Sanctum tokens

### API Resources
Two distinct resources for `Billet`:
- `BilletsResource` — used in list (`GET /billets`): returns Date, Titre, Contenu only
- `BilletResource` — used in detail (`GET /billets/{id}`): returns Date, Titre, Contenu + `Commentaires` via `CommentaireResource::collection()`, eager-loaded with `commentaires.user`

### Form Requests
`StoreCommentaireRequest` has three notable behaviors:
- Validates that `billet_id` exists in the `billets` table
- Validates that `user_id` matches `Auth::id()` (prevents posting comments as another user)
- Overrides `failedValidation()` to always return JSON (`{"success": false, "message": "Validation errors", "data": {...}}`) instead of Laravel's default redirect

`COM_DATE` is **not** accepted from the client — it is set server-side in `CommentaireController::store()` via `now()->toDateString()`.

`StoreBilletRequest` is an unimplemented stub (`authorize()` returns `false`). Most CRUD methods beyond `index`, `show`, and `store` are also stubs.

### Logging
Application errors use a custom `projectLog` channel → `storage/logs/project.log` (level: error). Usage: `Log::channel('projectLog')->error('...')`. Standard Laravel log goes to `storage/logs/laravel.log`.

### Database
MariaDB in development and production. The `phpunit.xml` SQLite/in-memory config is intentionally commented out — tests run against a real database connection by default.
