<?php

namespace App\Support;

use App\Models\Exam;
use App\Models\Sangha;
use App\Models\SiteSetting;

class ExamEligibleSnapshot
{
    public const SETTING_KEY = 'exam_eligible_snapshots';

    /**
     * Capture seated candidates (desk assigned) for this exam and store for the public site.
     *
     * @return array{exam_id: int, exam_name: string, exam_date: ?string, generated_at: string, candidates: list<array{desk_number: int, user_id: string, name: string, monastery_name: string}>}
     */
    public static function upsertFromExam(Exam $exam): array
    {
        $exam->loadMissing('examType');

        $sanghas = Sangha::query()
            ->where('exam_id', $exam->id)
            ->whereNotNull('desk_number')
            ->with('monastery')
            ->orderBy('desk_number')
            ->get();

        $payload = [
            'exam_id' => $exam->id,
            'exam_name' => $exam->name,
            'exam_date' => $exam->exam_date?->toDateString(),
            'exam_type_name' => $exam->examType?->name,
            'desk_number_prefix' => $exam->desk_number_prefix,
            'generated_at' => now()->toDateTimeString(),
            'candidates' => $sanghas->map(static function (Sangha $s): array {
                return [
                    'desk_number' => (int) $s->desk_number,
                    'user_id' => (string) $s->username,
                    'name' => (string) $s->name,
                    'monastery_name' => $s->monastery?->name ?? '—',
                ];
            })->values()->all(),
        ];

        $raw = SiteSetting::get(self::SETTING_KEY);
        $all = $raw ? (json_decode($raw, true) ?: []) : [];
        $all[(string) $exam->id] = $payload;
        SiteSetting::set(self::SETTING_KEY, json_encode($all, JSON_UNESCAPED_UNICODE));

        return $payload;
    }

    /**
     * @return array{exam_id: int, exam_name: string, exam_date: ?string, generated_at: string, candidates: list<array<string, mixed>}|null
     */
    public static function forExamId(int $examId): ?array
    {
        $raw = SiteSetting::get(self::SETTING_KEY);
        if (! $raw) {
            return null;
        }
        $all = json_decode($raw, true) ?: [];
        $key = (string) $examId;

        return $all[$key] ?? null;
    }

    /**
     * Exam IDs that currently have a published snapshot.
     *
     * @return list<int>
     */
    public static function publishedExamIds(): array
    {
        $raw = SiteSetting::get(self::SETTING_KEY);
        if (! $raw) {
            return [];
        }
        $all = json_decode($raw, true) ?: [];

        return array_values(array_map('intval', array_keys($all)));
    }

    public static function removeForExam(int $examId): void
    {
        $raw = SiteSetting::get(self::SETTING_KEY);
        if (! $raw) {
            return;
        }
        $all = json_decode($raw, true) ?: [];
        $key = (string) $examId;
        unset($all[$key]);
        if ($all === []) {
            SiteSetting::set(self::SETTING_KEY, null);

            return;
        }
        SiteSetting::set(self::SETTING_KEY, json_encode($all, JSON_UNESCAPED_UNICODE));
    }
}
