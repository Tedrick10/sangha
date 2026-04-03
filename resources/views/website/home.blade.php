@extends('website.layout')

@section('title', t('home'))

@section('content')
@php
    $pageLinkIcons = [
        'about' => 'information-circle',
        'contact' => 'envelope',
        'faq' => 'question-mark-circle',
        'privacy' => 'shield-check',
        'exam-schedule' => 'calendar',
        'results' => 'chart-bar',
        'guidelines' => 'clipboard-list',
        'syllabus' => 'academic-cap',
        'past-papers' => 'book-open',
        'news' => 'newspaper',
        'events' => 'megaphone',
        'gallery' => 'photograph',
        'terms-of-use' => 'scale',
        'accessibility' => 'sparkles',
        'donate' => 'heart',
        'resources' => 'folder',
        'volunteer' => 'users',
        'partners' => 'external-link',
        'sitemap' => 'map',
    ];

    $explorePalettes = [
        ['iconBg' => 'bg-amber-100 dark:bg-amber-900/50', 'iconTxt' => 'text-amber-800 dark:text-amber-300', 'ring' => 'ring-amber-200/70 dark:ring-amber-800/40', 'hoverBg' => 'hover:bg-amber-50/70 dark:hover:bg-amber-950/25'],
        ['iconBg' => 'bg-sky-100 dark:bg-sky-900/45', 'iconTxt' => 'text-sky-800 dark:text-sky-300', 'ring' => 'ring-sky-200/70 dark:ring-sky-800/40', 'hoverBg' => 'hover:bg-sky-50/70 dark:hover:bg-sky-950/25'],
        ['iconBg' => 'bg-violet-100 dark:bg-violet-900/45', 'iconTxt' => 'text-violet-800 dark:text-violet-300', 'ring' => 'ring-violet-200/70 dark:ring-violet-800/40', 'hoverBg' => 'hover:bg-violet-50/70 dark:hover:bg-violet-950/25'],
        ['iconBg' => 'bg-emerald-100 dark:bg-emerald-900/45', 'iconTxt' => 'text-emerald-800 dark:text-emerald-300', 'ring' => 'ring-emerald-200/70 dark:ring-emerald-800/40', 'hoverBg' => 'hover:bg-emerald-50/70 dark:hover:bg-emerald-950/25'],
        ['iconBg' => 'bg-rose-100 dark:bg-rose-900/45', 'iconTxt' => 'text-rose-800 dark:text-rose-300', 'ring' => 'ring-rose-200/70 dark:ring-rose-800/40', 'hoverBg' => 'hover:bg-rose-50/70 dark:hover:bg-rose-950/25'],
        ['iconBg' => 'bg-orange-100 dark:bg-orange-900/45', 'iconTxt' => 'text-orange-800 dark:text-orange-300', 'ring' => 'ring-orange-200/70 dark:ring-orange-800/40', 'hoverBg' => 'hover:bg-orange-50/70 dark:hover:bg-orange-950/25'],
    ];

    $examAccents = [
        'border-l-amber-500 dark:border-l-amber-400',
        'border-l-emerald-500 dark:border-l-emerald-400',
        'border-l-violet-500 dark:border-l-violet-400',
    ];

    $pageUrl = fn (\App\Models\Website $p) => route('website.page', $p->slug);
    $firstExam = $upcomingExams->first();
@endphp

