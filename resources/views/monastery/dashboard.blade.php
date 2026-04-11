@extends('layouts.monastery')

@section('title', t('monastery_portal'))

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ t('monastery_portal') }} — {{ $monastery->name }}</h1>
    <p class="mt-1 text-slate-600 dark:text-slate-400">
        @if($tab === 'results')
            {{ t('monastery_results_page_hint', 'Review published pass and fail lists for your monastery.') }}
        @elseif($tab === 'exam')
            {{ t('monastery_exam_tab_hint', 'Choose an exam programme, then upload the hall form and attachments for your sangha.') }}
        @elseif($tab === 'chat')
            {{ t('monastery_chat_tab_hint', 'Message administrators in real time. New replies appear automatically.') }}
        @else
            {{ t('monastery_portal_open_screen_hint', 'Tap a container to open a separate screen.') }}
        @endif
    </p>
</div>

@if($tab === 'main')
    @if($screen === 'main-home')
        @php
            $mpTile = 'group relative flex aspect-square min-h-[5.5rem] flex-col overflow-hidden rounded-2xl border-none bg-gradient-to-b from-white via-white to-slate-50/95 p-3.5 shadow-md shadow-slate-200/40 ring-0 outline-none transition-all duration-200 ease-out hover:-translate-y-0.5 hover:shadow-lg hover:shadow-slate-300/40 active:translate-y-0 active:scale-[0.98] focus-visible:outline-none focus-visible:ring-0 focus-visible:ring-offset-0 dark:from-slate-800 dark:via-slate-800/95 dark:to-slate-900/90 dark:shadow-black/30 dark:hover:shadow-lg dark:hover:shadow-black/40 sm:min-h-0 sm:p-5';
        @endphp
        <section class="mb-8">
            <div class="grid grid-cols-2 gap-3 min-[480px]:grid-cols-3 sm:gap-4">
                <a href="{{ route('monastery.dashboard', ['tab' => 'main', 'screen' => 'total']) }}" class="{{ $mpTile }}">
                    <span class="pointer-events-none absolute inset-0 bg-gradient-to-br from-blue-500/[0.06] via-transparent to-transparent opacity-80 dark:from-blue-400/[0.08]"></span>
                    <div class="relative flex flex-1 flex-col items-center justify-center gap-2 text-center">
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-700 shadow-sm ring-0 dark:bg-blue-950/50 dark:text-blue-300 sm:h-12 sm:w-12">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M6 7V5h12v2M6 11h12v8H6z" /></svg>
                        </span>
                        <p class="max-w-[95%] text-xs font-semibold uppercase leading-tight tracking-wide text-slate-500 [text-wrap:balance] dark:text-slate-400 sm:text-sm">{{ t('total', 'Total') }}</p>
                        <p class="text-3xl font-bold tabular-nums tracking-tight text-slate-900 dark:text-slate-50 sm:text-4xl">{{ $totalApplications }}</p>
                    </div>
                    <span class="absolute bottom-2.5 right-2.5 text-slate-400 transition duration-200 group-hover:text-amber-600 dark:text-slate-500 dark:group-hover:text-amber-400" aria-hidden="true">@include('partials.icon', ['name' => 'chevron-right', 'class' => 'h-4 w-4 opacity-50 group-hover:opacity-100 group-hover:translate-x-0.5 transition-all'])</span>
                </a>
                <a href="{{ route('monastery.dashboard', ['tab' => 'main', 'screen' => 'register']) }}" class="{{ $mpTile }}">
                    <span class="pointer-events-none absolute inset-0 bg-gradient-to-br from-indigo-500/[0.06] via-transparent to-transparent opacity-80 dark:from-indigo-400/[0.08]"></span>
                    <div class="relative flex flex-1 flex-col items-center justify-center gap-2 text-center">
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700 shadow-sm ring-0 dark:bg-indigo-950/50 dark:text-indigo-300 sm:h-12 sm:w-12">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        </span>
                        <p class="max-w-[95%] text-xs font-semibold uppercase leading-tight tracking-wide text-slate-500 [text-wrap:balance] dark:text-slate-400 sm:text-sm">{{ t('register', 'Register') }}</p>
                        <p class="text-base font-bold leading-snug text-slate-900 dark:text-slate-100 sm:text-lg">{{ t('new_sangha', 'New Sangha') }}</p>
                    </div>
                    <span class="absolute bottom-2.5 right-2.5 text-slate-400 transition duration-200 group-hover:text-amber-600 dark:text-slate-500 dark:group-hover:text-amber-400" aria-hidden="true">@include('partials.icon', ['name' => 'chevron-right', 'class' => 'h-4 w-4 opacity-50 group-hover:opacity-100 group-hover:translate-x-0.5 transition-all'])</span>
                </a>
                <a href="{{ route('monastery.dashboard', ['tab' => 'main', 'screen' => 'pending']) }}" class="{{ $mpTile }}">
                    <span class="pointer-events-none absolute inset-0 bg-gradient-to-br from-amber-500/[0.08] via-transparent to-transparent opacity-80 dark:from-amber-400/[0.1]"></span>
                    <div class="relative flex flex-1 flex-col items-center justify-center gap-2 text-center">
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100 text-amber-700 shadow-sm ring-0 dark:bg-amber-950/40 dark:text-amber-300 sm:h-12 sm:w-12">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3M12 22a10 10 0 100-20 10 10 0 000 20z" /></svg>
                        </span>
                        <p class="max-w-[95%] text-xs font-semibold uppercase leading-tight tracking-wide text-slate-500 [text-wrap:balance] dark:text-slate-400 sm:text-sm">{{ t('pending', 'Pending') }}</p>
                        <p class="text-3xl font-bold tabular-nums tracking-tight text-amber-600 dark:text-amber-300 sm:text-4xl">{{ $pendingCount }}</p>
                    </div>
                    <span class="absolute bottom-2.5 right-2.5 text-slate-400 transition duration-200 group-hover:text-amber-600 dark:text-slate-500 dark:group-hover:text-amber-400" aria-hidden="true">@include('partials.icon', ['name' => 'chevron-right', 'class' => 'h-4 w-4 opacity-50 group-hover:opacity-100 group-hover:translate-x-0.5 transition-all'])</span>
                </a>
                <a href="{{ route('monastery.dashboard', ['tab' => 'main', 'screen' => 'approved']) }}" class="{{ $mpTile }}">
                    <span class="pointer-events-none absolute inset-0 bg-gradient-to-br from-emerald-500/[0.06] via-transparent to-transparent opacity-80 dark:from-emerald-400/[0.08]"></span>
                    <div class="relative flex flex-1 flex-col items-center justify-center gap-2 text-center">
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 shadow-sm ring-0 dark:bg-emerald-950/50 dark:text-emerald-300 sm:h-12 sm:w-12">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </span>
                        <p class="max-w-[95%] text-xs font-semibold uppercase leading-tight tracking-wide text-slate-500 [text-wrap:balance] dark:text-slate-400 sm:text-sm">{{ t('approved', 'Approved') }}</p>
                        <p class="text-3xl font-bold tabular-nums tracking-tight text-emerald-600 dark:text-emerald-300 sm:text-4xl">{{ $approvedCount }}</p>
                    </div>
                    <span class="absolute bottom-2.5 right-2.5 text-slate-400 transition duration-200 group-hover:text-amber-600 dark:text-slate-500 dark:group-hover:text-amber-400" aria-hidden="true">@include('partials.icon', ['name' => 'chevron-right', 'class' => 'h-4 w-4 opacity-50 group-hover:opacity-100 group-hover:translate-x-0.5 transition-all'])</span>
                </a>
                <a href="{{ route('monastery.dashboard', ['tab' => 'main', 'screen' => 'rejected']) }}" class="{{ $mpTile }}">
                    <span class="pointer-events-none absolute inset-0 bg-gradient-to-br from-rose-500/[0.07] via-transparent to-transparent opacity-80 dark:from-rose-400/[0.09]"></span>
                    <div class="relative flex flex-1 flex-col items-center justify-center gap-2 text-center">
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-rose-100 text-rose-700 shadow-sm ring-0 dark:bg-rose-950/50 dark:text-rose-300 sm:h-12 sm:w-12">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </span>
                        <p class="max-w-[95%] text-xs font-semibold uppercase leading-tight tracking-wide text-slate-500 [text-wrap:balance] dark:text-slate-400 sm:text-sm">{{ t('rejected', 'Rejected') }}</p>
                        <p class="text-3xl font-bold tabular-nums tracking-tight text-red-600 dark:text-red-300 sm:text-4xl">{{ $rejectedCount }}</p>
                    </div>
                    <span class="absolute bottom-2.5 right-2.5 text-slate-400 transition duration-200 group-hover:text-amber-600 dark:text-slate-500 dark:group-hover:text-amber-400" aria-hidden="true">@include('partials.icon', ['name' => 'chevron-right', 'class' => 'h-4 w-4 opacity-50 group-hover:opacity-100 group-hover:translate-x-0.5 transition-all'])</span>
                </a>
                <a href="{{ route('monastery.dashboard', ['tab' => 'main', 'screen' => 'request']) }}" class="{{ $mpTile }}">
                    <span class="pointer-events-none absolute inset-0 bg-gradient-to-br from-violet-500/[0.06] via-transparent to-transparent opacity-80 dark:from-violet-400/[0.08]"></span>
                    <div class="relative flex flex-1 flex-col items-center justify-center gap-2 px-0.5 text-center">
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-violet-100 text-violet-700 shadow-sm ring-0 dark:bg-violet-950/50 dark:text-violet-300 sm:h-12 sm:w-12">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h8M8 14h5M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.257-3.771C3.468 15.03 3 13.56 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                        </span>
                        <p class="max-w-[95%] text-xs font-semibold uppercase leading-tight tracking-wide text-slate-500 [text-wrap:balance] dark:text-slate-400 sm:text-sm">{{ t('monastery_requests', 'Transfer Sangha') }}</p>
                    </div>
                    <span class="absolute bottom-2.5 right-2.5 text-slate-400 transition duration-200 group-hover:text-amber-600 dark:text-slate-500 dark:group-hover:text-amber-400" aria-hidden="true">@include('partials.icon', ['name' => 'chevron-right', 'class' => 'h-4 w-4 opacity-50 group-hover:opacity-100 group-hover:translate-x-0.5 transition-all'])</span>
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
        @php
            $mpMetaName = $sanghaFieldMeta->get('name');
            $mpMetaFather = $sanghaFieldMeta->get('father_name');
            $mpMetaNrc = $sanghaFieldMeta->get('nrc_number');
            $mpMetaExam = $sanghaFieldMeta->get('exam_id');
            $mpMetaDesc = $sanghaFieldMeta->get('description');
        @endphp
        <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 p-5 sm:p-6 shadow-sm mb-8">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">{{ t('new_sangha', 'New Sangha') }}</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ t('sangha_application_form', 'Sangha Application Form') }}</p>
            </div>
            <form action="{{ route('monastery.sanghas.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $mpMetaName?->name ?? t('sangha_name', 'Sangha Name') }}{{ ($mpMetaName?->required ?? true) ? ' *' : '' }}</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" class="mt-1 w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900/60 px-3 py-2.5 text-sm" placeholder="{{ $mpMetaName?->placeholder }}" @if($mpMetaName?->required ?? true) required @endif>
                        @error('name')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="father_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $mpMetaFather?->name ?? t('score_father_name_label', 'Father name') }}{{ ($mpMetaFather?->required ?? false) ? ' *' : '' }}</label>
                        <input type="text" id="father_name" name="father_name" value="{{ old('father_name') }}" maxlength="255" class="mt-1 w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900/60 px-3 py-2.5 text-sm" placeholder="{{ $mpMetaFather?->placeholder }}" @if($mpMetaFather?->required ?? false) required @endif>
                        @error('father_name')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="nrc_number" class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $mpMetaNrc?->name ?? t('score_nrc_label', 'NRC number') }}{{ ($mpMetaNrc?->required ?? false) ? ' *' : '' }}</label>
                        <input type="text" id="nrc_number" name="nrc_number" value="{{ old('nrc_number') }}" maxlength="100" class="mt-1 w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900/60 px-3 py-2.5 text-sm" placeholder="{{ $mpMetaNrc?->placeholder }}" @if($mpMetaNrc?->required ?? false) required @endif>
                        @error('nrc_number')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label for="exam_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $mpMetaExam?->name ?? t('exam') }}{{ ($mpMetaExam?->required ?? false) ? ' *' : '' }}</label>
                    <select id="exam_id" name="exam_id" class="mt-1 w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900/60 px-3 py-2.5 text-sm" @if($mpMetaExam?->required ?? false) required @endif>
                        <option value="">{{ $mpMetaExam?->placeholder ?: (($mpMetaExam?->required ?? false) ? t('select_exam', 'Select exam') : t('select_exam_optional', 'Select exam (optional)')) }}</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}" {{ (string) old('exam_id') === (string) $exam->id ? 'selected' : '' }}>{{ $exam->name }}{{ $exam->exam_date ? ' (' . $exam->exam_date->format('M d, Y') . ')' : '' }}</option>
                        @endforeach
                    </select>
                    @error('exam_id')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $mpMetaDesc?->name ?? t('description') }}{{ ($mpMetaDesc?->required ?? false) ? ' *' : '' }}</label>
                    <textarea id="description" name="description" rows="3" class="mt-1 w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900/60 px-3 py-2.5 text-sm" placeholder="{{ $mpMetaDesc?->placeholder }}" @if($mpMetaDesc?->required ?? false) required @endif>{{ old('description') }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ t('sangha_portal_no_student_id_hint', 'Student Id for login is assigned by an administrator after review.') }}</p>
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
        <section class="mb-8 overflow-hidden rounded-3xl border border-slate-200/70 bg-gradient-to-b from-white to-slate-50/90 shadow-md shadow-slate-200/30 ring-1 ring-slate-900/[0.03] dark:border-slate-700/55 dark:from-slate-900/90 dark:to-slate-950/90 dark:shadow-black/20 dark:ring-white/[0.04]">
            <div class="relative px-5 py-5 sm:px-8 sm:py-6">
                <div class="absolute left-0 top-0 h-1 w-full bg-gradient-to-r from-amber-400/80 via-amber-500/70 to-amber-600/60 dark:from-amber-500/50 dark:via-amber-600/40 dark:to-amber-700/30" aria-hidden="true"></div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-amber-700/90 dark:text-amber-400/90">{{ t('monastery_transfer_sangha_kicker', 'Submit a transfer') }}</p>
                        <h2 class="mt-1.5 text-xl font-semibold tracking-tight text-slate-900 dark:text-slate-50 sm:text-2xl">{{ t('monastery_requests', 'Transfer Sangha') }}</h2>
                        <p class="mt-2 max-w-xl text-sm leading-relaxed text-slate-600 dark:text-slate-400">{{ t('request_form_portal_hint', 'Submit a request using the fields below. It will appear as Pending for administrators until they approve or reject it.') }}</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-200/60 bg-slate-50/50 px-5 py-6 sm:px-8 sm:py-8 dark:border-slate-800/80 dark:bg-slate-950/35">
                <div class="mx-auto grid max-w-6xl grid-cols-1 gap-8 lg:grid-cols-12 lg:gap-10">
                    {{-- Form: left, generous column --}}
                    <div class="min-w-0 lg:col-span-7">
                        <form action="{{ route('monastery.messages.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                            @csrf
                            @if($requestCustomFields->isNotEmpty())
                                <div class="monastery-request-form-fields space-y-4">
                                    @include('website.partials.custom-fields', [
                                        'customFields' => $requestCustomFields,
                                        'idPrefix' => 'monastery_request',
                                        'oldPrefix' => null,
                                        'variant' => 'monastery',
                                    ])
                                </div>
                                <button type="submit" class="flex w-full items-center justify-center rounded-xl bg-amber-600 px-5 py-3 text-sm font-semibold text-white shadow-md shadow-amber-900/15 transition hover:bg-amber-500 active:scale-[0.995] dark:bg-amber-600 dark:shadow-amber-950/30 dark:hover:bg-amber-500">
                                    {{ t('submit_request', 'Submit request') }}
                                </button>
                            @else
                                <p class="rounded-2xl border border-amber-200/80 bg-amber-50/90 px-5 py-4 text-sm text-amber-900 dark:border-amber-800/40 dark:bg-amber-950/30 dark:text-amber-200">{{ t('no_request_fields_configured', 'No request form fields are configured yet. Please contact the administrator.') }}</p>
                            @endif
                        </form>
                    </div>

                    {{-- Recent requests: compact list --}}
                    <aside class="min-w-0 w-full max-w-xl lg:max-w-none lg:col-span-5 lg:sticky lg:top-24 lg:self-start">
                        <div class="rounded-2xl border border-slate-200/80 bg-white/95 p-4 shadow-sm dark:border-slate-700/60 dark:bg-slate-900/80 dark:shadow-none sm:p-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ t('your_recent_requests', 'Your recent requests') }}</h3>
                                <span class="inline-flex min-w-[1.5rem] items-center justify-center rounded-full bg-slate-200/90 px-1.5 py-0.5 text-[11px] font-bold tabular-nums text-slate-800 dark:bg-slate-700 dark:text-slate-100" aria-label="{{ t('request_count', 'Request count') }}">{{ $myFormRequests->count() }}</span>
                            </div>
                            <p class="mt-1 text-xs leading-snug text-slate-500 dark:text-slate-400">{{ t('recent_requests_hint_open_screen', 'Open a request to see the full submission on its own page.') }}</p>

                            @if($myFormRequests->isEmpty())
                                <p class="mt-4 rounded-xl bg-slate-100/80 px-3 py-3 text-center text-xs text-slate-600 dark:bg-slate-800/50 dark:text-slate-400">{{ t('no_requests_yet_portal', 'You have not submitted any requests yet. Use the form below when you are ready.') }}</p>
                            @else
                                <ul class="mt-3 flex max-h-[min(22rem,52vh)] flex-col gap-2 overflow-y-auto overscroll-contain pr-0.5 [scrollbar-width:thin]">
                                    @foreach($myFormRequests as $req)
                                        @php
                                            $summary = $req->summaryPreview();
                                            $reqLabelNum = $myFormRequests->count() - $loop->index;
                                        @endphp
                                        <li class="rounded-xl border border-slate-200/70 bg-slate-50/80 p-3 dark:border-slate-600/50 dark:bg-slate-800/50">
                                            <div class="flex items-start justify-between gap-2">
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-xs font-medium tabular-nums text-slate-900 dark:text-slate-100">
                                                        <time datetime="{{ $req->created_at->toIso8601String() }}">{{ $req->created_at->format('M j, Y') }}</time>
                                                        <span class="text-slate-400 dark:text-slate-500"> · </span>
                                                        <span class="text-slate-500 dark:text-slate-400">{{ $req->created_at->format('H:i') }}</span>
                                                    </p>
                                                    <p class="mt-0.5 text-[11px] text-slate-400 dark:text-slate-500">{{ t('request_number_short', 'Request') }} #{{ $reqLabelNum }}</p>
                                                </div>
                                                <div class="shrink-0">
                                                    @if($req->status === \App\Models\MonasteryFormRequest::STATUS_PENDING)
                                                        <span class="inline-flex rounded-full bg-amber-500/15 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-800 ring-1 ring-amber-500/20 dark:text-amber-200">{{ t('status_pending', 'Pending') }}</span>
                                                    @elseif($req->status === \App\Models\MonasteryFormRequest::STATUS_APPROVED)
                                                        <span class="inline-flex rounded-full bg-emerald-500/15 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-emerald-800 ring-1 ring-emerald-500/20 dark:text-emerald-200">{{ t('status_approved', 'Approved') }}</span>
                                                    @else
                                                        <span class="inline-flex rounded-full bg-rose-500/15 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-rose-800 ring-1 ring-rose-500/20 dark:text-rose-200">{{ t('status_rejected', 'Rejected') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            @if($summary !== '—')
                                                <p class="mt-2 line-clamp-2 text-xs leading-snug text-slate-600 dark:text-slate-300">{{ $summary }}</p>
                                            @endif

                                            <a href="{{ route('monastery.requests.show', $req) }}" class="mt-2.5 flex w-full items-center justify-center gap-1.5 rounded-lg border border-slate-200/90 bg-white px-3 py-2 text-xs font-semibold text-slate-800 transition hover:border-amber-300/70 hover:bg-amber-50/90 hover:text-amber-900 dark:border-slate-600 dark:bg-slate-900/40 dark:text-slate-100 dark:hover:border-amber-600/50 dark:hover:bg-slate-800">
                                                {{ t('view_full_request', 'View full request') }}
                                                @include('partials.icon', ['name' => 'chevron-right', 'class' => 'h-3.5 w-3.5 opacity-70'])
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </aside>
                </div>
            </div>
        </section>
    @endif
@elseif($tab === 'exam')
    @if($examTypesCanonical->isEmpty())
        <section class="mb-8 rounded-xl border border-amber-200/80 bg-amber-50/90 px-5 py-4 text-sm text-amber-900 dark:border-amber-800/50 dark:bg-amber-950/30 dark:text-amber-200">
            {{ t('monastery_exam_types_missing', 'Exam programmes are not configured yet. Please contact the administrator.') }}
        </section>
    @else
        <div class="mb-5 flex gap-2 overflow-x-auto pb-1 [scrollbar-width:thin]">
            @foreach($examTypesCanonical as $et)
                <a href="{{ route('monastery.dashboard', ['tab' => 'exam', 'exam_type_id' => $et->id]) }}" class="shrink-0 rounded-xl border px-3.5 py-2 text-xs font-semibold whitespace-nowrap transition sm:text-sm {{ (int) $examTypeId === (int) $et->id ? 'border-amber-500 bg-amber-500 text-white shadow-md shadow-amber-900/15 dark:border-amber-400 dark:bg-amber-600' : 'border-slate-200 bg-white text-slate-700 hover:border-amber-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:border-amber-500/50' }}">
                    {{ $et->name }}
                </a>
            @endforeach
        </div>

        <section class="mb-8 overflow-hidden rounded-3xl border border-slate-200/70 bg-gradient-to-b from-white to-slate-50/90 shadow-md dark:border-slate-700/55 dark:from-slate-900/90 dark:to-slate-950/90">
            <div class="relative px-5 py-5 sm:px-8 sm:py-6">
                <div class="absolute left-0 top-0 h-1 w-full bg-gradient-to-r from-teal-400/80 via-teal-500/70 to-emerald-600/60 dark:from-teal-500/50 dark:via-teal-600/40 dark:to-emerald-700/30" aria-hidden="true"></div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-teal-700/90 dark:text-teal-400/90">{{ t('monastery_exam_form_upload', 'Exam form upload') }}</p>
                    <h2 class="mt-1.5 text-xl font-semibold tracking-tight text-slate-900 dark:text-slate-50 sm:text-2xl">{{ $examTypesCanonical->firstWhere('id', $examTypeId)?->name }}</h2>
                    <p class="mt-2 max-w-xl text-sm leading-relaxed text-slate-600 dark:text-slate-400">{{ t('monastery_exam_form_intro', 'Submit documents for this programme. Administrators review each submission like other portal requests.') }}</p>
                </div>
            </div>
            <div class="border-t border-slate-200/60 bg-slate-50/50 px-5 py-6 sm:px-8 sm:py-8 dark:border-slate-800/80 dark:bg-slate-950/35">
                <div class="mx-auto grid max-w-6xl grid-cols-1 gap-8 lg:grid-cols-12 lg:gap-10">
                    <div class="min-w-0 lg:col-span-7">
                        <form action="{{ route('monastery.exam-forms.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                            @csrf
                            <input type="hidden" name="exam_type_id" value="{{ $examTypeId }}">
                            @if($monasteryExamCustomFields->isNotEmpty())
                                <div class="monastery-request-form-fields space-y-4">
                                    @include('website.partials.custom-fields', [
                                        'customFields' => $monasteryExamCustomFields,
                                        'idPrefix' => 'monastery_exam_form',
                                        'oldPrefix' => null,
                                        'variant' => 'monastery',
                                    ])
                                </div>
                                <div class="mt-8 rounded-2xl border border-slate-200/90 bg-white p-4 shadow-sm dark:border-slate-600 dark:bg-slate-900">
                                    <button type="submit" class="flex w-full items-center justify-center rounded-xl border border-amber-600/30 bg-amber-600 px-5 py-3.5 text-sm font-bold text-white shadow-md shadow-amber-900/20 ring-1 ring-amber-400/40 transition hover:bg-amber-500 hover:shadow-lg active:scale-[0.99] dark:border-amber-500/40 dark:bg-amber-600 dark:shadow-amber-950/30 dark:ring-amber-300/25 dark:hover:bg-amber-500">
                                        {{ t('submit_exam_form', 'Submit exam form') }}
                                    </button>
                                </div>
                            @else
                                <p class="rounded-2xl border border-amber-200/80 bg-amber-50/90 px-5 py-4 text-sm text-amber-900 dark:border-amber-800/40 dark:bg-amber-950/30 dark:text-amber-200">{{ t('no_monastery_exam_fields_configured', 'No monastery exam form fields are configured yet. Please contact the administrator.') }}</p>
                            @endif
                        </form>
                    </div>
                    <aside class="min-w-0 w-full max-w-xl lg:max-w-none lg:col-span-5 lg:sticky lg:top-24 lg:self-start">
                        <div class="rounded-2xl border border-slate-200/80 bg-white/95 p-4 shadow-sm dark:border-slate-700/60 dark:bg-slate-900/80 sm:p-4">
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ t('your_recent_exam_submissions', 'Recent submissions for this programme') }}</h3>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ t('monastery_exam_recent_hint', 'Open a row to see the full submission.') }}</p>
                            @if($myExamFormRequests->isEmpty())
                                <p class="mt-4 rounded-xl bg-slate-100/80 px-3 py-3 text-center text-xs text-slate-600 dark:bg-slate-800/50 dark:text-slate-400">{{ t('no_exam_submissions_yet', 'No submissions for this programme yet.') }}</p>
                            @else
                                <ul class="mt-3 flex max-h-[min(22rem,52vh)] flex-col gap-2 overflow-y-auto overscroll-contain [scrollbar-width:thin]">
                                    @foreach($myExamFormRequests as $req)
                                        <li class="rounded-xl border border-slate-200/70 bg-slate-50/80 p-3 dark:border-slate-600/50 dark:bg-slate-800/50">
                                            <div class="flex items-start justify-between gap-2">
                                                <p class="text-xs font-medium text-slate-900 dark:text-slate-100">{{ $req->created_at->format('M j, Y H:i') }}</p>
                                                @if($req->status === \App\Models\MonasteryFormRequest::STATUS_PENDING)
                                                    <span class="inline-flex rounded-full bg-amber-500/15 px-2 py-0.5 text-[10px] font-semibold uppercase text-amber-800 dark:text-amber-200">{{ t('status_pending', 'Pending') }}</span>
                                                @elseif($req->status === \App\Models\MonasteryFormRequest::STATUS_APPROVED)
                                                    <span class="inline-flex rounded-full bg-emerald-500/15 px-2 py-0.5 text-[10px] font-semibold uppercase text-emerald-800 dark:text-emerald-200">{{ t('status_approved', 'Approved') }}</span>
                                                @else
                                                    <span class="inline-flex rounded-full bg-rose-500/15 px-2 py-0.5 text-[10px] font-semibold uppercase text-rose-800 dark:text-rose-200">{{ t('status_rejected', 'Rejected') }}</span>
                                                @endif
                                            </div>
                                            <a href="{{ route('monastery.requests.show', $req) }}" class="mt-2 flex w-full items-center justify-center gap-1 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-800 hover:border-teal-300 dark:border-slate-600 dark:bg-slate-900/40 dark:text-slate-100">
                                                {{ t('view_full_request', 'View full request') }}
                                                @include('partials.icon', ['name' => 'chevron-right', 'class' => 'h-3.5 w-3.5 opacity-70'])
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </aside>
                </div>
            </div>
        </section>
    @endif
