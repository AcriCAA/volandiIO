# VolandiIO

VolandiIO is a Laravel app that watches specific flights (route, dates, cabin class) and
alerts you by email as soon as the live price drops to or below a target you set.

Live pricing comes from the [Amadeus for Developers](https://developers.amadeus.com) Flight
Offers Search API. Kayak and Google Flights don't offer a public API for this kind of
automated price search, which is why this app is built against Amadeus instead — the closest
provider with a free self-service tier.

## How it works

1. You create a **Flight Alert**: origin/destination airport codes, departure (and optional
   return) date, cabin class, number of adults, and a target price/currency.
2. A scheduled job (`flights:check-alerts`, run hourly by default) dispatches a queued job per
   active alert.
3. Each job (`App\Jobs\CheckFlightAlertPrice`) calls the Amadeus Flight Offers Search API via
   `App\Services\FlightPriceService`, records the cheapest offer found in `price_checks`, and
   updates the alert's `last_price`.
4. If the price is at or below your target, you get an email (and an in-app database
   notification) via `App\Notifications\FlightPriceAlertNotification`, and the alert is marked
   as triggered so you're not notified repeatedly for the same drop.

## Getting started

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite   # if using the default sqlite setup
php artisan migrate
npm install
npm run build
```

### Redis

Queues are managed by [Laravel Horizon](https://laravel.com/docs/horizon), which requires
Redis. Make sure a Redis server is running and reachable via `REDIS_HOST`/`REDIS_PORT` in
your `.env` (defaults to `127.0.0.1:6379`).

### Amadeus API credentials

1. Sign up for a free account at https://developers.amadeus.com.
2. Create an app to get a **test environment** API Key and API Secret (the test environment
   has generous free-tier limits and is what `AMADEUS_BASE_URL` defaults to).
3. Set these in your `.env`:

   ```
   AMADEUS_CLIENT_ID=your-api-key
   AMADEUS_CLIENT_SECRET=your-api-secret
   AMADEUS_BASE_URL=https://test.api.amadeus.com
   ```

   For production-grade pricing/quotas, switch to `https://api.amadeus.com` once you've
   upgraded to a paid Amadeus plan.

### Running it locally

You need these running for alerts to actually get checked and delivered:

```bash
php artisan serve          # the web app
php artisan horizon        # processes CheckFlightAlertPrice jobs (needs Redis)
php artisan schedule:work  # triggers flights:check-alerts hourly (local dev only)
```

In production, replace `schedule:work` with a real cron entry running
`php artisan schedule:run` every minute, and run `php artisan horizon` under a process
supervisor (e.g. Supervisor or systemd) so it restarts if it dies. See
[Laravel Horizon's deployment docs](https://laravel.com/docs/horizon#deploying-horizon) for a
sample Supervisor config, and call `php artisan horizon:terminate` during deploys so Horizon
restarts with the new code.

The Horizon dashboard is available at `/horizon`. In the `local` environment any visitor can
open it; in other environments only users whose email is listed in `HORIZON_ADMIN_EMAILS`
(comma-separated, see `.env`) can, via the `viewHorizon` gate in
`App\Providers\HorizonServiceProvider`.

You can also trigger a check run manually at any time:

```bash
php artisan flights:check-alerts
```

### Mail

`MAIL_MAILER=log` (the default) writes alert emails to `storage/logs/laravel.log` instead of
sending them, which is convenient for local development. Configure a real mailer (Postmark,
SES, SMTP, etc.) in `.env` before deploying.

## Testing

```bash
php artisan test
```

Amadeus calls are faked in tests (`tests/Feature/FlightAlertTest.php`), so no real API
credentials or network access are required to run the suite.
