@extends('website.layout')

@section('title', t('exam_eligible_index_title', 'Exam hall candidates'))

@section('content')
<section class="relative py-10 sm:py-14 lg:py-16">
    <div class="max-w-6xl mx-auto space-y-6">
        <div class="rounded-3xl border border-stone-200/80 dark:border-slate-800 bg-white/85 dark:bg-slate-900/70 backdrop-blur-sm shadow-sm p-6 sm:p-8">
            <h1 class="font-heading text-3xl sm:text-4xl font-semibold text-stone-900 dark:text-slate-100 tracking-tight">{{ t('exam_eligible_index_title', 'Exam hall candidates') }}</h1>
            <p class="mt-3 text-sm sm:text-base text-stone-600 dark:text-slate-300">
                {{ t('exam_eligible_index_intro', 'Choose an examination to view the published list of candidates allowed to sit (approved entrance with desk numbers).') }}
            </p>
        </div>

        <div class="rounded-3xl border border-stone-200/80 dark:border-slate-800 bg-white/85 dark:bg-slate-900/70 backdrop-blur-sm shadow-sm p-6 sm:p-8">
            <ul class="divide-y divide-stone-100 dark:divide-slate-800">
                @forelse($exams as $exam)
                    <li class="flex flex-col gap-2 py-4 first:pt-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <p class="font-medium text-stone-900 dark:text-slate-100">{{ $exam->name }}</p>
                            <p class="mt-0.5 text-sm text-stone-500 dark:text-slate-400">
                                @if($exam->exam_date)
                                    {{ $exam->exam_date->format('M j, Y') }}
                                @else
                                    —
                                @endif
                                @if($exam->examType)
                                    <span class="text-stone-400 dark:text-slate-500"> · </span>{{ $exam->examType->name }}
                                @endif
                            </p>
                            @if(in_array($exam->id, $publishedIds, true))
                                <span class="mt-2 inline-flex rounded-full bg-emerald-100 dark:bg-emerald-900/35 px-2.5 py-0.5 text-[11px] font-semibold uppercase tracking-wide text-emerald-800 dark:text-emerald-300">{{ t('exam_eligible_badge_published', 'Published') }}</span>
                            @else
                                <span class="mt-2 inline-flex rounded-full bg-stone-100 dark:bg-slate-800 px-2.5 py-0.5 text-[11px] font-semibold uppercase tracking-wide text-stone-600 dark:text-slate-400">{{ t('exam_eligible_badge_not_published', 'Not published yet') }}</span>
                            @endif
                        </div>
                        <a href="{{ route('website.exam-eligible.show', $exam) }}" class="inline-flex shrink-0 items-center justify-center rounded-xl border border-yellow-300/70 bg-yellow-50 px-4 py-2.5 text-sm font-semibold text-yellow-900 transition hover:bg-yellow-100 dark:border-yellow-600/50 dark:bg-yellow-950/40 dark:text-yellow-200 dark:hover:bg-yellow-900/35">
                            {{ t('exam_eligible_open_list', 'View list') }}
                        </a>
                    </li>
                @empty
                    <li class="py-8 text-center text-sm text-stone-500 dark:text-slate-400">{{ t('exam_eligible_index_empty', 'No active exams are available.') }}</li>
                @endforelse
            </ul>
        </div>
    </div>
</section>
@endsection
