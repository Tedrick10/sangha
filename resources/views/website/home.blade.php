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

    /* 14 distinct families; light = soft tint. Dark = dark chip + bright inset ring + near-white icon (readable on dark cards). */
    $explorePalettes = [
        ['iconBg' => 'bg-amber-100 dark:bg-slate-900 dark:ring-2 dark:ring-inset dark:ring-amber-400/85', 'iconTxt' => 'text-amber-900 dark:text-amber-50', 'ring' => 'ring-amber-200/70 dark:ring-amber-500/35', 'hoverBg' => 'hover:bg-amber-50/80 dark:hover:bg-slate-800/95'],
        ['iconBg' => 'bg-sky-100 dark:bg-slate-900 dark:ring-2 dark:ring-inset dark:ring-sky-400/85', 'iconTxt' => 'text-sky-900 dark:text-sky-50', 'ring' => 'ring-sky-200/70 dark:ring-sky-500/35', 'hoverBg' => 'hover:bg-sky-50/80 dark:hover:bg-slate-800/95'],
        ['iconBg' => 'bg-violet-100 dark:bg-slate-900 dark:ring-2 dark:ring-inset dark:ring-violet-400/85', 'iconTxt' => 'text-violet-900 dark:text-violet-50', 'ring' => 'ring-violet-200/70 dark:ring-violet-500/35', 'hoverBg' => 'hover:bg-violet-50/80 dark:hover:bg-slate-800/95'],
        ['iconBg' => 'bg-emerald-100 dark:bg-slate-900 dark:ring-2 dark:ring-inset dark:ring-emerald-400/85', 'iconTxt' => 'text-emerald-900 dark:text-emerald-50', 'ring' => 'ring-emerald-200/70 dark:ring-emerald-500/35', 'hoverBg' => 'hover:bg-emerald-50/80 dark:hover:bg-slate-800/95'],
        ['iconBg' => 'bg-rose-100 dark:bg-slate-900 dark:ring-2 dark:ring-inset dark:ring-rose-400/85', 'iconTxt' => 'text-rose-900 dark:text-rose-50', 'ring' => 'ring-rose-200/70 dark:ring-rose-500/35', 'hoverBg' => 'hover:bg-rose-50/80 dark:hover:bg-slate-800/95'],
        ['iconBg' => 'bg-orange-100 dark:bg-slate-900 dark:ring-2 dark:ring-inset dark:ring-orange-400/85', 'iconTxt' => 'text-orange-950 dark:text-orange-50', 'ring' => 'ring-orange-200/70 dark:ring-orange-500/35', 'hoverBg' => 'hover:bg-orange-50/80 dark:hover:bg-slate-800/95'],
        ['iconBg' => 'bg-indigo-100 dark:bg-slate-900 dark:ring-2 dark:ring-inset dark:ring-indigo-400/85', 'iconTxt' => 'text-indigo-900 dark:text-indigo-50', 'ring' => 'ring-indigo-200/70 dark:ring-indigo-500/35', 'hoverBg' => 'hover:bg-indigo-50/80 dark:hover:bg-slate-800/95'],
        ['iconBg' => 'bg-fuchsia-100 dark:bg-slate-900 dark:ring-2 dark:ring-inset dark:ring-fuchsia-400/85', 'iconTxt' => 'text-fuchsia-900 dark:text-fuchsia-50', 'ring' => 'ring-fuchsia-200/70 dark:ring-fuchsia-500/35', 'hoverBg' => 'hover:bg-fuchsia-50/80 dark:hover:bg-slate-800/95'],
        ['iconBg' => 'bg-lime-100 dark:bg-slate-900 dark:ring-2 dark:ring-inset dark:ring-lime-400/85', 'iconTxt' => 'text-lime-950 dark:text-lime-50', 'ring' => 'ring-lime-200/70 dark:ring-lime-500/35', 'hoverBg' => 'hover:bg-lime-50/80 dark:hover:bg-slate-800/95'],
        ['iconBg' => 'bg-teal-100 dark:bg-slate-900 dark:ring-2 dark:ring-inset dark:ring-teal-400/85', 'iconTxt' => 'text-teal-900 dark:text-teal-50', 'ring' => 'ring-teal-200/70 dark:ring-teal-500/35', 'hoverBg' => 'hover:bg-teal-50/80 dark:hover:bg-slate-800/95'],
        ['iconBg' => 'bg-cyan-100 dark:bg-slate-900 dark:ring-2 dark:ring-inset dark:ring-cyan-400/85', 'iconTxt' => 'text-cyan-900 dark:text-cyan-50', 'ring' => 'ring-cyan-200/70 dark:ring-cyan-500/35', 'hoverBg' => 'hover:bg-cyan-50/80 dark:hover:bg-slate-800/95'],
        ['iconBg' => 'bg-pink-100 dark:bg-slate-900 dark:ring-2 dark:ring-inset dark:ring-pink-400/85', 'iconTxt' => 'text-pink-900 dark:text-pink-50', 'ring' => 'ring-pink-200/70 dark:ring-pink-500/35', 'hoverBg' => 'hover:bg-pink-50/80 dark:hover:bg-slate-800/95'],
        ['iconBg' => 'bg-red-100 dark:bg-slate-900 dark:ring-2 dark:ring-inset dark:ring-red-400/85', 'iconTxt' => 'text-red-900 dark:text-red-50', 'ring' => 'ring-red-200/70 dark:ring-red-500/35', 'hoverBg' => 'hover:bg-red-50/80 dark:hover:bg-slate-800/95'],
        ['iconBg' => 'bg-blue-100 dark:bg-slate-900 dark:ring-2 dark:ring-inset dark:ring-blue-400/85', 'iconTxt' => 'text-blue-900 dark:text-blue-50', 'ring' => 'ring-blue-200/70 dark:ring-blue-500/35', 'hoverBg' => 'hover:bg-blue-50/80 dark:hover:bg-slate-800/95'],
    ];

    $examAccents = [
        'border-l-yellow-500 dark:border-l-yellow-400',
        'border-l-emerald-500 dark:border-l-emerald-400',
        'border-l-yellow-600 dark:border-l-yellow-400',
    ];

    $pageUrl = fn (\App\Models\Website $p) => route('website.page', $p->slug);
    $firstExam = $upcomingExams->first();
