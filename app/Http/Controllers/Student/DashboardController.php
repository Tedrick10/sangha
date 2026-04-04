<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $sangha = Auth::guard('student')->user();
        $sangha->load(['monastery', 'exam', 'scores.subject']);
        $scores = $sangha->scores()->with('subject')->get();

        $subjectsCount = $scores->count();
        $numericScores = $scores->filter(fn ($s) => is_numeric($s->value));
        $averageScore = $numericScores->isNotEmpty()
            ? (float) $numericScores->avg('value')
            : null;

        return view('student.dashboard', compact('sangha', 'scores', 'subjectsCount', 'averageScore'));
    }
}
