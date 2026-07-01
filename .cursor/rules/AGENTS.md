# Laravel CDN — Cursor Agent Index

> Rules aktif di `.cursor/rules/*.mdc`. Baca README.md untuk setup & API docs.

---

## Rules

| Rule | Scope |
|------|-------|
| `laravel-cdn-project.mdc` | Always — project context, stack, architecture DO/DON'T |
| `laravel-boost.mdc` | Always — Laravel Boost MCP, PHPUnit, Pint, Tailwind |
| `ponytail.mdc` | Always — minimal diff, reuse-first coding |

---

## Quick Reference

**Stack:** Laravel 12 · PHP 8.4 · MySQL · Redis · Breeze · Sanctum · Spatie Permission · Intervention Image · Tailwind 3 · Alpine 3

**Pattern:** Controllers (web + API) → `FileService` (logic) → Eloquent models · Policies for auth · Query Builder for API filters

**Dev:** `composer run dev` · `php artisan test` · `vendor/bin/pint --dirty`

**API:** `/api/auth/*` · `/api/files/*` · Sanctum bearer or API key