@endphp

{{-- Hero: split layout + bento stats --}}
<section class="website-fade-up relative mb-8 sm:mb-10 lg:mb-12 overflow-hidden rounded-[1.75rem] border border-stone-200/90 dark:border-slate-700/90 bg-gradient-to-br from-white via-yellow-50/40 to-teal-50/30 dark:from-slate-900 dark:via-slate-900 dark:to-teal-950/20">
    <div class="absolute top-0 right-0 w-1/2 max-w-md h-full bg-gradient-to-l from-yellow-200/20 to-transparent dark:from-yellow-900/10 pointer-events-none" aria-hidden="true"></div>
    <div class="relative px-5 py-10 sm:px-8 sm:py-12 lg:px-10 lg:py-14 grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-8 items-center">
        <div class="lg:col-span-7">
            <p class="inline-flex items-center gap-2 rounded-full border border-yellow-300/60 dark:border-yellow-700/50 bg-white/80 dark:bg-slate-800/60 px-3 py-1.5 text-xs font-semibold uppercase tracking-wider text-yellow-900 dark:text-yellow-300 mb-5 shadow-sm">
                @include('partials.icon', ['name' => 'sparkles', 'class' => 'w-3.5 h-3.5'])
                {{ t('home_hero_badge', 'Official examination platform') }}
            </p>
            <h1 class="font-heading text-3xl sm:text-4xl xl:text-[2.75rem] font-semibold text-stone-900 dark:text-yellow-100 tracking-tight leading-[1.15] mb-5">
                {{ t('home_hero_title', 'Pali & Dhamma examinations for monasteries and Sangha') }}
            </h1>
            <p class="text-base sm:text-lg text-stone-700 dark:text-slate-300 leading-relaxed max-w-xl">
                {{ t('home_hero_subtitle_v2', 'Each monastery runs its own hall lists; candidates see subject marks; the public sees schedules—all from one coherent system.') }}
            </p>
        </div>
        <div class="lg:col-span-5 grid grid-cols-2 gap-3 sm:gap-4">
            <div class="col-span-2 rounded-2xl border border-stone-200/80 dark:border-slate-600 bg-white/90 dark:bg-slate-800/80 p-4 sm:p-5 shadow-md shadow-stone-200/40 dark:shadow-none">
                <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-slate-400">{{ t('home_bento_snapshot', 'Live snapshot') }}</p>
                <p class="mt-2 font-heading text-2xl sm:text-3xl text-stone-900 dark:text-slate-200">
                    {{ $firstExam ? \Illuminate\Support\Str::limit($firstExam->name, 42) : t('home_bento_no_exam', 'Schedules ready to publish') }}
                </p>
                <p class="text-xs text-stone-600 dark:text-slate-400 mt-1">{{ $firstExam && $firstExam->exam_date ? $firstExam->exam_date->format('l, M j, Y') : t('home_bento_date_hint', 'Dates sync from your exam records') }}</p>
            </div>
            <div class="rounded-2xl bg-stone-900 dark:bg-slate-950 text-white p-4 sm:p-5 border border-stone-700 dark:border-slate-700">
                <p class="text-[10px] uppercase tracking-wider text-yellow-200/90">{{ t('exams', 'Exams') }}</p>
                <p class="font-heading text-3xl text-yellow-400 tabular-nums mt-1">{{ number_format($stats['exams'] ?? 0) }}</p>
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
        <div class="md:col-span-4 md:row-span-2 rounded-3xl border border-stone-200 dark:border-slate-600 bg-gradient-to-br from-yellow-50 via-white to-white dark:from-yellow-950/30 dark:via-slate-900 dark:to-slate-900 p-6 sm:p-8 website-card-hover relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-400/10 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2" aria-hidden="true"></div>
            <div class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-yellow-500 text-white shadow-lg shadow-yellow-500/30 mb-5">
                @include('partials.icon', ['name' => 'home', 'class' => 'w-6 h-6'])
            </div>
            <h2 class="font-heading text-xl sm:text-2xl font-semibold text-stone-900 dark:text-yellow-100 mb-3">{{ t('home_highlight_1_title', 'Monastery portal') }}</h2>
            <p class="text-sm sm:text-base text-stone-600 dark:text-slate-400 leading-relaxed max-w-lg">{{ t('home_highlight_1_body_v2', 'One dashboard for sangha lists, exam attachments, and secure messages—so coordinators are not juggling spreadsheets and chat threads.') }}</p>
        </div>
        <div class="md:col-span-2 rounded-2xl bg-slate-900 dark:bg-slate-950 text-slate-100 p-5 sm:p-6 border border-slate-700 website-card-hover flex flex-col justify-center">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 text-teal-300 dark:text-teal-200 mb-4 ring-1 ring-white/15">
                @include('partials.icon', ['name' => 'view', 'class' => 'w-5 h-5'])
            </div>
            <h2 class="font-heading text-lg font-semibold text-teal-100 mb-2">{{ t('home_highlight_2_title', 'Sangha results') }}</h2>
            <p class="text-sm text-slate-400 leading-relaxed">{{ t('home_highlight_2_body_v2', 'Per-exam, per-subject marks with a calm layout—easy to screenshot or explain to elders.') }}</p>
        </div>
        <div class="md:col-span-2 rounded-2xl border-2 border-dashed border-stone-300 dark:border-slate-600 bg-transparent p-5 sm:p-6 website-card-hover">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-yellow-100 text-yellow-900 dark:bg-slate-900 dark:ring-2 dark:ring-inset dark:ring-amber-400/80 dark:text-amber-50 mb-4">
                @include('partials.icon', ['name' => 'calendar', 'class' => 'w-5 h-5'])
            </div>
            <h2 class="font-heading text-lg font-semibold text-stone-900 dark:text-amber-100 mb-2">{{ t('home_highlight_3_title', 'Public schedule') }}</h2>
            <p class="text-sm text-stone-600 dark:text-slate-300 leading-relaxed">{{ t('home_highlight_3_body_v2', 'The schedule page mixes CMS copy with a real table—clients immediately see credible dates and venues.') }}</p>
        </div>
    </div>
