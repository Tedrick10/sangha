@extends('admin.layout')

@section('title', t('dashboard'))

@section('content')
<div class="admin-page-header mb-8">
    <h1>{{ t('dashboard') }}</h1>
</div>

<p class="text-slate-600 dark:text-slate-400 mb-8 max-w-2xl">{{ t('dashboard_welcome_admin') }}</p>

{{-- Stats --}}
<section class="mb-10" aria-labelledby="dashboard-stats-heading">
    <h2 id="dashboard-stats-heading" class="sr-only">{{ t('overview') }}</h2>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <a href="{{ route('admin.monasteries.index') }}" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 p-6 shadow-sm hover:border-amber-500/50 dark:hover:border-amber-500/50 transition-colors group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ t('monasteries') }}</p>
                    <p class="mt-1 text-3xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['monasteries'] }}</p>
                </div>
                <span class="rounded-lg bg-amber-100 dark:bg-amber-900/30 p-3 text-amber-600 dark:text-amber-400 group-hover:bg-amber-200 dark:group-hover:bg-amber-900/50">
                    @include('partials.icon', ['name' => 'home', 'class' => 'w-8 h-8'])
                </span>
            </div>
        </a>
        <a href="{{ route('admin.sanghas.index') }}" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 p-6 shadow-sm hover:border-amber-500/50 dark:hover:border-amber-500/50 transition-colors group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ t('sanghas') }}</p>
                    <p class="mt-1 text-3xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['sanghas'] }}</p>
                </div>
                <span class="rounded-lg bg-amber-100 dark:bg-amber-900/30 p-3 text-amber-600 dark:text-amber-400 group-hover:bg-amber-200 dark:group-hover:bg-amber-900/50">
                    @include('partials.icon', ['name' => 'view', 'class' => 'w-8 h-8'])
                </span>
            </div>
        </a>
        <a href="{{ route('admin.exams.index') }}" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 p-6 shadow-sm hover:border-amber-500/50 dark:hover:border-amber-500/50 transition-colors group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ t('exams') }}</p>
                    <p class="mt-1 text-3xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['exams'] }}</p>
                </div>
                <span class="rounded-lg bg-amber-100 dark:bg-amber-900/30 p-3 text-amber-600 dark:text-amber-400 group-hover:bg-amber-200 dark:group-hover:bg-amber-900/50">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                </span>
            </div>
        </a>
        <a href="{{ route('admin.scores.index') }}" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 p-6 shadow-sm hover:border-amber-500/50 dark:hover:border-amber-500/50 transition-colors group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ t('scores') }}</p>
                    <p class="mt-1 text-3xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['scores'] }}</p>
                </div>
                <span class="rounded-lg bg-amber-100 dark:bg-amber-900/30 p-3 text-amber-600 dark:text-amber-400 group-hover:bg-amber-200 dark:group-hover:bg-amber-900/50">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                </span>
            </div>
        </a>
        <a href="{{ route('admin.users.index') }}" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 p-6 shadow-sm hover:border-amber-500/50 dark:hover:border-amber-500/50 transition-colors group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ t('users') }}</p>
                    <p class="mt-1 text-3xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['users'] }}</p>
                </div>
                <span class="rounded-lg bg-amber-100 dark:bg-amber-900/30 p-3 text-amber-600 dark:text-amber-400 group-hover:bg-amber-200 dark:group-hover:bg-amber-900/50">
                    @include('partials.icon', ['name' => 'cog', 'class' => 'w-8 h-8'])
                </span>
            </div>
        </a>
    </div>
</section>