@elseif($tab === 'chat')
    {{-- Extra in-flow gap so the chat composer (end of card) sits above the fixed pill nav on short viewports. --}}
    <div class="mx-auto max-w-3xl px-0.5 pb-3 sm:pb-4 mb-6 sm:mb-8">
    <section class="mb-5">
        <h2 class="text-lg font-semibold tracking-tight text-slate-900 dark:text-slate-100">{{ t('monastery_chat_heading', 'Chat with administrators') }}</h2>
        <p class="mt-1.5 text-sm leading-relaxed text-slate-600 dark:text-slate-400">{{ t('monastery_chat_intro', 'Messages are shared with your monastery record. Administrators can reply from the admin panel.') }}</p>
    </section>
    @include('partials.chat-thread', [
        'messages' => $recentChatMessages,
        'fetchUrl' => route('monastery.chat.messages'),
        'sendUrl' => route('monastery.chat.messages.store'),
        'isAdminViewer' => false,
        'compactViewport' => true,
    ])
    </div>
@else
    @if($screen === 'results-home')
        @php
            $passCount = $passSanghas->count();
            $failCount = $failSanghas->count();
            $resultsChevron = 'h-5 w-5 text-slate-400 opacity-60 transition group-hover:translate-x-0.5 group-hover:opacity-100';
        @endphp
        <section class="mb-8 space-y-4">
            <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-md ring-1 ring-slate-200/60 sm:p-6 dark:border-slate-700/80 dark:bg-slate-900/50 dark:shadow-black/20 dark:ring-white/5">
                <h2 class="text-lg font-semibold tracking-tight text-slate-900 sm:text-xl dark:text-slate-100">{{ t('monastery_results_title', 'Examination results') }}</h2>
                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-600 dark:text-slate-400">{{ t('monastery_results_overview_hint', 'Summary of published candidates for your monastery. Tap a card below for the full list.') }}</p>
                <div class="mt-5 flex flex-wrap gap-3">
                    <div class="inline-flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 dark:border-emerald-500/25 dark:bg-emerald-500/10">
                        <span class="text-[11px] font-bold uppercase tracking-widest text-emerald-800 dark:text-emerald-400/90">{{ t('pass_list', 'Pass list') }}</span>
                        <span class="text-xl font-bold tabular-nums text-emerald-700 dark:text-emerald-300">{{ $passCount }}</span>
                    </div>
                    <div class="inline-flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 dark:border-rose-500/25 dark:bg-rose-500/10">
                        <span class="text-[11px] font-bold uppercase tracking-widest text-rose-800 dark:text-rose-400/90">{{ t('fail_list', 'Fail list') }}</span>
                        <span class="text-xl font-bold tabular-nums text-rose-700 dark:text-rose-300">{{ $failCount }}</span>
                    </div>
                </div>
                @if($resultsPublishedAt)
                    <p class="mt-4 text-xs text-slate-500 dark:text-slate-500">{{ t('published_at', 'Published at') }}: {{ \Illuminate\Support\Carbon::parse($resultsPublishedAt)->format('M d, Y H:i') }}</p>
                @endif
            </div>

            <div class="grid grid-cols-1 gap-4 min-[480px]:grid-cols-2">
                <a href="{{ route('monastery.dashboard', ['tab' => 'results', 'screen' => 'pass']) }}" class="group relative flex min-h-[168px] flex-col justify-between overflow-hidden rounded-2xl border-2 border-emerald-400/50 bg-gradient-to-br from-white via-emerald-50/30 to-teal-50/40 p-5 shadow-md ring-1 ring-emerald-200/50 transition duration-200 hover:border-emerald-500 hover:shadow-lg dark:border-emerald-500/35 dark:from-slate-950 dark:via-slate-900 dark:to-emerald-950/40 dark:shadow-emerald-950/20 dark:ring-emerald-500/10 dark:hover:border-emerald-400/50 dark:hover:shadow-emerald-900/30">
                    <div class="flex items-start justify-between gap-3">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 ring-2 ring-emerald-200/80 dark:bg-emerald-500/15 dark:text-emerald-400 dark:ring-emerald-500/25">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </span>
                        <div class="min-w-0 flex-1 text-right">
                            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-emerald-800 dark:text-emerald-400">{{ t('pass_list', 'Pass list') }}</p>
                            <p class="mt-1.5 text-xs leading-snug text-emerald-900/70 dark:text-emerald-100/70">{{ t('monastery_results_pass_sub', 'Candidates who passed in the published list.') }}</p>
                        </div>
                    </div>
                    <div class="mt-6 flex items-end justify-between gap-2">
                        <span class="text-4xl font-bold tabular-nums tracking-tight text-emerald-700 sm:text-5xl dark:text-emerald-400">{{ $passCount }}</span>
                        <span class="shrink-0" aria-hidden="true">@include('partials.icon', ['name' => 'chevron-right', 'class' => $resultsChevron])</span>
                    </div>
                </a>
                <a href="{{ route('monastery.dashboard', ['tab' => 'results', 'screen' => 'fail']) }}" class="group relative flex min-h-[168px] flex-col justify-between overflow-hidden rounded-2xl border-2 border-rose-400/50 bg-gradient-to-br from-white via-rose-50/30 to-red-50/30 p-5 shadow-md ring-1 ring-rose-200/50 transition duration-200 hover:border-rose-500 hover:shadow-lg dark:border-rose-500/35 dark:from-slate-950 dark:via-slate-900 dark:to-rose-950/40 dark:shadow-rose-950/20 dark:ring-rose-500/10 dark:hover:border-rose-400/50 dark:hover:shadow-rose-900/30">
                    <div class="flex items-start justify-between gap-3">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-700 ring-2 ring-rose-200/80 dark:bg-rose-500/15 dark:text-rose-400 dark:ring-rose-500/25">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </span>
                        <div class="min-w-0 flex-1 text-right">
                            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-rose-800 dark:text-rose-400">{{ t('fail_list', 'Fail list') }}</p>
                            <p class="mt-1.5 text-xs leading-snug text-rose-900/70 dark:text-rose-100/70">{{ t('monastery_results_fail_sub', 'Candidates recorded as fail in the published list.') }}</p>
                        </div>
                    </div>
                    <div class="mt-6 flex items-end justify-between gap-2">
                        <span class="text-4xl font-bold tabular-nums tracking-tight text-rose-700 sm:text-5xl dark:text-rose-400">{{ $failCount }}</span>
                        <span class="shrink-0" aria-hidden="true">@include('partials.icon', ['name' => 'chevron-right', 'class' => $resultsChevron])</span>
                    </div>
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
        <div class="overflow-x-auto bg-white dark:bg-slate-800/50">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-900/40">
                    <tr class="text-left text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <th class="px-4 py-3">No.</th>
                        <th class="px-4 py-3">{{ t('sanghas', 'Sangha') }}</th>
                        <th class="px-4 py-3">{{ t('exams', 'Exam') }}</th>
                        <th class="px-4 py-3">{{ t('score_table_father_nrc', 'Father / NRC') }}</th>
                        <th class="px-4 py-3">{{ t('score_candidate_ref_label', 'Student Id') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($passSanghas as $sangha)
                        <tr>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100">{{ $sangha->name }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $sangha->exam_name ?? $sangha->exam?->name ?? t('no_exam_selected', 'No exam selected') }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                {{ $sangha->latest_score_father_name ?: '—' }}
                                <span class="mx-1 text-slate-400">/</span>
                                {{ $sangha->latest_score_nrc_number ?: '—' }}
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-600 dark:text-slate-300">{{ $sangha->candidate_ref ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-sm text-slate-500 dark:text-slate-400">{{ t('monastery_results_list_empty', 'No entries in this list.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    @endif

    @if($screen === 'fail')
    <section class="rounded-xl border border-red-200/70 dark:border-red-800/50 overflow-hidden mb-8">
        <div class="px-4 py-3 border-b border-red-100 dark:border-red-900/40 bg-red-50/60 dark:bg-red-900/10">
            <h3 class="text-sm font-semibold text-red-800 dark:text-red-300 uppercase tracking-wider">{{ t('fail_sangha_list', 'Fail Sangha List') }}</h3>
        </div>
        <div class="overflow-x-auto bg-white dark:bg-slate-800/50">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-900/40">
                    <tr class="text-left text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <th class="px-4 py-3">No.</th>
                        <th class="px-4 py-3">{{ t('sanghas', 'Sangha') }}</th>
                        <th class="px-4 py-3">{{ t('exams', 'Exam') }}</th>
                        <th class="px-4 py-3">{{ t('score_table_father_nrc', 'Father / NRC') }}</th>
                        <th class="px-4 py-3">{{ t('score_candidate_ref_label', 'Student Id') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($failSanghas as $sangha)
                        <tr>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100">{{ $sangha->name }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $sangha->exam_name ?? $sangha->exam?->name ?? t('no_exam_selected', 'No exam selected') }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                {{ $sangha->latest_score_father_name ?: '—' }}
                                <span class="mx-1 text-slate-400">/</span>
                                {{ $sangha->latest_score_nrc_number ?: '—' }}
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-600 dark:text-slate-300">{{ $sangha->candidate_ref ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-sm text-slate-500 dark:text-slate-400">{{ t('monastery_results_list_empty', 'No entries in this list.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    @endif
@endif

<nav class="fixed bottom-5 left-1/2 z-40 w-[calc(100%-2rem)] max-w-xl -translate-x-1/2 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white/95 dark:bg-slate-900/95 px-1.5 py-2 shadow-xl backdrop-blur sm:px-2">
    <div class="flex flex-row flex-nowrap items-stretch justify-between gap-1 sm:gap-1.5 w-full">
        <a href="{{ route('monastery.dashboard', ['tab' => 'main', 'screen' => 'main-home']) }}" class="flex flex-1 min-w-0 basis-0 items-center justify-center rounded-xl px-1 py-2.5 text-center text-[11px] font-semibold leading-tight sm:px-2 sm:text-sm {{ $tab === 'main' ? 'bg-amber-500 text-white' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800' }}">{{ t('main', 'Main') }}</a>
        <a href="{{ route('monastery.dashboard', ['tab' => 'exam', 'exam_type_id' => $examTypeId ?: null]) }}" class="flex flex-1 min-w-0 basis-0 items-center justify-center rounded-xl px-1 py-2.5 text-center text-[11px] font-semibold leading-tight sm:px-2 sm:text-sm {{ $tab === 'exam' ? 'bg-amber-500 text-white' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800' }}">{{ t('monastery_nav_exam_form', 'Exam Form') }}</a>
        <a href="{{ route('monastery.dashboard', ['tab' => 'results', 'screen' => 'results-home']) }}" class="flex flex-1 min-w-0 basis-0 items-center justify-center rounded-xl px-1 py-2.5 text-center text-[11px] font-semibold leading-tight sm:px-2 sm:text-sm {{ $tab === 'results' ? 'bg-amber-500 text-white' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800' }}">{{ t('results', 'Results') }}</a>
        <a href="{{ route('monastery.dashboard', ['tab' => 'chat']) }}" class="flex flex-1 min-w-0 basis-0 items-center justify-center rounded-xl px-1 py-2.5 text-center text-[11px] font-semibold leading-tight sm:px-2 sm:text-sm {{ $tab === 'chat' ? 'bg-amber-500 text-white' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800' }}">{{ t('chat', 'Chat') }}</a>
    </div>
</nav>
@endsection
