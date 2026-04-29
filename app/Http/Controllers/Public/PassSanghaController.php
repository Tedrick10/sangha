<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ExamType;
use App\Models\SiteSetting;
use App\Support\PassSanghaListDisplay;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PassSanghaController extends Controller
{
    public function index(Request $request): View
    {
        $snapshotRaw = SiteSetting::get('pass_sanghas_snapshot');
        $snapshot = $snapshotRaw ? json_decode($snapshotRaw, true) : null;

        $rows = PassSanghaListDisplay::enrichSnapshotRows(
            collect($snapshot['pass_sanghas'] ?? [])
        )->values();
        $rows = PassSanghaListDisplay::uniqueByExamRoll($rows);

        $years = $rows
            ->pluck('exam_year')
            ->filter(fn ($year) => $year !== null && (string) $year !== '')
            ->map(fn ($year) => (string) $year)
            ->unique()
            ->sortDesc()
            ->values();

        $selectedYear = $request->filled('year') ? (string) $request->query('year') : null;
        if ($selectedYear !== null && ! $years->contains($selectedYear)) {
            $selectedYear = null;
        }

        $rowsByYear = $selectedYear
            ? $rows->where('exam_year', $selectedYear)->values()
            : collect();

        $catalogTypes = ExamType::query()
            ->whereIn('name', ExamType::CANONICAL_NAME_ORDER)
            ->orderByCanonical()
            ->get(['id', 'name'])
            ->map(fn ($type) => ['id' => (int) $type->id, 'name' => (string) $type->name])
            ->values();

        $examTypesInYear = $rowsByYear
            ->map(function (array $row) use ($catalogTypes) {
                $id = (int) ($row['exam_type_id'] ?? 0);
                $name = (string) ($row['level_name'] ?? '');
                if ($id > 0) {
                    $found = $catalogTypes->firstWhere('id', $id);
                    return $found ? ['id' => $id, 'name' => (string) $found['name']] : null;
                }
                if ($name === '') {
                    return null;
                }
                $found = $catalogTypes->firstWhere('name', $name);
                return $found ? ['id' => (int) $found['id'], 'name' => (string) $found['name']] : null;
            })
            ->filter()
            ->unique('id')
            ->values();

        $selectedExamTypeId = $request->filled('exam_type_id') ? (int) $request->query('exam_type_id') : null;
        if ($selectedExamTypeId !== null && ! $examTypesInYear->firstWhere('id', $selectedExamTypeId)) {
            $selectedExamTypeId = null;
        }

        $selectedExamType = $selectedExamTypeId
            ? $examTypesInYear->firstWhere('id', $selectedExamTypeId)
            : null;
        $selectedExamTypeName = (string) ($selectedExamType['name'] ?? '');

        $rowsByType = $selectedExamTypeId
            ? $rowsByYear->filter(function (array $row) use ($selectedExamTypeId, $selectedExamTypeName) {
                $rid = (int) ($row['exam_type_id'] ?? 0);
                if ($rid > 0) {
                    return $rid === $selectedExamTypeId;
                }
                return (string) ($row['level_name'] ?? '') === $selectedExamTypeName;
            })->values()
            : collect();

        $examOptions = $rowsByType
            ->map(function (array $row) {
                $id = (int) ($row['exam_id'] ?? 0);
                if ($id < 1) {
                    return null;
                }
                return [
                    'id' => $id,
                    'label' => (string) ($row['exam_name'] ?? ('Exam #'.$id)),
                ];
            })
            ->filter()
            ->unique('id')
            ->sortBy('label')
            ->values();

        $selectedExamId = $request->filled('exam_id') ? (int) $request->query('exam_id') : null;
        if ($selectedExamId !== null && ! $examOptions->firstWhere('id', $selectedExamId)) {
            $selectedExamId = null;
        }

        $search = trim((string) $request->query('q', ''));

        $passSanghas = $selectedExamId
            ? $rowsByType->filter(fn (array $row) => (int) ($row['exam_id'] ?? 0) === $selectedExamId)->values()
            : collect();

        if ($search !== '') {
            $needle = mb_strtolower($search);
            $passSanghas = $passSanghas
                ->filter(function (array $row) use ($needle) {
                    $name = mb_strtolower((string) ($row['name'] ?? ''));
                    return str_contains($name, $needle);
                })
                ->values();
        }

        $selectedExam = $selectedExamId ? $examOptions->firstWhere('id', $selectedExamId) : null;
        $generatedAt = $snapshot['generated_at'] ?? null;

        return view('website.pass-sanghas', compact(
            'generatedAt',
            'years',
            'selectedYear',
            'examTypesInYear',
            'selectedExamTypeId',
            'selectedExamType',
            'examOptions',
            'selectedExamId',
            'selectedExam',
            'passSanghas',
            'search'
        ));
    }
}