</section>

{{-- Stats: horizontal ribbon (distinct from cards above) --}}
@if(($stats['monasteries'] ?? 0) + ($stats['sanghas'] ?? 0) + ($stats['exams'] ?? 0) > 0)
    <section class="website-fade-up mb-8 sm:mb-10" style="animation-delay: 0.06s" aria-label="{{ t('home_stats_label', 'At a glance') }}">
        <div class="flex flex-col md:flex-row md:items-stretch rounded-2xl overflow-hidden border border-stone-200 dark:border-slate-700 bg-gradient-to-b from-white to-stone-50/80 dark:from-slate-800/90 dark:to-slate-900/90 shadow-sm">
            <div class="flex-1 px-6 py-6 md:py-8 text-center md:text-left md:border-r border-stone-200/90 dark:border-slate-700">
                <p class="text-xs font-bold uppercase tracking-wider text-yellow-700 dark:text-yellow-400">{{ t('monastery', 'Monastery') }}</p>
                <p class="font-heading text-4xl sm:text-5xl font-semibold text-stone-900 dark:text-yellow-200 tabular-nums mt-2">{{ number_format($stats['monasteries']) }}</p>
                <p class="text-sm text-stone-500 dark:text-slate-400 mt-2">{{ t('home_stat_monasteries_hint_v2', 'Partner monasteries in this demo dataset') }}</p>
            </div>
            <div class="flex-1 px-6 py-6 md:py-8 text-center md:text-left md:border-r border-stone-200/90 dark:border-slate-700 bg-stone-50/50 dark:bg-slate-800/30">
                <p class="text-xs font-bold uppercase tracking-wider text-teal-700 dark:text-teal-400">{{ t('sangha_label', 'Sangha') }}</p>
                <p class="font-heading text-4xl sm:text-5xl font-semibold text-stone-900 dark:text-teal-200 tabular-nums mt-2">{{ number_format($stats['sanghas']) }}</p>
                <p class="text-sm text-stone-500 dark:text-slate-400 mt-2">{{ t('home_stat_sanghas_hint_v2', 'Candidate profiles linked to monasteries') }}</p>
            </div>
            <div class="flex-1 px-6 py-6 md:py-8 text-center md:text-left">
                <p class="text-xs font-bold uppercase tracking-wider text-yellow-700 dark:text-yellow-400">{{ t('exams', 'Exams') }}</p>
                <p class="font-heading text-4xl sm:text-5xl font-semibold text-stone-900 dark:text-yellow-200 tabular-nums mt-2">{{ number_format($stats['exams']) }}</p>
                <p class="text-sm text-stone-500 dark:text-slate-400 mt-2">{{ t('home_stat_exams_hint_v2', 'Active examination programs counted here') }}</p>
            </div>
        </div>
    </section>
