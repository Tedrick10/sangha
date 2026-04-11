<?php

namespace App\Models;

use App\Traits\HasCustomFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamType extends Model
{
    /**
     * Display / pick-list order for the five standard programmes.
     *
     * @var list<string>
     */
    public const CANONICAL_NAME_ORDER = [
        'မူလတန်း စာမေးပွဲ',
        'ဥပစာတန်း စာမေးပွဲ',
        'ပထမဆင့် စာမေးပွဲ',
        'ဒုတိယဆင့် စာမေးပွဲ',
        'တတိယဆင့် စာမေးပွဲ',
    ];

    use HasCustomFields, HasFactory;

    protected function getCustomFieldEntityType(): string
    {
        return 'exam_type';
    }

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'approved',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'approved' => 'boolean',
        ];
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'exam_type_id');
    }

    /**
     * Order rows: canonical five first (fixed sequence), then any others by name.
     */
    public function scopeOrderByCanonical(Builder $query): Builder
    {
        $names = self::CANONICAL_NAME_ORDER;
        if ($names === []) {
            return $query->orderBy('name');
        }

        $cases = [];
        $bindings = [];
        foreach ($names as $i => $name) {
            $cases[] = 'WHEN name = ? THEN ?';
            $bindings[] = $name;
            $bindings[] = $i + 1;
        }
        $sql = 'CASE '.implode(' ', $cases).' ELSE ? END';
        $bindings[] = 9999;

        return $query->orderByRaw($sql, $bindings)->orderBy('name');
    }
}
