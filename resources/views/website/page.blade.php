@extends('website.layout')

@section('title', $page->title)

@section('content')
@php
    $maxW = match ($pageTheme ?? 'standard') {
        'utility' => 'max-w-2xl',
        'magazine', 'syllabus', 'directory', 'sitemap', 'split', 'contact', 'legal' => 'max-w-6xl',
        default => 'max-w-5xl',
    };
    $blocksAboveCard = in_array($pageTheme ?? 'standard', ['gallery', 'resources', 'results', 'timeline', 'papers'], true);
@endphp

@if(($pageTheme ?? '') === 'sitemap')
    <section class="relative py-8 sm:py-10 lg:py-14">
        <div class="absolute inset-0 bg-gradient-to-b from-sky-950/10 via-transparent to-transparent dark:from-sky-950/25 pointer-events-none" aria-hidden="true"></div>
        <div class="relative w-full {{ $maxW }} mx-auto px-0 sm:px-2 space-y-8">
            <div class="website-fade-up flex flex-col sm:flex-row sm:items-start gap-5">
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-sky-100 dark:bg-sky-900/40 text-sky-800 dark:text-sky-200 shadow-sm">
                    @include('partials.icon', ['name' => 'map', 'class' => 'w-7 h-7'])
                </span>
                <div>
                    <h1 class="font-heading text-3xl sm:text-4xl lg:text-[2.5rem] font-semibold text-stone-900 dark:text-slate-100 tracking-tight">
                        {{ $page->title }}
                    </h1>
                    <p class="mt-3 text-stone-600 dark:text-slate-400 text-base max-w-2xl leading-relaxed">
                        {{ t('sitemap_page_intro', 'Browse every published public page, grouped the same way as your navigation—ideal for demos and accessibility.') }}
                    </p>
                </div>
            </div>
            @include('website.partials.public-page-blocks')
            <div class="website-fade-up rounded-3xl border border-stone-200/80 dark:border-slate-700/90 bg-white/90 dark:bg-slate-900/85 backdrop-blur-sm shadow-lg shadow-stone-200/30 dark:shadow-none overflow-hidden" style="animation-delay: 0.06s">
                <div class="h-1 w-full bg-gradient-to-r {{ $pageAccentBar }}" aria-hidden="true"></div>
                <div class="px-5 sm:px-8 lg:px-10 py-8 sm:py-10">
                    @include('website.partials.public-page-body')
                </div>
            </div>
        </div>
    </section>
@elseif(($pageTheme ?? '') === 'directory')
    <section class="relative py-8 sm:py-12 lg:py-16">
        <div class="w-full {{ $maxW }} mx-auto px-0 sm:px-2 space-y-8">
            <div class="website-fade-up rounded-[1.75rem] overflow-hidden border border-cyan-200/60 dark:border-cyan-800/40 bg-gradient-to-br from-cyan-50 via-white to-sky-50/80 dark:from-slate-900 dark:via-slate-900 dark:to-cyan-950/30 px-6 sm:px-10 py-10 sm:py-12">
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-cyan-800 dark:text-cyan-300 mb-2">{{ t('partners_eyebrow', 'Network') }}</p>
                        <h1 class="font-heading text-3xl sm:text-4xl font-semibold text-stone-900 dark:text-slate-100 tracking-tight">
                            {{ $page->title }}
                        </h1>
                        <p class="mt-3 text-stone-600 dark:text-slate-400 max-w-xl leading-relaxed">
                            {{ t('partners_hero_sub', 'Live partner monasteries from your database—updated automatically when you approve and activate institutions.') }}
                        </p>
                    </div>
                    @isset($partnerMonasteries)
                        <div class="rounded-2xl bg-white/80 dark:bg-slate-800/70 border border-cyan-200/50 dark:border-cyan-800/40 px-5 py-4 text-center shrink-0">
                            <p class="text-3xl font-heading font-semibold text-cyan-900 dark:text-cyan-100 tabular-nums">{{ $partnerMonasteries->count() }}</p>
                            <p class="text-xs font-medium text-cyan-800/80 dark:text-cyan-300/90 uppercase tracking-wider">{{ t('partners_active_count', 'Active in demo') }}</p>
                        </div>
                    @endisset
                </div>
            </div>
            @include('website.partials.public-page-blocks')
            <div class="website-fade-up rounded-3xl border border-stone-200/80 dark:border-slate-700/90 bg-white/90 dark:bg-slate-900/85 overflow-hidden shadow-lg shadow-stone-200/25 dark:shadow-none">
                <div class="h-1 w-full bg-gradient-to-r {{ $pageAccentBar }}" aria-hidden="true"></div>
                <div class="px-5 sm:px-8 lg:px-10 py-8 sm:py-10">
                    @include('website.partials.public-page-body')
                </div>
            </div>
        </div>
    </section>
