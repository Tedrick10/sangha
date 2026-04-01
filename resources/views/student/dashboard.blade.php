@extends('layouts.student')

@section('title', t('my_scores'))

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $sangha->name }}</h1>
        <p class="mt-1 text-slate-600 dark:text-slate-400">{{ $sangha->monastery?->name }} @if($sangha->exam) · {{ $sangha->exam->name }} @if($sangha->exam?->exam_date) ({{ $sangha->exam->exam_date->format('M d, Y') }}) @endif @else · <span class="text-amber-600 dark:text-amber-400">{{ t('no_exam_assigned') }}</span> @endif</p>
        <p class="mt-2 text-slate-600 dark:text-slate-400">{{ t('dashboard_welcome_sangha') }}</p>
    </div>
    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors shrink-0">
        @include('partials.icon', ['name' => 'external-link', 'class' => 'w-4 h-4'])
        {{ t('back_to_website') }}
    </a>
</div>

<section class="mb-8" aria-labelledby="sangha-overview-heading">
    <h2 id="sangha-overview-heading" class="sr-only">{{ t('overview') }}</h2>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @if($sangha->exam)
            <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ t('exam') }}</p>
                <p class="mt-1 text-lg font-semibold text-slate-900 dark:text-slate-100 truncate" title="{{ $sangha->exam->name }}">{{ $sangha->exam->name }}</p>
                @if($sangha->exam->exam_date)
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ t('exam_date') }}: {{ $sangha->exam->exam_date->format('M d, Y') }}</p>
                @endif
            </div>
        @endif
        <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ t('subjects_count') }}</p>
            <p class="mt-1 text-3xl font-bold text-slate-900 dark:text-slate-100">{{ $subjectsCount }}</p>
            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ t('subjects_with_scores') }}</p>
        </div>
        @if($averageScore !== null)
            <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ t('average_score') }}</p>
                <p class="mt-1 text-3xl font-bold text-slate-900 dark:text-slate-100">{{ $averageScore }}</p>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ t('numeric_scores_only') }}</p>
            </div>
        @endif
    </div>
</section>

<section aria-labelledby="sangha-scores-heading">
    <h2 id="sangha-scores-heading" class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">{{ t('my_scores') }}</h2>
    <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 overflow-hidden shadow-sm">
    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700">
        <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ t('my_scores') }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-800/80">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">{{ t('subject') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">{{ t('score') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($scores as $score)
                    <tr>
                        <td class="px-6 py-4 text-slate-900 dark:text-slate-100">{{ $score->subject?->name ?? '—' }}</td>
                        <td class="px-6 py-4 font-medium text-slate-900 dark:text-slate-100">{{ $score->value ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">{{ t('no_scores') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </div>
</section>
@endsection
