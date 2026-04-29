@extends('admin.layout')

@section('title', t('monastery_details', 'Monastery details'))

@section('content')
<div class="admin-form-page-header">
    <a href="{{ route('admin.monasteries.index') }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) {{ t('monasteries') }}</a>
    <h1 class="admin-page-title">{{ $monastery->name ?: '—' }}</h1>
    <p class="text-slate-600 dark:text-slate-400 mt-1">{{ t('username') }}: <span class="font-mono">{{ $monastery->username ?: '—' }}</span></p>
</div>

<div class="rounded-xl border border-slate-200/80 dark:border-slate-600 bg-white dark:bg-slate-800 p-5 mb-6">
    <dl class="grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-3">
        <div>
            <dt class="text-slate-500 dark:text-slate-400">{{ t('status') }}</dt>
            <dd class="text-slate-900 dark:text-slate-100">
                @if($monastery->moderationStatus() === 'approved')
                    <span class="admin-badge-yes">Approved</span>
                @elseif($monastery->moderationStatus() === 'rejected')
                    <span class="admin-badge-rejected">Rejected</span>
                @else
                    <span class="admin-badge-pending">Pending</span>
                @endif
            </dd>
        </div>
        <div>
            <dt class="text-slate-500 dark:text-slate-400">{{ t('region') }}</dt>
            <dd class="text-slate-900 dark:text-slate-100">{{ $monastery->region ?: '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-500 dark:text-slate-400">{{ t('city') }}</dt>
            <dd class="text-slate-900 dark:text-slate-100">{{ $monastery->city ?: '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-500 dark:text-slate-400">{{ t('phone') }}</dt>
            <dd class="text-slate-900 dark:text-slate-100">{{ $monastery->phone ?: '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-500 dark:text-slate-400">{{ t('address') }}</dt>
            <dd class="text-slate-900 dark:text-slate-100">{{ $monastery->address ?: '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-500 dark:text-slate-400">{{ t('sanghas', 'Sanghas') }}</dt>
            <dd class="text-slate-900 dark:text-slate-100">{{ $sanghaCount }}</dd>
        </div>
    </dl>

    @if($monastery->moderationStatus() === 'rejected' && filled($monastery->rejection_reason))
        <div class="mt-5 rounded-lg border border-rose-200/80 dark:border-rose-500/40 bg-rose-50 dark:bg-rose-900/20 px-4 py-3 text-sm text-rose-700 dark:text-rose-300">
            <p class="font-semibold mb-1">Rejection Reason</p>
            <p>{{ $monastery->rejection_reason }}</p>
        </div>
    @endif

    @if(filled($monastery->description))
        <div class="mt-5">
            <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">{{ t('description') }}</h2>
            <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line">{{ $monastery->description }}</p>
        </div>
    @endif
</div>

@if(!empty($customFieldValues))
    <div class="admin-table-card">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-600 bg-gradient-to-r from-slate-50 to-slate-100/80 dark:from-slate-700 dark:to-slate-700/80">
            <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ t('additional_information', 'Additional information') }}</h2>
        </div>
        <div class="p-6">
            <dl class="grid gap-4 text-sm sm:grid-cols-2">
                @foreach($customFieldValues as $label => $value)
                    <div>
                        <dt class="text-slate-500 dark:text-slate-400">{{ $label }}</dt>
                        <dd class="text-slate-900 dark:text-slate-100 mt-1">{{ filled($value) ? $value : '—' }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </div>
@endif
@endsection
