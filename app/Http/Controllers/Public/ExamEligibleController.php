<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Exam;
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

        return view('website.exam-eligible', compact('exam', 'snapshot'));
    }
}