{{-- Hero: split layout + bento stats --}}
<section class="website-fade-up relative mb-8 sm:mb-10 lg:mb-12 overflow-hidden rounded-[1.75rem] border border-stone-200/90 dark:border-slate-700/90 bg-gradient-to-br from-white via-amber-50/40 to-teal-50/30 dark:from-slate-900 dark:via-slate-900 dark:to-teal-950/20">
    <div class="absolute top-0 right-0 w-1/2 max-w-md h-full bg-gradient-to-l from-amber-200/20 to-transparent dark:from-amber-900/10 pointer-events-none" aria-hidden="true"></div>
    <div class="relative px-5 py-10 sm:px-8 sm:py-12 lg:px-10 lg:py-14 grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-8 items-center">
        <div class="lg:col-span-7">
            <p class="inline-flex items-center gap-2 rounded-full border border-amber-300/60 dark:border-amber-700/50 bg-white/80 dark:bg-slate-800/60 px-3 py-1.5 text-xs font-semibold uppercase tracking-wider text-amber-900 dark:text-amber-300 mb-5 shadow-sm">
                @include('partials.icon', ['name' => 'sparkles', 'class' => 'w-3.5 h-3.5'])
                {{ t('home_hero_badge', 'Official examination platform') }}
            </p>
            <h1 class="font-heading text-3xl sm:text-4xl xl:text-[2.75rem] font-semibold text-stone-900 dark:text-slate-100 tracking-tight leading-[1.15] mb-5">
                {{ t('home_hero_title', 'Pali & Dhamma examinations for monasteries and Sangha') }}
            </h1>
            <p class="text-base sm:text-lg text-stone-700 dark:text-slate-300 leading-relaxed mb-8 max-w-xl">
                {{ t('home_hero_subtitle_v2', 'Each monastery runs its own hall lists; candidates see subject marks; the public sees schedules—all from one coherent system.') }}
            </p>
            <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3">
                <a href="{{ route('website.login', ['type' => 'monastery', 'mode' => 'register']) }}" class="inline-flex items-center justify-center gap-2 min-h-[48px] px-6 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-white font-semibold shadow-lg shadow-amber-500/30 hover:from-amber-600 hover:to-amber-700 transition-all">
                    @include('partials.icon', ['name' => 'plus', 'class' => 'w-5 h-5 shrink-0'])
                    {{ t('register_monastery', 'Register monastery') }}
                </a>
                <a href="{{ route('website.login', ['type' => 'sangha', 'mode' => 'register']) }}" class="inline-flex items-center justify-center gap-2 min-h-[48px] px-6 rounded-xl border-2 border-teal-600/40 dark:border-teal-500/40 text-teal-900 dark:text-teal-300 font-semibold bg-white/70 dark:bg-slate-800/60 hover:bg-teal-50 dark:hover:bg-teal-950/30 transition-colors">
                    @include('partials.icon', ['name' => 'academic-cap', 'class' => 'w-5 h-5 shrink-0'])
                    {{ t('register_sangha', 'Register Sangha') }}
                </a>
                <a href="{{ route('website.login') }}" class="inline-flex items-center justify-center min-h-[48px] px-4 text-sm font-semibold text-stone-600 dark:text-slate-400 hover:text-amber-800 dark:hover:text-amber-300 transition-colors">
                    {{ t('home_hero_login', 'Already have an account? Log in') }}
                </a>
            </div>
        </div>
        <div class="lg:col-span-5 grid grid-cols-2 gap-3 sm:gap-4">
            <div class="col-span-2 rounded-2xl border border-stone-200/80 dark:border-slate-600 bg-white/90 dark:bg-slate-800/80 p-4 sm:p-5 shadow-md shadow-stone-200/40 dark:shadow-none">
                <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-slate-400">{{ t('home_bento_snapshot', 'Live snapshot') }}</p>
                <p class="mt-2 font-heading text-2xl sm:text-3xl text-stone-900 dark:text-slate-50">
                    {{ $firstExam ? \Illuminate\Support\Str::limit($firstExam->name, 42) : t('home_bento_no_exam', 'Schedules ready to publish') }}
                </p>
                <p class="text-xs text-stone-600 dark:text-slate-400 mt-1">{{ $firstExam && $firstExam->exam_date ? $firstExam->exam_date->format('l, M j, Y') : t('home_bento_date_hint', 'Dates sync from your exam records') }}</p>
            </div>
            <div class="rounded-2xl bg-stone-900 dark:bg-slate-950 text-white p-4 sm:p-5 border border-stone-700 dark:border-slate-700">
                <p class="text-[10px] uppercase tracking-wider text-amber-200/90">{{ t('exams', 'Exams') }}</p>
                <p class="font-heading text-3xl text-amber-400 tabular-nums mt-1">{{ number_format($stats['exams'] ?? 0) }}</p>
            </div>
            <div class="rounded-2xl border-2 border-dashed border-teal-400/50 dark:border-teal-600/50 bg-teal-50/50 dark:bg-teal-950/20 p-4 sm:p-5">
                <p class="text-[10px] uppercase tracking-wider text-teal-800 dark:text-teal-300">{{ t('monastery', 'Monastery') }}</p>
                <p class="font-heading text-3xl text-teal-900 dark:text-teal-200 tabular-nums mt-1">{{ number_format($stats['monasteries'] ?? 0) }}</p>
            </div>
        </div>
    </div>
