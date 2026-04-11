<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\Exam;
use App\Models\Monastery;
use App\Models\Sangha;
use App\Models\Website;
use App\Support\WebsitePagePresentation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        $upcomingExams = Exam::query()
            ->where('is_active', true)
            ->whereNotNull('exam_date')
            ->whereDate('exam_date', '>=', now()->toDateString())
            ->orderBy('exam_date')
            ->with(['monastery', 'examType'])
            ->take(6)
            ->get();

        if ($upcomingExams->isEmpty()) {
            $upcomingExams = Exam::query()
                ->where('is_active', true)
                ->orderByDesc('exam_date')
                ->with(['monastery', 'examType'])
                ->take(6)
                ->get();
        }

        $stats = [
            'monasteries' => Monastery::where('is_active', true)->count(),
            'sanghas' => Sangha::where('is_active', true)->count(),
            'exams' => Exam::where('is_active', true)->count(),
        ];

        $featuredPages = Website::query()
            ->where('type', 'page')
            ->where('is_published', true)
            ->whereNotIn('slug', ['home', 'login', 'footer', 'registration'])
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return view('website.home', compact(
            'upcomingExams',
            'stats',
            'featuredPages'
        ));
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
        $sanghaFieldMeta = CustomField::sanghaDefinitionsBySlug();
        $monasteries = Monastery::where('is_active', true)->orderBy('name')->get();
        $exams = Exam::where('is_active', true)->orderBy('exam_date', 'desc')->orderBy('name')->get();

        return view('website.login', compact(
            'monasteryCustomFields',
            'sanghaCustomFields',
            'sanghaFieldMeta',
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
        if (! $page) {
            abort(404);
        }

        $scheduleExams = null;
        if ($slug === 'exam-schedule') {
            $scheduleExams = Exam::query()
                ->where('is_active', true)
                ->orderByRaw('CASE WHEN exam_date IS NULL THEN 1 ELSE 0 END')
                ->orderBy('exam_date')
                ->orderBy('name')
                ->with(['monastery', 'examType'])
                ->get();
        }

        $pageTheme = WebsitePagePresentation::theme($slug);
        $pageAccentBar = WebsitePagePresentation::accentBarClasses($slug);
        $useFallbackContent = WebsitePagePresentation::isPlaceholderContent($page->content, $page->title);

        return view('website.page', array_merge(
            compact('page', 'scheduleExams', 'pageTheme', 'pageAccentBar', 'useFallbackContent'),
            WebsitePagePresentation::extras($slug)
        ));
    }
}
