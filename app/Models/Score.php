<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Score extends Model
{
    use HasFactory;

    protected $fillable = [
        'sangha_id',
        'subject_id',
        'exam_id',
        'desk_number',
        'value',
        'moderation_decision',
        'father_name',
        'nrc_number',
        'candidate_ref',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'float',
        ];
    }

    public function sangha(): BelongsTo
    {
        return $this->belongsTo(Sangha::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Latest score row per sangha (by highest score id): father, NRC, candidate ref, desk number.
     *
     * @return Collection<int, array{father_name: ?string, nrc_number: ?string, candidate_ref: ?string, desk_number: ?string}>
     */
    public static function latestScoreRowMetaBySanghaIds(array $sanghaIds): Collection
    {
        $ids = array_values(array_unique(array_filter($sanghaIds)));
        if ($ids === []) {
            return collect();
        }

        $rows = static::query()
            ->whereIn('sangha_id', $ids)
            ->orderByDesc('id')
            ->get(['id', 'sangha_id', 'father_name', 'nrc_number', 'candidate_ref', 'desk_number']);

        $out = collect();
        foreach ($rows as $row) {
            if (! $out->has($row->sangha_id)) {
                $out->put($row->sangha_id, [
                    'father_name' => $row->father_name,
                    'nrc_number' => $row->nrc_number,
                    'candidate_ref' => $row->candidate_ref,
                    'desk_number' => $row->desk_number !== null && $row->desk_number !== '' ? (string) $row->desk_number : null,
                ]);
            }
        }

        return $out;
    }
}
