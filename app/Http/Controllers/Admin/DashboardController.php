<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Monastery;
use App\Models\Sangha;
use App\Models\Score;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'monasteries' => Monastery::count(),
            'sanghas' => Sangha::count(),
            'exams' => Exam::count(),
            'scores' => Score::count(),
            'users' => User::count(),
        ];

        $recentMonasteries = Monastery::latest()->limit(5)->get(['id', 'name', 'username', 'created_at']);
        $recentExams = Exam::with('monastery:id,name', 'examType:id,name')
            ->latest('exam_date')
            ->limit(5)
            ->get();
        $recentScores = Score::with(['sangha:id,name', 'subject:id,name', 'exam:id,name'])
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentMonasteries', 'recentExams', 'recentScores'));
    }
}