{{-- Quick links --}}
<section class="mb-10" aria-labelledby="quick-links-heading">
    <h2 id="quick-links-heading" class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">{{ t('quick_links') }}</h2>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('admin.monasteries.create') }}" class="admin-btn-secondary">@include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) {{ t('add_monastery') }}</a>
        <a href="{{ route('admin.sanghas.create') }}" class="admin-btn-secondary">@include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) {{ t('add_sangha') }}</a>
        <a href="{{ route('admin.exams.create') }}" class="admin-btn-secondary">@include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) {{ t('add_exam') }}</a>
        <a href="{{ route('admin.scores.index') }}" class="admin-btn-secondary">@include('partials.icon', ['name' => 'view', 'class' => 'w-4 h-4']) {{ t('scores') }}</a>
        <a href="{{ route('admin.users.create') }}" class="admin-btn-secondary">@include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) {{ t('add_user') }}</a>
        <a href="{{ route('admin.websites.index') }}" class="admin-btn-secondary">@include('partials.icon', ['name' => 'view', 'class' => 'w-4 h-4']) {{ t('website') }}</a>
    </div>
</section>

{{-- Recent activity --}}
<div class="grid gap-6 lg:grid-cols-3">
    <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 overflow-hidden shadow-sm" aria-labelledby="recent-monasteries-heading">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
            <h2 id="recent-monasteries-heading" class="text-lg font-semibold text-slate-900 dark:text-slate-100">{{ t('recent_monasteries') }}</h2>
            <a href="{{ route('admin.monasteries.index') }}" class="text-sm font-medium text-amber-600 dark:text-amber-400 hover:underline">{{ t('view') }} →</a>
        </div>
        <div class="divide-y divide-slate-100 dark:divide-slate-700">
            @forelse($recentMonasteries as $m)
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="font-medium text-slate-900 dark:text-slate-100">{{ $m->name }}</span>
                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ $m->created_at?->format('M d, Y') }}</span>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-slate-500 dark:text-slate-400 text-sm">{{ t('no_monasteries') }}</div>
            @endforelse
        </div>
    </section>
    <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 overflow-hidden shadow-sm" aria-labelledby="recent-exams-heading">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
            <h2 id="recent-exams-heading" class="text-lg font-semibold text-slate-900 dark:text-slate-100">{{ t('recent_exams') }}</h2>
            <a href="{{ route('admin.exams.index') }}" class="text-sm font-medium text-amber-600 dark:text-amber-400 hover:underline">{{ t('view') }} →</a>
        </div>
        <div class="divide-y divide-slate-100 dark:divide-slate-700">
            @forelse($recentExams as $e)
                <div class="px-6 py-3 flex items-center justify-between gap-4">
                    <span class="font-medium text-slate-900 dark:text-slate-100 truncate">{{ $e->name }}</span>
                    <span class="text-sm text-slate-500 dark:text-slate-400 shrink-0">{{ $e->exam_date?->format('M d, Y') }} · {{ $e->monastery?->name ?? '—' }}</span>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-slate-500 dark:text-slate-400 text-sm">{{ t('no_exams') }}</div>
            @endforelse
        </div>
    </section>
    <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 overflow-hidden shadow-sm" aria-labelledby="recent-scores-heading">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
            <h2 id="recent-scores-heading" class="text-lg font-semibold text-slate-900 dark:text-slate-100">{{ t('recent_scores') }}</h2>
            <a href="{{ route('admin.scores.index') }}" class="text-sm font-medium text-amber-600 dark:text-amber-400 hover:underline">{{ t('view') }} →</a>
        </div>
        <div class="divide-y divide-slate-100 dark:divide-slate-700">
            @forelse($recentScores ?? [] as $score)
                <div class="px-6 py-3 flex items-center justify-between gap-4">
                    <span class="font-medium text-slate-900 dark:text-slate-100 truncate">{{ $score->sangha?->name ?? '—' }}</span>
                    <span class="text-sm text-slate-500 dark:text-slate-400 shrink-0">{{ $score->subject?->name ?? '—' }} · {{ $score->value ?? '—' }}</span>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-slate-500 dark:text-slate-400 text-sm">{{ t('no_scores') }}</div>
            @endforelse
        </div>
    </section>
</div>
@endsection
