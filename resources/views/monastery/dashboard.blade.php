@extends('layouts.monastery')

@section('title', t('monastery_portal'))

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ t('monastery_portal') }} — {{ $monastery->name }}</h1>
    <p class="mt-1 text-slate-600 dark:text-slate-400">{{ t('monastery_portal_open_screen_hint', 'Tap a container to open a separate screen.') }}</p>
    </div>

@if($tab === 'main')
    @if($screen === 'main-home')
        <section class="mb-8">
            <div class="grid grid-cols-3 gap-3 sm:gap-4">
                <a href="{{ route('monastery.dashboard', ['tab' => 'main', 'screen' => 'total']) }}" class="aspect-square rounded-2xl border p-3.5 shadow-sm flex flex-col justify-between border-slate-200 dark:border-slate-700 bg-gradient-to-br from-white to-slate-50 dark:from-slate-800/70 dark:to-slate-900/60 hover:border-amber-300 hover:shadow-md transition-all">
                    <div class="inline-flex items-center gap-2">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M6 7V5h12v2M6 11h12v8H6z" /></svg>
                        </span>
                        <p class="text-[11px] uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ t('total', 'Total') }}</p>
                    </div>
                    <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $totalApplications }}</p>
                </a>
                <a href="{{ route('monastery.dashboard', ['tab' => 'main', 'screen' => 'register']) }}" class="aspect-square rounded-2xl border p-3.5 shadow-sm flex flex-col justify-between border-slate-200 dark:border-slate-700 bg-gradient-to-br from-white to-slate-50 dark:from-slate-800/70 dark:to-slate-900/60 hover:border-amber-300 hover:shadow-md transition-all">
                    <div class="inline-flex items-center gap-2">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        </span>
                        <p class="text-[11px] uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ t('register', 'Register') }}</p>
                    </div>
                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ t('new_sangha', 'New Sangha') }}</p>
                </a>
                <a href="{{ route('monastery.dashboard', ['tab' => 'main', 'screen' => 'pending']) }}" class="aspect-square rounded-2xl border p-3.5 shadow-sm flex flex-col justify-between border-slate-200 dark:border-slate-700 bg-gradient-to-br from-white to-slate-50 dark:from-slate-800/70 dark:to-slate-900/60 hover:border-amber-300 hover:shadow-md transition-all">
                    <div class="inline-flex items-center gap-2">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3M12 22a10 10 0 100-20 10 10 0 000 20z" /></svg>
                        </span>
                        <p class="text-[11px] uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ t('pending', 'Pending') }}</p>
                    </div>
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-300">{{ $pendingCount }}</p>
                </a>
                <a href="{{ route('monastery.dashboard', ['tab' => 'main', 'screen' => 'approved']) }}" class="aspect-square rounded-2xl border p-3.5 shadow-sm flex flex-col justify-between border-slate-200 dark:border-slate-700 bg-gradient-to-br from-white to-slate-50 dark:from-slate-800/70 dark:to-slate-900/60 hover:border-amber-300 hover:shadow-md transition-all">
                    <div class="inline-flex items-center gap-2">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </span>
                        <p class="text-[11px] uppercase tracking-wide text-slate-500 dark:text-slate-400">Approved</p>
                    </div>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-300">{{ $approvedCount }}</p>
                </a>
                <a href="{{ route('monastery.dashboard', ['tab' => 'main', 'screen' => 'rejected']) }}" class="aspect-square rounded-2xl border p-3.5 shadow-sm flex flex-col justify-between border-slate-200 dark:border-slate-700 bg-gradient-to-br from-white to-slate-50 dark:from-slate-800/70 dark:to-slate-900/60 hover:border-amber-300 hover:shadow-md transition-all">
                    <div class="inline-flex items-center gap-2">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </span>
                        <p class="text-[11px] uppercase tracking-wide text-slate-500 dark:text-slate-400">Rejected</p>
                    </div>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-300">{{ $rejectedCount }}</p>
                </a>
                <a href="{{ route('monastery.dashboard', ['tab' => 'main', 'screen' => 'request']) }}" class="aspect-square rounded-2xl border p-3.5 shadow-sm flex flex-col justify-between border-slate-200 dark:border-slate-700 bg-gradient-to-br from-white to-slate-50 dark:from-slate-800/70 dark:to-slate-900/60 hover:border-amber-300 hover:shadow-md transition-all">
                    <div class="inline-flex items-center gap-2">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h8M8 14h5M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.257-3.771C3.468 15.03 3 13.56 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                        </span>
                        <p class="text-[11px] uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ t('request', 'Request') }}</p>
                    </div>
                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ t('open_chat', 'Open Chat') }}</p>
    </a>
