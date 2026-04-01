<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetWebsiteLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->routeIs('admin.*') && session()->has('website_locale')) {
            app()->setLocale(session('website_locale'));
        }

        return $next($request);
    }
}
