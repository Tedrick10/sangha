<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminHasPermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();
        $routeName = is_object($route) ? (string) ($route->getName() ?? '') : '';
        if ($routeName === '') {
            return $next($request);
        }

        $requiredPermission = $this->requiredPermissionForRoute($routeName);
        if ($requiredPermission === null) {
            return $next($request);
        }

        $user = $request->user();
        if (! $user || ! method_exists($user, 'hasPermission') || ! $user->hasPermission($requiredPermission)) {
            abort(403, t('permission_denied', 'You do not have permission to perform this action.'));
        }

        return $next($request);
    }

    private function requiredPermissionForRoute(string $routeName): ?string
    {
        $map = config('admin-route-permissions', []);
        foreach ($map as $pattern => $permission) {
            if (Str::is($pattern, $routeName)) {
                return is_string($permission) ? $permission : null;
            }
        }

        return null;
    }
}