</div>
        </section>
    @endif

    @if($screen !== 'main-home')
        <div class="mb-4">
            <a href="{{ route('monastery.dashboard', ['tab' => 'main', 'screen' => 'main-home']) }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 px-3 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">
                @include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4'])
                {{ t('back_to_main', 'Back to Main') }}
            </a>
        </div>
    @endif

    @if($screen === 'total')
        <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 p-5 sm:p-6 shadow-sm mb-8">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">{{ t('overall_summary', 'Overall Summary') }}</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase">{{ t('total_applications', 'Total Applications') }}</p>
                    <p class="text-2xl font-bold mt-1">{{ $totalApplications }}</p>
        </div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase">{{ t('recent_score_records', 'Recent Score Records') }}</p>
                    <p class="text-2xl font-bold mt-1">{{ $scoresCount }}</p>
        </div>
    </div>
</section>
    @endif

    @if($screen === 'register')
        <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 p-5 sm:p-6 shadow-sm mb-8">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">{{ t('sangha_application_form', 'Sangha Application Form') }}</h2>
            <form action="{{ route('monastery.sanghas.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ t('sangha_name', 'Sangha Name') }} *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required class="mt-1 w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900/60 px-3 py-2.5 text-sm">
                        @error('name')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="username" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Username *</label>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required class="mt-1 w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900/60 px-3 py-2.5 text-sm">
                        @error('username')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Password *</label>
                        <input type="password" id="password" name="password" required class="mt-1 w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900/60 px-3 py-2.5 text-sm">
                        @error('password')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Confirm Password *</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required class="mt-1 w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900/60 px-3 py-2.5 text-sm">
                    </div>
                </div>
                <div>
                    <label for="exam_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Exam</label>
                    <select id="exam_id" name="exam_id" class="mt-1 w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900/60 px-3 py-2.5 text-sm">
                        <option value="">{{ t('select_exam_optional', 'Select exam (optional)') }}</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}" {{ (string) old('exam_id') === (string) $exam->id ? 'selected' : '' }}>{{ $exam->name }}{{ $exam->exam_date ? ' (' . $exam->exam_date->format('M d, Y') . ')' : '' }}</option>
                        @endforeach
                    </select>
                    @error('exam_id')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Description</label>
                    <textarea id="description" name="description" rows="3" class="mt-1 w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900/60 px-3 py-2.5 text-sm">{{ old('description') }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                @if($sanghaCustomFields->isNotEmpty())
                    <div class="grid gap-4 sm:grid-cols-2">
                        @include('website.partials.custom-fields', [
                            'customFields' => $sanghaCustomFields,
                            'idPrefix' => 'monastery_sangha',
                            'oldPrefix' => null,
                        ])
                    </div>
                @endif
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-amber-500 hover:bg-amber-400 text-white text-sm font-semibold transition-colors">
                    {{ t('submit_sangha_application', 'Submit Sangha Application') }}
                </button>
            </form>
        </section>
    @endif

    @if($screen === 'pending')
        <section class="rounded-xl border border-amber-200/70 dark:border-amber-800/50 bg-white dark:bg-slate-800/50 shadow-sm overflow-hidden mb-8">
            <div class="px-4 py-3 border-b border-amber-100 dark:border-amber-900/40 bg-amber-50/60 dark:bg-amber-900/10">
                <h2 class="text-sm font-semibold text-amber-800 dark:text-amber-300 uppercase tracking-wider">{{ t('pending_sangha_list', 'Pending Sangha List') }}</h2>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-700 max-h-[520px] overflow-y-auto">
                @forelse($pendingSanghas as $sangha)
                    <div class="px-4 py-3">
                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ $sangha->name }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $sangha->exam?->name ?? t('no_exam_selected', 'No exam selected') }}</p>
                    </div>
                @empty
                    <p class="px-4 py-6 text-sm text-slate-500 dark:text-slate-400">{{ t('no_pending_sangha_applications', 'No pending sangha applications.') }}</p>
                @endforelse
            </div>
        </section>
    @endif

    @if($screen === 'approved')
        <section class="rounded-xl border border-emerald-200/70 dark:border-emerald-800/50 bg-white dark:bg-slate-800/50 shadow-sm overflow-hidden mb-8">
            <div class="px-4 py-3 border-b border-emerald-100 dark:border-emerald-900/40 bg-emerald-50/60 dark:bg-emerald-900/10">
                <h2 class="text-sm font-semibold text-emerald-800 dark:text-emerald-300 uppercase tracking-wider">{{ t('approved_sangha_list', 'Approved Sangha List') }}</h2>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-700 max-h-[520px] overflow-y-auto">
                @forelse($approvedSanghas as $sangha)
                    <div class="px-4 py-3">
                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ $sangha->name }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $sangha->exam?->name ?? t('no_exam_selected', 'No exam selected') }}</p>
                    </div>
                @empty
                    <p class="px-4 py-6 text-sm text-slate-500 dark:text-slate-400">{{ t('no_approved_sanghas_yet', 'No approved sanghas yet.') }}</p>
                @endforelse
            </div>
        </section>
    @endif

    @if($screen === 'rejected')
        <section class="rounded-xl border border-red-200/70 dark:border-red-800/50 bg-white dark:bg-slate-800/50 shadow-sm overflow-hidden mb-8">
            <div class="px-4 py-3 border-b border-red-100 dark:border-red-900/40 bg-red-50/60 dark:bg-red-900/10">
                <h2 class="text-sm font-semibold text-red-800 dark:text-red-300 uppercase tracking-wider">{{ t('rejected_sangha_list', 'Rejected Sangha List') }}</h2>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-700 max-h-[520px] overflow-y-auto">
                @forelse($rejectedSanghas as $sangha)
                    <div class="px-4 py-3">
                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ $sangha->name }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $sangha->exam?->name ?? t('no_exam_selected', 'No exam selected') }}</p>
                        @if($sangha->rejection_reason)
                            <details class="mt-2 max-w-md group">
                                <summary class="list-none cursor-pointer inline-flex items-center gap-2 rounded-lg border border-red-200/80 dark:border-red-800/70 bg-red-50/80 dark:bg-red-900/20 px-3 py-1.5 text-xs font-semibold text-red-700 dark:text-red-300">
                                    {{ t('view_rejection_reason', 'View Rejection Reason') }}
                                    <span class="transition-transform group-open:rotate-180">▾</span>
                                </summary>
                                <div class="mt-2 rounded-lg border border-red-200/80 dark:border-red-800/70 bg-red-50/70 dark:bg-red-900/15 p-2.5">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-red-700 dark:text-red-300">{{ t('rejection_note', 'Rejection Note') }}</p>
                                    <div class="mt-1 max-h-28 overflow-y-auto pr-1">
                                        <p class="text-xs leading-relaxed text-red-700/90 dark:text-red-200/90 break-words whitespace-pre-wrap">{{ $sangha->rejection_reason }}</p>
                                    </div>
                                </div>
                            </details>
                        @endif
                    </div>
                @empty
                    <p class="px-4 py-6 text-sm text-slate-500 dark:text-slate-400">{{ t('no_rejected_sanghas', 'No rejected sanghas.') }}</p>
                @endforelse
            </div>
        </section>
    @endif

    @if($screen === 'request')
        <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 p-5 sm:p-6 shadow-sm mb-8">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">{{ t('request_form_super_admin', 'Request Form (Super Admin)') }}</h2>
            <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-900/40 p-4 mb-4 max-h-[280px] overflow-y-auto space-y-3">
                @forelse($messages as $message)
                    <div class="flex {{ $message->sender_type === 'admin' ? 'justify-start' : 'justify-end' }}">
                        <div class="max-w-[90%] rounded-xl px-3 py-2 text-sm {{ $message->sender_type === 'admin' ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 border border-slate-200 dark:border-slate-600' : 'bg-amber-500/20 text-slate-900 dark:text-slate-100 border border-amber-400/40' }}">
                            <p class="text-[11px] font-semibold mb-1 {{ $message->sender_type === 'admin' ? 'text-slate-500 dark:text-slate-400' : 'text-amber-700 dark:text-amber-300' }}">
                                {{ $message->sender_type === 'admin' ? ($message->user->name ?? t('super_admin', 'Super Admin')) : t('you', 'You') }}
                            </p>
                            @if(!empty($message->payload_json) && is_array($message->payload_json))
                                <div class="mb-2 rounded-lg border border-slate-200/80 dark:border-slate-600/70 bg-white/70 dark:bg-slate-900/40 px-2.5 py-2 space-y-1">
                                    @foreach($message->payload_json as $item)
                                        <p class="text-xs text-slate-600 dark:text-slate-300">
                                            <span class="font-semibold">{{ $item['label'] ?? t('field', 'Field') }}:</span>
                                            <span>
                                                @if(is_array($item['value'] ?? null))
                                                    {{ implode(', ', $item['value']) }}
                                                @else
                                                    {{ $item['value'] ?? t('empty_dash', '—') }}
                                                @endif
                                            </span>
                                        </p>
                                    @endforeach
                                </div>
                            @endif
                            <p class="whitespace-pre-wrap break-words">{{ $message->message }}</p>
                            <p class="text-[10px] mt-1 text-slate-500 dark:text-slate-400">{{ $message->created_at?->format('M d, H:i') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ t('no_messages_send_first_request', 'No messages yet. Send your first request.') }}</p>
                @endforelse
            </div>
            <form action="{{ route('monastery.messages.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                @if($requestCustomFields->isNotEmpty())
                    <div class="grid gap-4 sm:grid-cols-2">
                        @include('website.partials.custom-fields', [
                            'customFields' => $requestCustomFields,
                            'idPrefix' => 'monastery_request',
                            'oldPrefix' => null,
                        ])
                    </div>
                @endif
                <div>
                    <label for="message" class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ t('additional_message', 'Additional Message') }}</label>
                    <textarea id="message" name="message" rows="4" class="mt-1 w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900/60 px-3 py-2.5 text-sm" placeholder="{{ t('write_request_to_super_admin', 'Write your request to Super Admin...') }}">{{ old('message') }}</textarea>
                    @error('message')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-slate-800 dark:bg-slate-700 hover:bg-slate-700 dark:hover:bg-slate-600 text-white text-sm font-semibold transition-colors">
                    {{ t('send_message', 'Send Message') }}
                </button>
            </form>
        </section>
    @endif
