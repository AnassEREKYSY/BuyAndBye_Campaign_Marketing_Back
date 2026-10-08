# Kickback API

Kickback is an influencer campaign platform: brands publish campaigns, creators apply, and every
accepted creator gets a tracked link and a promo code. Clicks are counted and turned into tiered payouts.

This repository is the Laravel 12 REST API. The web app lives in `BuyAndBye_Campaign_Marketing_Front`.

## Stack

- Laravel 12, PHP 8.4, Sanctum (bearer tokens)
- PostgreSQL (Supabase in production)
- Clean Architecture: `app/Domain` (contracts), `app/Application/UseCases`, `app/Infrastructure`
- Swagger docs via l5-swagger (`/api/documentation` locally)

## Run locally

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec api php artisan key:generate
docker compose exec api php artisan migrate:fresh --seed
```

API: http://localhost:8000/api/health

### Demo accounts (password: `password`)

| Email | Role |
|---|---|
| brand@kickback.demo | Brand (Maison Nour) |
| coffee@kickback.demo | Brand (Atlas Coffee) |
| creator@kickback.demo | Creator (Lina Benali) |
| admin@kickback.demo | Admin |

The seeder (`database/seeders/DemoSeeder.php`) creates campaigns, collaborations, ~8,000 clicks over
the last months, payouts, messages and notifications so every screen has data.

## Main endpoints

All under `/api/v1`, bearer token required except auth and the redirect.

- `POST auth/register`, `POST auth/login`, `GET auth/me`
- `GET t/{code}`: public tracked-link redirect (counts the click)
- Campaigns, tiers, applications, collaborations, payouts, notifications, conversations (see `routes/api.php`)
- `GET brand/analytics/overview?days=7|30|90`: totals, daily clicks, campaign and creator rankings, sources, devices, payouts
- `GET influencer/analytics/overview?days=7|30|90`: clicks, every link and promo code with its numbers, earnings by status and month

## Deploy on Vercel + Supabase

### 1. Supabase

1. Create a project (EU region).
2. **Project Settings → Database → Connection string**: copy the **Transaction pooler** URI (port `6543`).
3. **Storage**: create a **public** bucket named `uploads` (avatars, logos, product images).
4. **Project Settings → API**: copy the project URL and the `service_role` key.

### 2. Create the tables and demo data

From this folder, with Docker running (uses the **Session pooler** URI, port `5432`, for migrations):

```bash
docker compose run --rm --no-deps \
  -e DATABASE_URL="postgresql://postgres.xxxx:PASSWORD@aws-0-eu-xxx.pooler.supabase.com:5432/postgres" \
  -e DB_SSLMODE=require \
  api php artisan migrate:fresh --seed --force
```

### 3. Vercel project for the API

1. **Add New → Project**, import this repository. Framework preset: **Other**. Root directory: `/`.
2. Environment variables:

| Name | Value |
|---|---|
| `APP_KEY` | output of `php artisan key:generate --show` |
| `APP_URL` | the API URL, e.g. `https://kickback-api.vercel.app` |
| `DATABASE_URL` | Supabase **transaction pooler** URI (port 6543) |
| `CORS_ALLOWED_ORIGINS` | the web app URL, e.g. `https://kickback.vercel.app` |
| `SUPABASE_URL` | `https://xxxx.supabase.co` |
| `SUPABASE_SERVICE_ROLE_KEY` | the service role key |

Everything else (stateless cache/session, logs to stderr, `/tmp` caches, prepared-statement
emulation for the pooler) is already set in `vercel.json`. PHP runs on the community runtime
`vercel-php@0.8.0` (PHP 8.4).

3. Deploy, then open `https://<your-api>.vercel.app/api/health`.

### Notes

- Free plans are fine for a personal demo. Supabase pauses a free project after a week without traffic; restore it from the dashboard.
- Uploads go to Supabase Storage when `SUPABASE_URL` and `SUPABASE_SERVICE_ROLE_KEY` are set, and to `storage/app/public` otherwise.
- Queues run synchronously (`QUEUE_CONNECTION=sync`), real-time broadcasting is off.