</section>

{{-- Highlights: asymmetric bento (3 cards, different treatments) --}}
<section class="website-fade-up mb-8 sm:mb-10" style="animation-delay: 0.04s" aria-label="{{ t('home_highlights_label', 'Platform highlights') }}">
    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 sm:gap-5">
        <div class="md:col-span-4 md:row-span-2 rounded-3xl border border-stone-200 dark:border-slate-600 bg-gradient-to-br from-amber-50 via-white to-white dark:from-amber-950/30 dark:via-slate-900 dark:to-slate-900 p-6 sm:p-8 website-card-hover relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-amber-400/10 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2" aria-hidden="true"></div>
            <div class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500 text-white shadow-lg shadow-amber-500/30 mb-5">
                @include('partials.icon', ['name' => 'home', 'class' => 'w-6 h-6'])
            </div>
            <h2 class="font-heading text-xl sm:text-2xl font-semibold text-stone-900 dark:text-slate-100 mb-3">{{ t('home_highlight_1_title', 'Monastery portal') }}</h2>
            <p class="text-sm sm:text-base text-stone-600 dark:text-slate-400 leading-relaxed max-w-lg">{{ t('home_highlight_1_body_v2', 'One dashboard for sangha lists, exam attachments, and secure messages—so coordinators are not juggling spreadsheets and chat threads.') }}</p>
        </div>
        <div class="md:col-span-2 rounded-2xl bg-slate-900 dark:bg-slate-950 text-slate-100 p-5 sm:p-6 border border-slate-700 website-card-hover flex flex-col justify-center">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 text-teal-400 mb-4">
                @include('partials.icon', ['name' => 'view', 'class' => 'w-5 h-5'])
            </div>
            <h2 class="font-heading text-lg font-semibold text-white mb-2">{{ t('home_highlight_2_title', 'Sangha results') }}</h2>
            <p class="text-sm text-slate-400 leading-relaxed">{{ t('home_highlight_2_body_v2', 'Per-exam, per-subject marks with a calm layout—easy to screenshot or explain to elders.') }}</p>
        </div>
        <div class="md:col-span-2 rounded-2xl border-2 border-dashed border-stone-300 dark:border-slate-600 bg-transparent p-5 sm:p-6 website-card-hover">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 dark:bg-violet-900/40 text-violet-700 dark:text-violet-300 mb-4">
                @include('partials.icon', ['name' => 'calendar', 'class' => 'w-5 h-5'])
            </div>
            <h2 class="font-heading text-lg font-semibold text-stone-900 dark:text-slate-100 mb-2">{{ t('home_highlight_3_title', 'Public schedule') }}</h2>
            <p class="text-sm text-stone-600 dark:text-slate-400 leading-relaxed">{{ t('home_highlight_3_body_v2', 'The schedule page mixes CMS copy with a real table—clients immediately see credible dates and venues.') }}</p>
        </div>
    </div>
</section>

