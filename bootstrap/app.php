<?php

use App\Http\Middleware\EnsureAdminAuthenticated;
use App\Http\Middleware\SetAdminLocale;
use App\Http\Middleware\SetWebsiteLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // cPanel / reverse proxies: trust X-Forwarded-* so HTTPS and URLs are correct.
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'admin.auth' => EnsureAdminAuthenticated::class,
            'admin.locale' => SetAdminLocale::class,
            'website.locale' => SetWebsiteLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
