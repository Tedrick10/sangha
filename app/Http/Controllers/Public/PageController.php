<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\Exam;
use App\Models\Monastery;
use App\Models\Website;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        $page = Website::getPageBySlug('home');
        return view('website.home', ['page' => $page]);
    }

    public function login(): View|RedirectResponse
    {
        if (Auth::guard('monastery')->check()) {
            return redirect()->route('monastery.dashboard');
        }
        if (Auth::guard('student')->check()) {
            return redirect()->route('sangha.dashboard');
        }

        $monasteryCustomFields = CustomField::forEntity('monastery')
            ->where('is_built_in', false)
            ->get();
        $sanghaCustomFields = CustomField::forEntity('sangha')
            ->where('is_built_in', false)
            ->get();
        $monasteries = Monastery::where('is_active', true)->orderBy('name')->get();
        $exams = Exam::where('is_active', true)->orderBy('exam_date', 'desc')->orderBy('name')->get();

        return view('website.login', compact(
            'monasteryCustomFields',
            'sanghaCustomFields',
            'monasteries',
            'exams'
        ));
    }

    public function show(string $slug): View|RedirectResponse
    {
        if ($slug === 'registration') {
            return redirect()->route('website.register');
        }

        $page = Website::getPageBySlug($slug);
        if (!$page) {
            abort(404);
        }
        return view('website.page', ['page' => $page]);
    }
}
