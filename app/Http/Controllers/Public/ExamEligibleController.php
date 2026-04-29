<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Sangha;
use App\Support\ExamEligibleSnapshot;
use Illuminate\View\View;

class ExamEligibleController extends Controller
{
    public function index(): View
    {
        $publishedIds = ExamEligibleSnapshot::publishedExamIds();

        $exams = Exam::query()
            ->where('is_active', true)
            ->with('examType')
            ->orderByRaw('exam_date IS NULL')
            ->orderBy('exam_date')
            ->orderBy('name')
            ->get();

        return view('website.exam-eligible-index', compact('exams', 'publishedIds'));
    }

    public function show(Exam $exam): View
    {
        $exam->loadMissing('examType');
        $snapshot = ExamEligibleSnapshot::forExamId($exam->id);
        if ($snapshot) {
            $liveByDesk = Sangha::query()
                ->where('exam_id', $exam->id)
                ->where('workflow_status', Sangha::STATUS_APPROVED)
                ->whereNotNull('desk_number')
                ->with('monastery')
                ->get()
                ->keyBy(fn (Sangha $sangha) => (string) $sangha->desk_number);

            $snapshot['candidates'] = collect($snapshot['candidates'] ?? [])
                ->map(function (array $row) use ($liveByDesk): array {
                    $desk = (string) ($row['desk_number'] ?? '');
                    $live = $desk !== '' ? $liveByDesk->get($desk) : null;
                    if (! $live) {
                        return $row;
                    }

                    $row['eligible_roll_number'] = filled($row['eligible_roll_number'] ?? null)
                        ? (string) $row['eligible_roll_number']
                        : (string) ($live->eligible_roll_number ?? '');
                    $row['father_name'] = filled($row['father_name'] ?? null)
                        ? (string) $row['father_name']
                        : (string) ($live->father_name ?? '');
                    $row['nrc_number'] = filled($row['nrc_number'] ?? null)
                        ? (string) $row['nrc_number']
                        : (string) ($live->nrc_number ?? '');
                    $row['name'] = filled($row['name'] ?? null)
                        ? (string) $row['name']
                        : (string) ($live->name ?? '');
                    $row['monastery_name'] = filled($row['monastery_name'] ?? null)
                        ? (string) $row['monastery_name']
                        : (string) ($live->monastery?->name ?? '—');
                    $row['user_id'] = filled($row['user_id'] ?? null)
                        ? (string) $row['user_id']
                        : (string) ($live->username ?? '');

                    return $row;
                })
                ->all();
        }

        return view('website.exam-eligible', compact('exam', 'snapshot'));
    }
}