{{-- Stats: horizontal ribbon (distinct from cards above) --}}
@if(($stats['monasteries'] ?? 0) + ($stats['sanghas'] ?? 0) + ($stats['exams'] ?? 0) > 0)
    <section class="website-fade-up mb-8 sm:mb-10" style="animation-delay: 0.06s" aria-label="{{ t('home_stats_label', 'At a glance') }}">
        <div class="flex flex-col md:flex-row md:items-stretch rounded-2xl overflow-hidden border border-stone-200 dark:border-slate-700 bg-gradient-to-b from-white to-stone-50/80 dark:from-slate-800/90 dark:to-slate-900/90 shadow-sm">
            <div class="flex-1 px-6 py-6 md:py-8 text-center md:text-left md:border-r border-stone-200/90 dark:border-slate-700">
                <p class="text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400">{{ t('monastery', 'Monastery') }}</p>
                <p class="font-heading text-4xl sm:text-5xl font-semibold text-stone-900 dark:text-slate-50 tabular-nums mt-2">{{ number_format($stats['monasteries']) }}</p>
                <p class="text-sm text-stone-500 dark:text-slate-400 mt-2">{{ t('home_stat_monasteries_hint_v2', 'Partner monasteries in this demo dataset') }}</p>
            </div>
            <div class="flex-1 px-6 py-6 md:py-8 text-center md:text-left md:border-r border-stone-200/90 dark:border-slate-700 bg-stone-50/50 dark:bg-slate-800/30">
                <p class="text-xs font-bold uppercase tracking-wider text-teal-700 dark:text-teal-400">{{ t('sangha_label', 'Sangha') }}</p>
                <p class="font-heading text-4xl sm:text-5xl font-semibold text-stone-900 dark:text-slate-50 tabular-nums mt-2">{{ number_format($stats['sanghas']) }}</p>
                <p class="text-sm text-stone-500 dark:text-slate-400 mt-2">{{ t('home_stat_sanghas_hint_v2', 'Candidate profiles linked to monasteries') }}</p>
            </div>
            <div class="flex-1 px-6 py-6 md:py-8 text-center md:text-left">
                <p class="text-xs font-bold uppercase tracking-wider text-violet-700 dark:text-violet-400">{{ t('exams', 'Exams') }}</p>
                <p class="font-heading text-4xl sm:text-5xl font-semibold text-stone-900 dark:text-slate-50 tabular-nums mt-2">{{ number_format($stats['exams']) }}</p>
                <p class="text-sm text-stone-500 dark:text-slate-400 mt-2">{{ t('home_stat_exams_hint_v2', 'Active & approved programs counted here') }}</p>
            </div>
        </div>
    </section>
@endif

{{-- Upcoming exams: accent stripes --}}
@if($upcomingExams->isNotEmpty())
    <section class="website-fade-up mb-8 sm:mb-10" style="animation-delay: 0.1s">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
            <div>
                <h2 class="font-heading text-2xl sm:text-3xl text-stone-900 dark:text-slate-100">{{ t('home_upcoming_exams', 'Upcoming examinations') }}</h2>
                <p class="text-sm text-stone-600 dark:text-slate-400 mt-1">{{ t('home_upcoming_exams_sub_v2', 'Three accent styles rotate by row—data still comes straight from your database.') }}</p>
            </div>
            <a href="{{ $featuredPages->firstWhere('slug', 'exam-schedule') ? route('website.page', 'exam-schedule') : route('website.home') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-teal-700 dark:text-teal-400 hover:underline shrink-0">
                {{ t('home_view_schedule', 'Full schedule') }}
                @include('partials.icon', ['name' => 'chevron-right', 'class' => 'w-4 h-4'])
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($upcomingExams as $exam)
                @php $accent = $examAccents[$loop->index % count($examAccents)]; @endphp
                <article class="website-card-hover rounded-2xl border border-stone-200 dark:border-slate-600 border-l-[5px] {{ $accent }} bg-white dark:bg-slate-800/90 pl-4 pr-5 py-5 flex flex-col min-h-[150px] shadow-sm dark:shadow-none">
                    <div class="flex items-start gap-3 mb-3">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-stone-100 dark:bg-slate-700/80 text-stone-700 dark:text-slate-200">
                            @include('partials.icon', ['name' => 'calendar', 'class' => 'w-5 h-5'])
                        </span>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-semibold text-stone-900 dark:text-slate-50 leading-snug">{{ $exam->name }}</h3>
                            @if($exam->examType)
                                <p class="text-xs font-medium text-stone-500 dark:text-slate-400 mt-1">{{ $exam->examType->name }}</p>
                            @endif
                        </div>
                    </div>
                    <dl class="mt-auto space-y-2 text-sm border-t border-stone-100 dark:border-slate-700/80 pt-3">
                        <div class="flex justify-between gap-2">
                            <dt class="text-stone-500 dark:text-slate-400">{{ t('home_exam_date', 'Date') }}</dt>
                            <dd class="font-medium text-stone-900 dark:text-slate-100 text-right tabular-nums">{{ $exam->exam_date ? $exam->exam_date->format('M j, Y') : t('home_exam_date_tbd', 'TBC') }}</dd>
                        </div>
                        <div class="flex justify-between gap-2">
                            <dt class="text-stone-500 dark:text-slate-400 shrink-0">{{ t('home_exam_venue', 'Venue') }}</dt>
                            <dd class="text-stone-700 dark:text-slate-300 text-right line-clamp-2">{{ $exam->location ?: '—' }}</dd>
                        </div>
                        <div class="flex justify-between gap-2">
                            <dt class="text-stone-500 dark:text-slate-400 shrink-0">{{ t('monastery', 'Monastery') }}</dt>
                            <dd class="text-stone-700 dark:text-slate-300 text-right line-clamp-2">{{ $exam->monastery?->name ?? '—' }}</dd>
                        </div>
                    </dl>
                </article>
            @endforeach
        </div>
    </section>
