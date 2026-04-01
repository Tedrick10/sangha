<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'permissions'];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function hasPermission(string $permission): bool
    {
        $permissions = $this->permissions ?? [];
        return in_array($permission, $permissions, true);
    }

    public static function availablePermissions(): array
    {
        return config('permissions', []);
    }

    public static function permissionSlug(string $resource, string $action): string
    {
        return $resource . '.' . $action;
    }

    public static function allPermissionSlugs(): array
    {
        $slugs = [];
        foreach (array_keys(static::availablePermissions()) as $resource) {
            foreach (['create', 'read', 'update', 'delete'] as $action) {
                $slugs[] = static::permissionSlug($resource, $action);
            }
        }
        return $slugs;
    }
}