@elseif(($pageTheme ?? '') === 'split')
    <section class="py-8 sm:py-12 lg:py-16">
        <div class="w-full {{ $maxW }} mx-auto px-0 sm:px-2">
            <h1 class="website-fade-up font-heading text-3xl sm:text-4xl lg:text-[2.5rem] font-semibold text-stone-900 dark:text-slate-100 tracking-tight mb-8">
                {{ $page->title }}
            </h1>
            <div class="grid lg:grid-cols-12 gap-8 lg:gap-10 items-start">
                <aside class="lg:col-span-4 space-y-4 website-fade-up" style="animation-delay: 0.05s">
                    @isset($aboutStats)
                        <div class="rounded-2xl border border-amber-200/70 dark:border-amber-800/40 bg-gradient-to-br from-amber-50 to-white dark:from-amber-950/25 dark:to-slate-900 p-6">
                            <p class="text-xs font-bold uppercase tracking-wider text-amber-800 dark:text-amber-300 mb-4">{{ t('about_aside_title', 'At a glance') }}</p>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-xs text-stone-500 dark:text-slate-400">{{ t('monastery', 'Monastery') }}</dt>
                                    <dd class="font-heading text-2xl font-semibold text-stone-900 dark:text-slate-50 tabular-nums">{{ number_format($aboutStats['monasteries']) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-stone-500 dark:text-slate-400">{{ t('sangha_label', 'Sangha') }}</dt>
                                    <dd class="font-heading text-2xl font-semibold text-stone-900 dark:text-slate-50 tabular-nums">{{ number_format($aboutStats['sanghas']) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-stone-500 dark:text-slate-400">{{ t('exams', 'Exams') }}</dt>
                                    <dd class="font-heading text-2xl font-semibold text-stone-900 dark:text-slate-50 tabular-nums">{{ number_format($aboutStats['exams']) }}</dd>
                                </div>
                            </dl>
                        </div>
                    @endisset
                    <div class="rounded-2xl border border-dashed border-stone-300 dark:border-slate-600 p-5 text-sm text-stone-600 dark:text-slate-400 leading-relaxed">
                        {{ t('about_aside_note', 'Figures reflect active records in this environment—useful when presenting the system to committees.') }}
                    </div>
                </aside>
                <div class="lg:col-span-8 website-fade-up rounded-3xl border border-stone-200/80 dark:border-slate-700/90 bg-white/90 dark:bg-slate-900/85 overflow-hidden shadow-lg shadow-stone-200/25 dark:shadow-none" style="animation-delay: 0.08s">
                    <div class="h-1 w-full bg-gradient-to-r {{ $pageAccentBar }}" aria-hidden="true"></div>
                    <div class="px-5 sm:px-8 lg:px-10 py-8 sm:py-10">
                        @include('website.partials.public-page-body')
                    </div>
                </div>
            </div>
        </div>
    </section>
@elseif(($pageTheme ?? '') === 'contact')
    <section class="py-8 sm:py-12 lg:py-16">
        <div class="w-full {{ $maxW }} mx-auto px-0 sm:px-2">
            <div class="grid lg:grid-cols-5 gap-8 items-start">
                <div class="lg:col-span-2 website-fade-up hidden lg:flex flex-col gap-4 rounded-3xl border border-teal-200/50 dark:border-teal-800/40 bg-teal-50/40 dark:bg-teal-950/15 p-8">
                    <div class="flex items-center gap-3 text-teal-900 dark:text-teal-200">
                        @include('partials.icon', ['name' => 'envelope', 'class' => 'w-8 h-8'])
                        <span class="font-heading font-semibold text-lg">{{ t('contact_aside_title', 'We reply with care') }}</span>
                    </div>
                    <p class="text-sm text-teal-900/80 dark:text-teal-200/80 leading-relaxed">{{ t('contact_aside_p', 'Examination seasons are busy—please include your monastery name and, if relevant, the sitting date.') }}</p>
                    <ul class="text-sm text-teal-800 dark:text-teal-300 space-y-2">
                        <li class="flex gap-2"><span class="text-teal-500">·</span> {{ t('contact_tip_1', 'Use one thread per issue (registration vs technical).') }}</li>
                        <li class="flex gap-2"><span class="text-teal-500">·</span> {{ t('contact_tip_2', 'Screenshots help for UI questions.') }}</li>
                    </ul>
                </div>
                <div class="lg:col-span-3 website-fade-up rounded-3xl border border-stone-200/80 dark:border-slate-700/90 bg-white/90 dark:bg-slate-900/85 overflow-hidden shadow-lg shadow-stone-200/25 dark:shadow-none">
                    <div class="h-1 w-full bg-gradient-to-r {{ $pageAccentBar }}" aria-hidden="true"></div>
                    <header class="px-5 sm:px-8 pt-7 sm:pt-9 pb-4 border-b border-stone-200/80 dark:border-slate-700/80">
                        <h1 class="font-heading text-3xl sm:text-4xl font-semibold text-stone-900 dark:text-slate-100 tracking-tight">{{ $page->title }}</h1>
                    </header>
                    <div class="px-5 sm:px-8 lg:px-10 py-8 sm:py-10">
                        @include('website.partials.public-page-body')
                    </div>
                </div>
            </div>
        </div>
    </section>
@elseif(($pageTheme ?? '') === 'legal')
    <section class="py-8 sm:py-12 lg:py-16">
        <div class="w-full {{ $maxW }} mx-auto px-0 sm:px-2">
            <div class="grid lg:grid-cols-5 gap-8 items-start">
                <aside class="lg:col-span-2 website-fade-up lg:sticky lg:top-24 space-y-4">
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-800/60 p-6">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-2">{{ t('legal_aside_label', 'Reading guide') }}</p>
                        <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">{{ t('legal_aside_p', 'Policies are written for clarity. Replace demo paragraphs with counsel-reviewed text before production.') }}</p>
                    </div>
                </aside>
                <div class="lg:col-span-3 website-fade-up rounded-3xl border border-stone-200/80 dark:border-slate-700/90 bg-white/90 dark:bg-slate-900/85 overflow-hidden shadow-lg shadow-stone-200/25 dark:shadow-none">
                    <div class="h-1 w-full bg-gradient-to-r {{ $pageAccentBar }}" aria-hidden="true"></div>
                    <header class="px-5 sm:px-8 pt-7 sm:pt-9 pb-6 border-b border-stone-200/80 dark:border-slate-700/80">
                        <h1 class="font-heading text-3xl sm:text-4xl font-semibold text-stone-900 dark:text-slate-100 tracking-tight">{{ $page->title }}</h1>
                    </header>
                    <div class="px-5 sm:px-8 lg:px-10 py-8 sm:py-10">
                        @include('website.partials.public-page-body')
                    </div>
                </div>
            </div>
        </div>
    </section>
@elseif(($pageTheme ?? '') === 'utility')
    <section class="py-10 sm:py-14 lg:py-16">
        <div class="w-full {{ $maxW }} mx-auto px-0 sm:px-2">
            <div class="website-fade-up rounded-3xl border border-stone-200/80 dark:border-slate-700/90 bg-white/95 dark:bg-slate-900/90 shadow-xl shadow-stone-200/40 dark:shadow-none overflow-hidden">
                <div class="h-1 w-full bg-gradient-to-r {{ $pageAccentBar }}" aria-hidden="true"></div>
                <header class="px-6 sm:px-10 pt-8 pb-5 text-center border-b border-stone-100 dark:border-slate-800">
                    <h1 class="font-heading text-2xl sm:text-3xl font-semibold text-stone-900 dark:text-slate-100 tracking-tight">{{ $page->title }}</h1>
                </header>
                <div class="px-6 sm:px-10 py-8 sm:py-10">
                    @include('website.partials.public-page-body')
                </div>
            </div>
        </div>
    </section>
@elseif(($pageTheme ?? '') === 'cta')
    <section class="py-8 sm:py-12 lg:py-16">
        <div class="w-full max-w-5xl mx-auto px-0 sm:px-2">
            <div class="website-fade-up rounded-3xl overflow-hidden border border-rose-200/50 dark:border-rose-900/40 shadow-xl shadow-rose-200/20 dark:shadow-none">
                <div class="bg-gradient-to-r from-rose-600 via-amber-600 to-amber-500 dark:from-rose-700 dark:via-amber-700 dark:to-amber-600 px-6 sm:px-10 py-10 sm:py-12 text-white">
                    <h1 class="font-heading text-3xl sm:text-4xl font-semibold tracking-tight">{{ $page->title }}</h1>
                    <p class="mt-3 text-amber-50/95 max-w-2xl leading-relaxed">{{ t('cta_page_sub', 'Transparent copy builds trust—swap every line below with your official appeal or volunteer brief.') }}</p>
                </div>
                <div class="bg-white dark:bg-slate-900 px-6 sm:px-10 py-8 sm:py-10">
                    @include('website.partials.public-page-body')
                </div>
            </div>
        </div>
    </section>
@elseif(($pageTheme ?? '') === 'magazine')
    <section class="py-8 sm:py-12 lg:py-16">
        <div class="w-full {{ $maxW }} mx-auto px-0 sm:px-2 space-y-10">
            <header class="website-fade-up border-b border-stone-200 dark:border-slate-700 pb-8">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-700 dark:text-violet-400 mb-3">{{ t('magazine_eyebrow', 'Stories & dates') }}</p>
                <h1 class="font-heading text-4xl sm:text-5xl font-semibold text-stone-900 dark:text-slate-100 tracking-tight max-w-3xl">{{ $page->title }}</h1>
            </header>
            @include('website.partials.public-page-blocks')
            <div class="website-fade-up rounded-3xl border border-stone-200/80 dark:border-slate-700/80 bg-white/80 dark:bg-slate-900/70 px-6 sm:px-10 py-9 sm:py-11">
                <div class="h-1 w-12 rounded-full bg-gradient-to-r {{ $pageAccentBar }} mb-8" aria-hidden="true"></div>
                @include('website.partials.public-page-body')
            </div>
        </div>
    </section>
@elseif(($pageTheme ?? '') === 'syllabus')
    <section class="py-8 sm:py-12 lg:py-16">
        <div class="w-full {{ $maxW }} mx-auto px-0 sm:px-2">
            <div class="website-fade-up rounded-3xl overflow-hidden border border-indigo-200/60 dark:border-indigo-900/50 bg-white/90 dark:bg-slate-900/85 shadow-lg shadow-indigo-100/40 dark:shadow-none">
                <div class="h-2 w-full bg-gradient-to-r {{ $pageAccentBar }}" aria-hidden="true"></div>
                <div class="px-5 sm:px-8 lg:px-12 py-9 sm:py-11">
                    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-8 pb-8 border-b border-stone-200/80 dark:border-slate-700">
                        <div>
                            <h1 class="font-heading text-3xl sm:text-4xl lg:text-[2.75rem] font-semibold text-stone-900 dark:text-slate-100 tracking-tight">{{ $page->title }}</h1>
                            <p class="mt-2 text-stone-600 dark:text-slate-400 max-w-2xl">{{ t('syllabus_intro', 'Structured overview of papers and weightings—pair this narrative with tables in your CMS body.') }}</p>
                        </div>
                        <span class="inline-flex items-center gap-2 rounded-full bg-indigo-50 dark:bg-indigo-950/50 text-indigo-800 dark:text-indigo-200 text-xs font-semibold px-4 py-2 border border-indigo-200/60 dark:border-indigo-800/50">
                            @include('partials.icon', ['name' => 'academic-cap', 'class' => 'w-4 h-4'])
                            {{ t('syllabus_badge', 'Curriculum reference') }}
                        </span>
                    </div>
                    @include('website.partials.public-page-body')
                </div>
            </div>
        </div>
    </section>
@else
    @php
        $hasPageBody = trim(strip_tags($page->content ?? '', '<img>')) !== '' || ($useFallbackContent ?? false);
    @endphp
    <section class="relative py-8 sm:py-12 lg:py-16">
        <div class="w-full {{ $maxW }} mx-auto px-0 sm:px-2">
            @if($blocksAboveCard)
                <header class="website-fade-up mb-8">
                    <h1 class="font-heading text-3xl sm:text-4xl lg:text-[2.5rem] font-semibold text-stone-900 dark:text-slate-100 tracking-tight">{{ $page->title }}</h1>
                    @if(($pageTheme ?? '') === 'results')
                        <p class="mt-2 text-stone-600 dark:text-slate-400 max-w-2xl">{{ t('results_page_intro', 'Live counts from your database; narrative copy sits below for policies and process.') }}</p>
                    @endif
                </header>
                @include('website.partials.public-page-blocks')
            @endif

            <div class="website-fade-up rounded-3xl border border-stone-200/80 dark:border-slate-700/90 bg-white/90 dark:bg-slate-900/85 backdrop-blur-sm shadow-lg shadow-stone-200/30 dark:shadow-none overflow-hidden">
                <div class="h-1 w-full bg-gradient-to-r {{ $pageAccentBar }}" aria-hidden="true"></div>
                @unless($blocksAboveCard)
                    <header class="px-5 sm:px-8 lg:px-10 pt-7 sm:pt-9 pb-6 border-b border-stone-200/80 dark:border-slate-700/80">
                        <h1 class="font-heading text-3xl sm:text-4xl lg:text-[2.5rem] font-semibold text-stone-900 dark:text-slate-100 tracking-tight">
                            {{ $page->title }}
                        </h1>
                    </header>
                @endunless

                @if($scheduleExams !== null)
                    <div class="px-5 sm:px-8 lg:px-10 pt-8 pb-2">
                        <h2 class="text-lg font-semibold text-stone-900 dark:text-slate-100 mb-1">{{ t('schedule_table_title', 'Scheduled examinations') }}</h2>
                        <p class="text-sm text-stone-600 dark:text-slate-400 mb-6">{{ t('schedule_table_sub', 'Dates and venues from the live database (demo-friendly).') }}</p>

                        @if($scheduleExams->isEmpty())
                            <div class="rounded-2xl border border-dashed border-stone-300 dark:border-slate-600 bg-stone-50/80 dark:bg-slate-800/50 px-6 py-12 text-center text-stone-600 dark:text-slate-400">
                                {{ t('schedule_empty', 'No examinations are listed yet. Check back after admins publish schedules.') }}
                            </div>
                        @else
                            <div class="overflow-x-auto rounded-2xl border border-stone-200 dark:border-slate-700 -mx-1 sm:mx-0">
                                <table class="min-w-full text-left text-sm">
                                    <thead>
                                        <tr class="border-b border-stone-200 dark:border-slate-700 bg-stone-50/95 dark:bg-slate-800/90">
                                            <th class="px-4 py-3 font-semibold text-stone-800 dark:text-slate-200 whitespace-nowrap">{{ t('exam', 'Exam') }}</th>
                                            <th class="px-4 py-3 font-semibold text-stone-800 dark:text-slate-200 whitespace-nowrap hidden sm:table-cell">{{ t('exam_type', 'Type') }}</th>
                                            <th class="px-4 py-3 font-semibold text-stone-800 dark:text-slate-200 whitespace-nowrap">{{ t('date', 'Date') }}</th>
                                            <th class="px-4 py-3 font-semibold text-stone-800 dark:text-slate-200 min-w-[8rem]">{{ t('location', 'Venue') }}</th>
                                            <th class="px-4 py-3 font-semibold text-stone-800 dark:text-slate-200 hidden md:table-cell">{{ t('monastery', 'Monastery') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-stone-200/80 dark:divide-slate-700/80">
                                        @foreach($scheduleExams as $exam)
                                            <tr class="bg-white dark:bg-slate-900/40 hover:bg-amber-50/40 dark:hover:bg-slate-800/60 transition-colors">
                                                <td class="px-4 py-3.5 font-medium text-stone-900 dark:text-slate-100">{{ $exam->name }}</td>
                                                <td class="px-4 py-3.5 text-stone-600 dark:text-slate-300 hidden sm:table-cell">{{ $exam->examType?->name ?? '—' }}</td>
                                                <td class="px-4 py-3.5 text-stone-700 dark:text-slate-200 whitespace-nowrap">
                                                    {{ $exam->exam_date ? $exam->exam_date->format('M j, Y') : t('home_exam_date_tbd', 'TBC') }}
                                                </td>
                                                <td class="px-4 py-3.5 text-stone-600 dark:text-slate-300">{{ $exam->location ?: '—' }}</td>
                                                <td class="px-4 py-3.5 text-stone-600 dark:text-slate-300 hidden md:table-cell">{{ $exam->monastery?->name ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @endif

                <article @class([
                    'px-5 sm:px-8 lg:px-10 py-8 sm:py-10',
                    'pt-6 border-t border-stone-200/80 dark:border-slate-700/80' => $scheduleExams !== null && $hasPageBody,
                ])>
                    @include('website.partials.public-page-body')
                </article>
            </div>
        </div>
    </section>
@endif
@endsection