@endif

{{-- Explore: unique icon + colour per link; occasional wide tile --}}
@if($featuredPages->isNotEmpty())
    <section class="pb-6 sm:pb-10 website-fade-up" style="animation-delay: 0.14s">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="font-heading text-2xl sm:text-3xl text-stone-900 dark:text-slate-100">{{ t('home_explore_title', 'Explore the site') }}</h2>
                <p class="text-sm text-stone-600 dark:text-slate-400 mt-2 max-w-2xl">{{ t('home_explore_sub_v2', 'Each tile uses its own icon and colour—so the grid feels curated, not copy-pasted.') }}</p>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
            @foreach($featuredPages as $featuredPage)
                @php
                    $icon = $pageLinkIcons[$featuredPage->slug] ?? 'document-text';
                    $pal = $explorePalettes[$loop->index % count($explorePalettes)];
                    $wide = $loop->index % 9 === 0;
                @endphp
                <a href="{{ $pageUrl($featuredPage) }}" class="website-card-hover group flex flex-col sm:flex-row sm:items-center gap-4 rounded-2xl border border-stone-200/90 dark:border-slate-600 bg-white/95 dark:bg-slate-800/90 px-4 py-4 sm:py-5 min-h-[4.5rem] ring-1 ring-transparent {{ $pal['ring'] }} {{ $pal['hoverBg'] }} transition-all {{ $wide ? 'xl:col-span-2' : '' }}">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl {{ $pal['iconBg'] }} {{ $pal['iconTxt'] }} shadow-sm group-hover:scale-105 transition-transform duration-300">
                        @include('partials.icon', ['name' => $icon, 'class' => 'w-6 h-6'])
                    </span>
                    <span class="flex-1 min-w-0 text-left">
                        <span class="block font-semibold text-stone-900 dark:text-slate-50 group-hover:text-amber-900 dark:group-hover:text-amber-200 transition-colors">{{ $featuredPage->title }}</span>
                        @if($wide)
                            <span class="block text-xs text-stone-500 dark:text-slate-400 mt-1">{{ t('home_explore_wide_hint', 'Featured link — spans two columns on wide screens.') }}</span>
                        @endif
                    </span>
                    @include('partials.icon', ['name' => 'chevron-right', 'class' => 'w-5 h-5 text-stone-400 dark:text-slate-500 shrink-0 transition-transform group-hover:translate-x-1 group-hover:text-amber-600 dark:group-hover:text-amber-400'])
                </a>
            @endforeach
        </div>
    </section>
@endif
@endsection
