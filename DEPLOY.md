# Production Deploy Runbook — Laravel CDN (Internal)

## Prerequisites

- PHP 8.4+ with extensions: `gd`, `mbstring`, `openssl`, `pdo`, `fileinfo`
- Composer 2.x
- Node.js 20+ and npm (for Breeze asset build)
- MySQL 8+ (recommended) or PostgreSQL
- Web server (Nginx/Apache) pointing document root to `public/`

## Environment

Copy `.env.example` to `.env` and set at minimum:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://cdn.example.com
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=laravel_cdn
DB_USERNAME=...
DB_PASSWORD=...

CDN_URL=https://cdn.example.com
SANCTUM_STATEFUL_DOMAINS=cdn.example.com
SESSION_SECURE_COOKIE=true
API_REGISTER_ENABLED=false

SEED_ADMIN_PASSWORD=<strong-random-password>
SEED_USER_PASSWORD=<strong-random-password>
```

**Never** run `php artisan db:seed` in production without setting `SEED_ADMIN_PASSWORD` and `SEED_USER_PASSWORD`.

## Deploy Steps

1. Pull release tag / branch
2. `composer install --no-dev --optimize-autoloader`
3. `npm ci && npm run build`
4. `php artisan key:generate` (first deploy only)
5. `php artisan migrate --force`
6. `php artisan storage:link`
7. `php artisan config:cache`
8. `php artisan route:cache`
9. `php artisan view:cache`
10. Ensure `storage/` and `bootstrap/cache/` are writable by the web server user

## Post-deploy

- Log in as admin and change default passwords if seed was used in staging
- Verify upload, edit, delete from dashboard
- Verify private file download requires authentication
- Set up queue worker if async jobs are added later: `php artisan queue:work --daemon`

## Rollback

1. Revert code to previous release
2. `php artisan migrate:rollback` (only if migration is reversible)
3. `php artisan config:cache && php artisan route:cache`

## Health Checks

- `GET /up` — Laravel health
- `GET /api/health` — API health JSON