@else
    @if($screen === 'results-home')
        <section class="mb-8">
            <div class="grid grid-cols-2 gap-3 sm:gap-4">
                <a href="{{ route('monastery.dashboard', ['tab' => 'results', 'screen' => 'pass']) }}" class="aspect-square rounded-2xl border border-slate-200 dark:border-slate-700 bg-gradient-to-br from-white to-emerald-50/40 dark:from-slate-800/70 dark:to-slate-900/60 p-4 shadow-sm flex flex-col justify-between hover:border-emerald-300 hover:shadow-md transition-all">
                    <div class="inline-flex items-center gap-2">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </span>
                        <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ t('pass_list', 'Pass List') }}</p>
                    </div>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-300">{{ $passSanghas->count() }}</p>
                </a>
                <a href="{{ route('monastery.dashboard', ['tab' => 'results', 'screen' => 'fail']) }}" class="aspect-square rounded-2xl border border-slate-200 dark:border-slate-700 bg-gradient-to-br from-white to-rose-50/40 dark:from-slate-800/70 dark:to-slate-900/60 p-4 shadow-sm flex flex-col justify-between hover:border-red-300 hover:shadow-md transition-all">
                    <div class="inline-flex items-center gap-2">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </span>
                        <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ t('fail_list', 'Fail List') }}</p>
                    </div>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-300">{{ $failSanghas->count() }}</p>
                </a>
            </div>
        </section>
    @endif

    @if($screen === 'pass' || $screen === 'fail')
        <div class="mb-4">
            <a href="{{ route('monastery.dashboard', ['tab' => 'results', 'screen' => 'results-home']) }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 px-3 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">
                @include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4'])
                {{ t('back_to_results', 'Back to Results') }}
            </a>
        </div>
    @endif

    @if($screen === 'pass')
        <section class="rounded-xl border border-emerald-200/70 dark:border-emerald-800/50 overflow-hidden mb-8">
            <div class="px-4 py-3 border-b border-emerald-100 dark:border-emerald-900/40 bg-emerald-50/60 dark:bg-emerald-900/10">
                <h3 class="text-sm font-semibold text-emerald-800 dark:text-emerald-300 uppercase tracking-wider">{{ t('pass_sangha_list', 'Pass Sangha List') }}</h3>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-700 max-h-[520px] overflow-y-auto bg-white dark:bg-slate-800/50">
                @forelse($passSanghas as $sangha)
                    <div class="px-4 py-3">
                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ $sangha->name }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $sangha->exam?->name ?? t('no_exam_selected', 'No exam selected') }}</p>
                    </div>
                @empty
                    <p class="px-4 py-6 text-sm text-slate-500 dark:text-slate-400">{{ t('no_pass_sanghas_found', 'No pass sanghas found.') }}</p>
                @endforelse
            </div>
        </section>
    @endif

    @if($screen === 'fail')
        <section class="rounded-xl border border-red-200/70 dark:border-red-800/50 overflow-hidden mb-8">
            <div class="px-4 py-3 border-b border-red-100 dark:border-red-900/40 bg-red-50/60 dark:bg-red-900/10">
                <h3 class="text-sm font-semibold text-red-800 dark:text-red-300 uppercase tracking-wider">{{ t('fail_sangha_list', 'Fail Sangha List') }}</h3>
        </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-700 max-h-[520px] overflow-y-auto bg-white dark:bg-slate-800/50">
                @forelse($failSanghas as $sangha)
                    <div class="px-4 py-3">
                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ $sangha->name }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $sangha->exam?->name ?? t('no_exam_selected', 'No exam selected') }}</p>
                    </div>
                @empty
                    <p class="px-4 py-6 text-sm text-slate-500 dark:text-slate-400">{{ t('no_fail_sanghas_found', 'No fail sanghas found.') }}</p>
                @endforelse
        </div>
    </section>
    @endif
@endif

<nav class="fixed bottom-4 left-1/2 -translate-x-1/2 z-50 w-[calc(100%-2rem)] max-w-md rounded-2xl border border-slate-200 dark:border-slate-700 bg-white/95 dark:bg-slate-900/95 backdrop-blur px-2 py-2 shadow-xl">
    <div class="grid grid-cols-2 gap-2">
        <a href="{{ route('monastery.dashboard', ['tab' => 'main', 'screen' => 'main-home']) }}" class="inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold {{ $tab === 'main' ? 'bg-amber-500 text-white' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800' }}">{{ t('main', 'Main') }}</a>
        <a href="{{ route('monastery.dashboard', ['tab' => 'results', 'screen' => 'results-home']) }}" class="inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold {{ $tab === 'results' ? 'bg-amber-500 text-white' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800' }}">{{ t('results', 'Results') }}</a>
</div>
</nav>
@endsection
