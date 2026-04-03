<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetAdminLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('admin.*')) {
            $locale = session('app_locale') ?? session('admin_locale');
            if ($locale) {
                app()->setLocale($locale);
            }
        }

        return $next($request);
    }
}
