<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

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
    }
}
