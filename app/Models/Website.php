<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'type',
        'is_published',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public static function getPageBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)
            ->where('type', 'page')
            ->where('is_published', true)
            ->first();
    }

    public static function getBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)
            ->where('is_published', true)
            ->first();
    }
}
