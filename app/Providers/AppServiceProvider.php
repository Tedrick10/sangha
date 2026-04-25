<?php

namespace App\Providers;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Production must never use Vite dev/HMR (public/hot). If that file is left on the server,
        // browsers load @vite/client + virtual app.css/app.js and often hit ERR_SSL_PROTOCOL_ERROR.
        if ($this->app->environment('production')) {
            Vite::useHotFile(storage_path('framework/vite.hot'));
        }

        // `php artisan serve` (SAPI cli-server) has no TLS. If APP_URL is https (e.g. production domain),
        // `URL::forceScheme('https')` below still turns asset URLs into https://127.0.0.1:8000/build/…
        // (current host + forced scheme). The server logs "Unsupported SSL request"; CSS/JS fail to load.
        if (PHP_SAPI === 'cli-server') {
            URL::forceScheme('http');

            return;
        }

        // Local PHP-FPM/Valet over plain HTTP on loopback: same host/scheme mismatch as above.
        if (! $this->app->runningInConsole() && $this->app->environment('local')) {
            $request = request();
            if ($request && ! $request->secure()) {
                $h = strtolower($request->getHost());
                if (in_array($h, ['127.0.0.1', 'localhost', '[::1]'], true)) {
                    URL::forceScheme('http');

                    return;
                }
            }
        }

        $appUrl = config('app.url');
        if (is_string($appUrl) && str_starts_with($appUrl, 'https://')) {
            URL::forceScheme('https');
        }

        $this->assertProductionDeployRequirements();
    }

    /**
     * Fail fast on cPanel / shared hosting with log-friendly messages (common causes of HTTP 500).
     */
    private function assertProductionDeployRequirements(): void
    {
        if ($this->app->runningInConsole() || ! $this->app->environment('production')) {
            return;
        }

        $key = (string) config('app.key');
        if ($key === '') {
            Log::critical('APP_KEY is empty. Run: php artisan key:generate');

            throw new RuntimeException(
                'APP_KEY is empty. SSH into the server, run `php artisan key:generate`, save the new key in .env, then reload the site.'
            );
        }

        if (! is_file(public_path('build/manifest.json'))) {
            Log::critical('Missing Vite manifest at public/build/manifest.json. Run `npm ci && npm run build` and deploy the public/build directory.');

            throw new RuntimeException(
                'Missing public/build/manifest.json (compiled CSS/JS). On your computer run `npm ci && npm run build`, commit or upload the entire public/build folder, then reload.'
            );
        }

        try {
            if (! Schema::hasTable('migrations')) {
                Log::critical('Database has no migrations table. Run: php artisan migrate --force');

                throw new RuntimeException(
                    'The database has not been migrated. From the project root run `php artisan migrate --force`, then reload.'
                );
            }
        } catch (QueryException $e) {
            Log::critical('Database connection failed.', ['exception' => $e->getMessage()]);

            throw new RuntimeException(
                'Cannot connect to the database. Verify DB_CONNECTION, DB_HOST, DB_DATABASE, DB_USERNAME, and DB_PASSWORD in .env (cPanel: enable pdo_mysql for your PHP version).',
                0,
                $e
            );
        }
    }
}