@endif

{{-- Upcoming exams: accent stripes --}}
@if($upcomingExams->isNotEmpty())
    <section class="website-fade-up mb-8 sm:mb-10" style="animation-delay: 0.1s">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
            <div>
                <h2 class="font-heading text-2xl sm:text-3xl text-stone-900 dark:text-yellow-100">{{ t('home_upcoming_exams', 'Upcoming examinations') }}</h2>
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
                            <h3 class="font-semibold text-stone-900 dark:text-slate-200 leading-snug">{{ $exam->name }}</h3>
                            @if($exam->examType)
                                <p class="text-xs font-medium text-stone-500 dark:text-slate-400 mt-1">{{ $exam->examType->name }}</p>
                            @endif
                        </div>
                    </div>
                    <dl class="mt-auto space-y-2 text-sm border-t border-stone-100 dark:border-slate-700/80 pt-3">
                        <div class="flex justify-between gap-2">
                            <dt class="text-stone-500 dark:text-slate-400">{{ t('home_exam_date', 'Date') }}</dt>
                            <dd class="font-medium text-stone-900 dark:text-slate-200 text-right tabular-nums">{{ $exam->exam_date ? $exam->exam_date->format('M j, Y') : t('home_exam_date_tbd', 'TBC') }}</dd>
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
                <h2 class="font-heading text-2xl sm:text-3xl text-stone-900 dark:text-yellow-100">{{ t('home_explore_title', 'Explore the site') }}</h2>
                <p class="text-sm text-stone-600 dark:text-slate-400 mt-2 max-w-2xl">{{ t('home_explore_sub_v2', 'Each tile uses its own icon and colour—so the grid feels curated, not copy-pasted.') }}</p>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
                        @foreach($featuredPages as $featuredPage)
                @php
                    $icon = $pageLinkIcons[$featuredPage->slug] ?? 'document-text';
                    $palIdx = (abs(crc32((string) $featuredPage->slug)) + $loop->index * 13) % count($explorePalettes);
                    $pal = $explorePalettes[$palIdx];
                    $wide = $loop->index % 9 === 0;
                @endphp
                <a href="{{ $pageUrl($featuredPage) }}" class="website-card-hover group flex flex-col sm:flex-row sm:items-center gap-4 rounded-2xl border border-stone-200/90 dark:border-slate-600 bg-white/95 dark:bg-slate-800/90 px-4 py-4 sm:py-5 min-h-[4.5rem] ring-1 ring-transparent {{ $pal['ring'] }} {{ $pal['hoverBg'] }} transition-all {{ $wide ? 'xl:col-span-2' : '' }}">
                    <span class="home-explore-icon flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl {{ $pal['iconBg'] }} {{ $pal['iconTxt'] }} shadow-sm shadow-black/5 dark:shadow-black/40 group-hover:scale-105 transition-transform duration-300">
                        @include('partials.icon', ['name' => $icon, 'class' => 'w-6 h-6'])
                    </span>
                    <span class="flex-1 min-w-0 text-left">
                        <span class="block font-semibold text-stone-900 dark:text-slate-200 group-hover:text-yellow-900 dark:group-hover:text-yellow-200 transition-colors">{{ $featuredPage->title }}</span>
                        @if($wide)
                            <span class="block text-xs text-stone-500 dark:text-slate-300 mt-1">{{ t('home_explore_wide_hint', 'Featured link — spans two columns on wide screens.') }}</span>
                        @endif
                    </span>
                    @include('partials.icon', ['name' => 'chevron-right', 'class' => 'w-5 h-5 text-stone-400 dark:text-slate-400 shrink-0 transition-transform group-hover:translate-x-1 group-hover:text-yellow-600 dark:group-hover:text-amber-200'])
                            </a>
                        @endforeach
        </div>
    </section>
@endif
@endsection
