<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetAdminLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('admin.*') && session()->has('admin_locale')) {
            app()->setLocale(session('admin_locale'));
        }

        return $next($request);
    }
}
