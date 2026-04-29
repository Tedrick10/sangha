@extends('layouts.monastery')

@section('title', t('monastery_portal'))

@section('content')
@php
    $mpIsMainHome = $tab === 'main' && ($screen ?? '') === 'main-home';
    $portalProgrammeHubScreensLocal = $portalProgrammeHubScreens ?? ['primary', 'intermediate', 'level-1', 'level-2', 'level-3'];
    $mpIsProgrammeHubScreen = $tab === 'main' && in_array($screen ?? '', $portalProgrammeHubScreensLocal, true);
@endphp
@if($tab !== 'main' && ! $mpIsProgrammeHubScreen)
<div class="{{ $mpIsMainHome ? 'mb-5 sm:mb-6' : 'mb-6' }}">
    <h1 @if($mpIsMainHome) id="mp-portal-home-heading" @endif class="font-bold tracking-tight text-slate-900 dark:text-slate-50 {{ $mpIsMainHome ? 'text-xl leading-snug sm:text-2xl' : 'text-2xl' }}">{{ t('monastery_portal') }} — {{ $monastery->name }}</h1>
    <p class="{{ $mpIsMainHome ? 'mt-2 max-w-2xl text-sm leading-relaxed text-slate-600 dark:text-slate-400 sm:text-base' : 'mt-1 text-slate-600 dark:text-slate-400' }}">
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
@endif

@if($tab === 'main')
    @php
        $mpBlankPortalTiles = [
            ['screen' => 'total', 'label' => 'Total', 'icon' => 'chart-bar'],
            ['screen' => 'primary', 'label' => 'Primary', 'icon' => 'book-open'],
            ['screen' => 'intermediate', 'label' => 'Intermediate', 'icon' => 'academic-cap'],
            ['screen' => 'level-1', 'label' => 'Level 1', 'icon' => 'document-text'],
            ['screen' => 'level-2', 'label' => 'Level 2', 'icon' => 'clipboard-list'],
            ['screen' => 'level-3', 'label' => 'Level 3', 'icon' => 'sparkles'],
        ];
        $mpBlankPortalScreenKeys = array_column($mpBlankPortalTiles, 'screen');
        @endphp
    @if($screen === 'main-home')
        <section class="monastery-portal-home-section mb-6 w-full sm:mb-7" role="region" aria-labelledby="mp-portal-home-heading">
            <div class="monastery-portal-home-wrap monastery-portal-home-grid">
                @foreach($mpBlankPortalTiles as $tile)
                    <a
                        href="{{ route('monastery.dashboard', ['tab' => 'main', 'screen' => $tile['screen']]) }}"
                        class="monastery-portal-home-tile"
                        data-mp-tile="{{ $tile['screen'] }}"
                        aria-label="{{ $tile['label'] }}"
                    >
                        <span class="monastery-portal-home-tile__iconWrap" aria-hidden="true">
                            @include('partials.icon', ['name' => $tile['icon'], 'class' => 'monastery-portal-home-tile__svg'])
                        </span>
                        <span class="monastery-portal-home-tile__label">{{ $tile['label'] }}</span>
                    </a>
                @endforeach
                    </div>
        </section>
    @endif

    @if($screen !== 'main-home')
        @if($mpIsProgrammeHubScreen)
            <div class="monastery-portal-back-row mb-5 flex flex-wrap items-center gap-2">
                <a href="{{ route('monastery.dashboard', ['tab' => 'main', 'screen' => 'main-home']) }}" class="monastery-portal-back-row__btn monastery-portal-back-row__btn--neutral">
                    @include('partials.icon', ['name' => 'arrow-left', 'class' => 'h-4 w-4 shrink-0 opacity-75'])
                    {{ t('back_to_main', 'Back to Main') }}
                </a>
                    </div>
        @elseif(!empty($programmeContext))
            @php
                $mpProgrammeBackMeta = collect($mpBlankPortalTiles)->firstWhere('screen', $programmeContext);
                $mpProgrammeBackLabel = is_array($mpProgrammeBackMeta) ? ($mpProgrammeBackMeta['label'] ?? $programmeContext) : $programmeContext;
            @endphp
            <div class="monastery-portal-back-row mb-5 flex flex-wrap items-center gap-2">
                <a href="{{ route('monastery.dashboard', ['tab' => 'main', 'screen' => $programmeContext]) }}" class="monastery-portal-back-row__btn monastery-portal-back-row__btn--accent">
                    @include('partials.icon', ['name' => 'chevron-left', 'class' => 'h-4 w-4 shrink-0 opacity-80'])
                    {{ t('mp_back_to_programme_prefix', 'Back to') }} {{ $mpProgrammeBackLabel }}
                </a>
                    </div>
        @elseif($screen === 'total')
            {{-- Total is opened from main-home without programme; show same back control as programme hubs. --}}
            <div class="monastery-portal-back-row mb-5 flex flex-wrap items-center gap-2">
                <a href="{{ route('monastery.dashboard', ['tab' => 'main', 'screen' => 'main-home']) }}" class="monastery-portal-back-row__btn monastery-portal-back-row__btn--neutral">
                    @include('partials.icon', ['name' => 'arrow-left', 'class' => 'h-4 w-4 shrink-0 opacity-75'])
                    {{ t('back_to_main', 'Back to Main') }}
                </a>
            </div>
        @endif
    @endif

    @if(in_array($screen, ['total', 'eligible', 'pending', 'approved', 'needed-update', 'rejected'], true))
        @php
            $hideStatusAndDateFilters = in_array($screen, ['eligible', 'pending', 'approved', 'needed-update', 'rejected'], true);
            $mpPortalFilterField = 'mp-portal-filter-field w-full min-h-[2.75rem] rounded-xl border border-slate-200/80 bg-white px-3.5 py-2.5 text-sm text-slate-800 antialiased shadow-[inset_0_1px_0_rgba(255,255,255,0.6)] outline-none ring-0 transition placeholder:text-slate-500 hover:border-slate-300/90 focus:border-amber-400 focus:shadow-[inset_0_0_0_1px_rgba(251,191,36,0.25)] focus:ring-2 focus:ring-amber-400/25 dark:border-slate-600/70 dark:bg-slate-900/70 dark:text-slate-100 dark:shadow-[inset_0_1px_0_rgba(255,255,255,0.04)] dark:placeholder:text-slate-400 dark:hover:border-slate-500 dark:focus:border-amber-500/60 dark:focus:shadow-none dark:focus:ring-amber-400/20';
            $mpPortalFilterSelect = $mpPortalFilterField.' cursor-pointer text-slate-800 dark:text-slate-100';
        @endphp
        <form method="GET" class="mp-portal-filter-card mb-7 rounded-2xl border border-slate-200/70 bg-gradient-to-b from-white via-white to-slate-50/80 p-5 shadow-md shadow-slate-900/[0.04] ring-1 ring-slate-900/[0.025] dark:border-slate-700/55 dark:from-slate-900 dark:via-slate-900 dark:to-slate-950 dark:shadow-black/30 dark:ring-white/[0.04] sm:p-6">
            <input type="hidden" name="tab" value="main">
            <input type="hidden" name="screen" value="{{ $screen }}">
            @if(!empty($programmeContext))
                <input type="hidden" name="programme" value="{{ $programmeContext }}">
            @endif
            <p class="mb-4 text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">{{ t('mp_filters_heading', 'Filters') }}</p>
            @if($hideStatusAndDateFilters)
                {{-- Eligible / pending / … : one row on large screens — 4 fields + Filter/Clear in the same grid line --}}
                <div class="grid grid-cols-2 items-end gap-3 sm:grid-cols-4 lg:grid-cols-6">
                    <input type="text" name="filter_roll_number" value="{{ $portalFilters['roll_number'] ?? '' }}" placeholder="{{ t('roll_number', 'Roll Number') }}" class="{{ $mpPortalFilterField }}" autocomplete="off">
                    <input type="text" name="filter_desk_number" value="{{ $portalFilters['desk_number'] ?? '' }}" placeholder="{{ t('desk_number_short', 'Desk No.') }}" class="{{ $mpPortalFilterField }}" autocomplete="off">
                    <input type="text" name="filter_sangha" value="{{ $portalFilters['sangha'] ?? '' }}" placeholder="{{ t('sanghas', 'Sangha') }}" class="{{ $mpPortalFilterField }}" autocomplete="off">
                    <select name="filter_exam_id" data-no-select-search="1" class="{{ $mpPortalFilterSelect }}">
                        <option value="">{{ t('exams', 'Exam') }}</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}" {{ (string) ($portalFilters['exam_id'] ?? '') === (string) $exam->id ? 'selected' : '' }}>{{ $exam->name }}</option>
                        @endforeach
                    </select>
                    <div class="col-span-2 flex justify-end gap-3 sm:col-span-4 lg:col-span-2 lg:col-start-5">
                        <button type="submit" class="inline-flex min-h-[2.75rem] min-w-[9rem] items-center justify-center rounded-xl bg-gradient-to-b from-amber-400 to-amber-600 px-6 text-sm font-semibold text-white shadow-md shadow-amber-900/15 transition hover:from-amber-400 hover:to-amber-500 hover:shadow-lg hover:shadow-amber-900/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-300/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white active:translate-y-px dark:shadow-amber-950/40 dark:focus-visible:ring-offset-slate-900">{{ t('filter', 'Filter') }}</button>
                        <a href="{{ route('monastery.dashboard', array_filter(['tab' => 'main', 'screen' => $screen, 'programme' => $programmeContext])) }}" class="inline-flex min-h-[2.75rem] min-w-[5.5rem] items-center justify-center rounded-xl border border-slate-300/80 bg-white/90 px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800/80 dark:text-slate-200 dark:hover:border-slate-500 dark:hover:!bg-slate-700">{{ t('clear', 'Clear') }}</a>
                    </div>
                </div>
            @else
            <div class="grid gap-3 [grid-template-columns:repeat(auto-fill,minmax(min(100%,13.25rem),1fr))]">
                <input type="text" name="filter_roll_number" value="{{ $portalFilters['roll_number'] ?? '' }}" placeholder="{{ t('roll_number', 'Roll Number') }}" class="{{ $mpPortalFilterField }}" autocomplete="off">
                <input type="text" name="filter_desk_number" value="{{ $portalFilters['desk_number'] ?? '' }}" placeholder="{{ t('desk_number_short', 'Desk No.') }}" class="{{ $mpPortalFilterField }}" autocomplete="off">
                <input type="text" name="filter_sangha" value="{{ $portalFilters['sangha'] ?? '' }}" placeholder="{{ t('sanghas', 'Sangha') }}" class="{{ $mpPortalFilterField }}" autocomplete="off">
                <select name="filter_exam_id" data-no-select-search="1" class="{{ $mpPortalFilterSelect }}">
                    <option value="">{{ t('exams', 'Exam') }}</option>
                    @foreach($exams as $exam)
                        <option value="{{ $exam->id }}" {{ (string) ($portalFilters['exam_id'] ?? '') === (string) $exam->id ? 'selected' : '' }}>{{ $exam->name }}</option>
                    @endforeach
                </select>
                    <input type="date" name="filter_created_date" value="{{ $portalFilters['created_date'] ?? '' }}" title="{{ t('created_at', 'Created') }}" class="cf-native-datetime {{ $mpPortalFilterField }}">
            </div>
            @endif
            @if(!$hideStatusAndDateFilters)
                {{-- Grid: fixed-width status column (select intrinsic min-width otherwise blows up flex). Buttons in second column, end-aligned. --}}
                <div class="mt-4 grid grid-cols-1 gap-3 border-t border-slate-200/60 pt-4 dark:border-slate-600/40 sm:grid-cols-[minmax(0,13rem)_minmax(0,1fr)] sm:items-center sm:gap-4">
                    <div class="min-w-0 w-full max-w-[13rem] sm:w-[13rem]">
                        <select name="filter_status" data-no-select-search="1" class="mp-portal-filter-status {{ $mpPortalFilterSelect }} box-border w-full min-w-0 max-w-full">
                            <option value="">{{ t('status', 'Status') }}</option>
                            <option value="eligible" {{ ($portalFilters['status'] ?? '') === 'eligible' ? 'selected' : '' }}>{{ t('mp_card_eligible', 'Eligible') }}</option>
                            <option value="pending" {{ ($portalFilters['status'] ?? '') === 'pending' ? 'selected' : '' }}>{{ t('status_pending', 'Pending') }}</option>
                            <option value="approved" {{ ($portalFilters['status'] ?? '') === 'approved' ? 'selected' : '' }}>{{ t('status_approved', 'Approved') }}</option>
                            <option value="needed_update" {{ ($portalFilters['status'] ?? '') === 'needed_update' ? 'selected' : '' }}>{{ t('status_needed_update', 'Need to Update') }}</option>
                            <option value="rejected" {{ ($portalFilters['status'] ?? '') === 'rejected' ? 'selected' : '' }}>{{ t('status_rejected', 'Rejected') }}</option>
                            @if($screen === 'total')
                                <option value="pass_published" {{ ($portalFilters['status'] ?? '') === 'pass_published' ? 'selected' : '' }}>{{ t('pass_sangha_list', 'Pass Sangha List') }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="flex min-w-0 flex-wrap items-center justify-end gap-3">
                        <button type="submit" class="inline-flex min-h-[2.75rem] min-w-[9rem] items-center justify-center rounded-xl bg-gradient-to-b from-amber-400 to-amber-600 px-6 text-sm font-semibold text-white shadow-md shadow-amber-900/15 transition hover:from-amber-400 hover:to-amber-500 hover:shadow-lg hover:shadow-amber-900/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-300/90 focus-visible:ring-offset-2 focus-visible:ring-offset-white active:translate-y-px dark:shadow-amber-950/40 dark:focus-visible:ring-offset-slate-900">{{ t('filter', 'Filter') }}</button>
                        <a href="{{ route('monastery.dashboard', array_filter(['tab' => 'main', 'screen' => $screen, 'programme' => $programmeContext])) }}" class="inline-flex min-h-[2.75rem] min-w-[5.5rem] items-center justify-center rounded-xl border border-slate-300/80 bg-white/90 px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800/80 dark:text-slate-200 dark:hover:border-slate-500 dark:hover:!bg-slate-700">{{ t('clear', 'Clear') }}</a>
                    </div>
                </div>
            @endif
        </form>
    @endif

    @if($screen === 'total')
        @php
            $mpBlankTileMeta = collect($mpBlankPortalTiles)->firstWhere('screen', $screen);
            $mpBlankAria = is_array($mpBlankTileMeta) ? ($mpBlankTileMeta['label'] ?? $screen) : $screen;
            $totalRows = $totalUnifiedRows ?? collect();
        @endphp
        <section class="mb-8 space-y-6" aria-label="{{ $mpBlankAria }}">
            <article class="mp-eligible-panel overflow-hidden rounded-2xl border border-slate-200/70 bg-gradient-to-b from-white to-slate-50/95 shadow-md ring-1 ring-slate-900/[0.03] dark:border-slate-700/65 dark:from-slate-900 dark:to-slate-950 dark:shadow-black/30 dark:ring-white/[0.05]">
                <div class="relative flex flex-wrap items-center justify-between gap-3 px-4 py-4 sm:px-6 sm:py-5">
                    <div class="absolute left-0 top-0 h-1 w-full bg-gradient-to-r from-sky-400/70 via-indigo-500/55 to-violet-500/50 dark:from-sky-500/45 dark:via-indigo-500/35 dark:to-violet-600/30" aria-hidden="true"></div>
                    <div>
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-slate-800 dark:text-slate-200">{{ t('mp_total_unified_title', 'All applications & pass list') }}</h2>
                        <p class="mt-1 max-w-2xl text-xs leading-relaxed text-slate-600 dark:text-slate-400">{{ t('mp_total_unified_hint', 'Every workflow status for your monastery plus published pass entries (admin Generate) in one table.') }}</p>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">{{ t('total', 'Total') }}: {{ $totalRows->count() }}</span>
                </div>
                <div class="px-4 pb-5 sm:px-6">
                    <div class="mp-eligible-table-wrap w-full min-w-0 rounded-xl border border-slate-200/70 dark:border-slate-700/70">
                        <table class="w-full min-w-0 table-fixed text-xs sm:text-sm">
                            <thead>
                                <tr class="border-b border-slate-200/70 bg-slate-100/65 text-left text-[11px] uppercase tracking-wider dark:border-slate-700/70 dark:bg-slate-800/55">
                                    <th class="px-4 py-3 sm:px-6">{{ t('roll_number', 'Roll Number') }}</th>
                                    <th class="px-4 py-3 sm:px-6">
                                        <span class="block">{{ t('desk_number_short', 'Desk No.') }}</span>
                                        <span class="block whitespace-nowrap text-[10px] font-normal normal-case leading-tight text-slate-500 dark:text-slate-400 sm:text-[11px]">({{ t('exam_roll_number', 'Exam Roll Number') }})</span>
                                    </th>
                                    <th class="px-4 py-3 sm:px-6">{{ t('sanghas', 'Sangha') }}</th>
                                    <th class="px-4 py-3 sm:px-6">{{ t('level', 'Level') }}</th>
                                    <th class="px-4 py-3 sm:px-6">{{ t('exams', 'Exam') }}</th>
                                    <th class="px-4 py-3 sm:px-6">{{ t('created_at', 'Created') }}</th>
                                    <th class="px-4 py-3 sm:px-6">{{ t('status', 'Status') }}</th>
                                    <th class="px-4 py-3 sm:px-6 text-right">{{ t('actions', 'Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($totalRows as $row)
                                    @if(($row->kind ?? '') === 'pass')
                                        @php $p = $row->pass; @endphp
                                        <tr class="border-b border-slate-100/90 last:border-0 dark:border-slate-800/80">
                                            <td class="px-4 py-3 sm:px-6 font-mono text-xs tracking-wide">
                                                <span class="mp-eligible-roll">{{ ($p->roll_display ?? \App\Support\MonasteryPortalResultsSnapshot::formatRollDisplaySix($p->eligible_roll_number ?? null)) ?: '—' }}</span>
                                            </td>
                                            <td class="px-4 py-3 sm:px-6 font-mono text-xs tracking-wide">
                                                <span class="mp-eligible-roll">{{ $p->desk_display ?? '—' }}</span>
                                            </td>
                                            <td class="px-4 py-3 sm:px-6"><p class="font-semibold">{{ $p->name ?? '—' }}</p></td>
                                            <td class="px-4 py-3 sm:px-6 text-slate-700 dark:text-slate-300">{{ $p->level_name ?? $p->programme_level ?? '—' }}</td>
                                            <td class="px-4 py-3 sm:px-6 mp-eligible-muted">{{ $p->exam_name ?? t('no_exam_selected', 'No exam selected') }}</td>
                                            <td class="px-4 py-3 sm:px-6 text-xs mp-eligible-muted">—</td>
                                            <td class="px-4 py-3 sm:px-6">
                                                <span class="inline-flex rounded-full bg-cyan-50 px-2.5 py-1 text-[10px] font-semibold tracking-wide text-cyan-950 ring-1 ring-cyan-800/25 dark:bg-cyan-950 dark:text-cyan-100 dark:ring-cyan-400/55">{{ t('pass_result', 'Pass') }}</span>
                                            </td>
                                            <td class="px-4 py-3 sm:px-6 text-right">
                                                @if(!empty($p->id))
                                                    <button type="button" class="mp-action-btn mp-action-btn--secondary js-open-sangha-details" data-sangha-id="{{ (int) $p->id }}">
                                                        {{ t('view_details', 'View Details') }}
                                                    </button>
                                                @else
                                                    <span class="text-xs text-slate-400">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @else
                                        @php $sangha = $row->sangha ?? null; @endphp
                                        @if($sangha)
                                            @php $ws = (string) ($sangha->workflow_status ?? ''); @endphp
                                            <tr class="border-b border-slate-100/90 last:border-0 dark:border-slate-800/80">
                                                <td class="px-4 py-3 sm:px-6 font-mono text-xs tracking-wide">
                                                    <span class="mp-eligible-roll">{{ $sangha->eligible_roll_number ?: '—' }}</span>
                                                </td>
                                                <td class="px-4 py-3 sm:px-6 font-mono text-xs tracking-wide">
                                                    @if(filled($sangha->desk_number))
                                                        <span class="mp-eligible-roll">{{ ($sangha->exam?->desk_number_prefix ?? '') . str_pad((string) $sangha->desk_number, 6, '0', STR_PAD_LEFT) }}</span>
                                                    @else
                                                        <span class="text-slate-500 dark:text-slate-400">—</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 sm:px-6"><p class="font-semibold">{{ $sangha->name }}</p></td>
                                                <td class="px-4 py-3 sm:px-6 text-slate-500 dark:text-slate-400">—</td>
                                                <td class="px-4 py-3 sm:px-6 mp-eligible-muted">{{ $sangha->exam?->name ?? t('no_exam_selected', 'No exam selected') }}</td>
                                                <td class="px-4 py-3 sm:px-6 text-xs mp-eligible-muted">{{ $sangha->created_at?->format('M d, Y H:i') }}</td>
                                                <td class="px-4 py-3 sm:px-6">
                                                    @if($ws === \App\Models\Sangha::STATUS_APPROVED)
                                                        <span class="inline-flex rounded-full bg-emerald-500/15 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-emerald-800 ring-1 ring-emerald-500/25 dark:text-emerald-200">{{ t('status_approved', 'Approved') }}</span>
                                                    @elseif($ws === \App\Models\Sangha::STATUS_PENDING)
                                                        <span class="inline-flex rounded-full bg-amber-500/15 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-amber-800 ring-1 ring-amber-500/25 dark:text-amber-200">{{ t('status_pending', 'Pending') }}</span>
                                                    @elseif($ws === \App\Models\Sangha::STATUS_NEEDED_UPDATE)
                                                        <span class="inline-flex rounded-full bg-violet-500/15 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-violet-800 ring-1 ring-violet-500/25 dark:text-violet-200">{{ t('status_needed_update', 'Needed Update') }}</span>
                                                    @elseif($ws === \App\Models\Sangha::STATUS_REJECTED)
                                                        <span class="inline-flex rounded-full bg-rose-500/15 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-rose-800 ring-1 ring-rose-500/25 dark:text-rose-200">{{ t('status_rejected', 'Rejected') }}</span>
                                                    @else
                                                        <span class="inline-flex rounded-full bg-sky-500/15 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-sky-800 ring-1 ring-sky-500/25 dark:text-sky-200">{{ t('mp_card_eligible', 'Eligible') }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 sm:px-6 text-right">
                                                    <button type="button" class="mp-action-btn mp-action-btn--secondary js-open-sangha-details" data-sangha-id="{{ $sangha->id }}">
                                                        {{ t('view_details', 'View Details') }}
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-5 text-sm text-slate-500 dark:text-slate-400 sm:px-6">{{ t('no_data', 'No data') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </article>
        </section>
    @endif

    @if($mpIsProgrammeHubScreen)
        @php
            $mpHubProgramme = $screen;
            $mpProgrammeTileMeta = collect($mpBlankPortalTiles)->firstWhere('screen', $mpHubProgramme);
            $mpProgrammeTitle = is_array($mpProgrammeTileMeta) ? ($mpProgrammeTileMeta['label'] ?? $mpHubProgramme) : $mpHubProgramme;
            $mpQ = ['tab' => 'main', 'programme' => $mpHubProgramme];
            $mpSanghaFlowCards = [
                ['key' => 'create', 'icon' => 'plus', 'label' => t('mp_card_create_new_sangha', 'Create new Sangha'), 'href' => route('monastery.dashboard', array_merge($mpQ, ['screen' => 'register']))],
                ['key' => 'eligible', 'icon' => 'shield-check', 'label' => t('mp_card_eligible', 'Eligible'), 'href' => route('monastery.dashboard', array_merge($mpQ, ['screen' => 'eligible']))],
                ['key' => 'pending', 'icon' => 'clipboard-list', 'label' => t('mp_card_pending', 'Pending'), 'href' => route('monastery.dashboard', array_merge($mpQ, ['screen' => 'pending']))],
                ['key' => 'approved', 'icon' => 'check', 'label' => t('mp_card_approved', 'Approved'), 'href' => route('monastery.dashboard', array_merge($mpQ, ['screen' => 'approved']))],
                ['key' => 'needed-update', 'icon' => 'pencil', 'label' => t('mp_card_needed_update', 'Needed Update'), 'href' => route('monastery.dashboard', array_merge($mpQ, ['screen' => 'needed-update']))],
                ['key' => 'rejected', 'icon' => 'x', 'label' => t('mp_card_rejected', 'Rejected'), 'href' => route('monastery.dashboard', array_merge($mpQ, ['screen' => 'rejected']))],
                ['key' => 'transfer', 'icon' => 'envelope', 'label' => t('mp_card_transfer', 'Transfer'), 'href' => route('monastery.dashboard', array_merge($mpQ, ['screen' => 'request']))],
            ];
        @endphp
        <section class="monastery-portal-programme-hub mb-8 w-full" role="region" aria-labelledby="mp-programme-hub-heading">
            <header class="monastery-portal-programme-hub__header">
                <h2 id="mp-programme-hub-heading" class="monastery-portal-programme-hub__title">{{ $mpProgrammeTitle }}</h2>
                <p class="monastery-portal-programme-hub__subtitle">{{ t('mp_programme_hub_hint', 'Choose an action for this programme.') }}</p>
            </header>
            <div class="monastery-portal-programme-hub-wrap">
                <div class="monastery-portal-programme-hub-grid">
                    @foreach($mpSanghaFlowCards as $card)
                        <a
                            href="{{ $card['href'] }}"
                            class="monastery-portal-hub-card"
                            data-mp-hub-card="{{ $card['key'] }}"
                            aria-label="{{ $card['label'] }}"
                        >
                            <span class="monastery-portal-hub-card__iconWrap" aria-hidden="true">
                                @include('partials.icon', ['name' => $card['icon'], 'class' => 'monastery-portal-hub-card__svg'])
                            </span>
                            <span class="monastery-portal-hub-card__label">{{ $card['label'] }}</span>
                        </a>
                    @endforeach
        </div>
            </div>
        </section>
    @endif

    @if($screen === 'eligible')
        <section class="mp-eligible-panel mb-8 overflow-hidden rounded-2xl border border-slate-200/70 bg-gradient-to-b from-white to-slate-50/95 shadow-md ring-1 ring-slate-900/[0.03] dark:border-slate-700/65 dark:from-slate-900 dark:to-slate-950 dark:shadow-black/30 dark:ring-white/[0.05]">
            <div class="relative px-4 py-4 sm:px-6 sm:py-5">
                <div class="mp-eligible-accent-bar absolute left-0 top-0 h-1 w-full bg-gradient-to-r from-sky-400/80 via-cyan-500/70 to-teal-500/60 dark:from-sky-500/60 dark:via-cyan-500/50 dark:to-teal-600/40" aria-hidden="true"></div>
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 id="mp-eligible-heading" class="text-sm font-semibold uppercase tracking-wider text-sky-800 dark:text-sky-300">{{ t('mp_eligible_title', 'Eligible Sangha List') }}</h2>
                        <p class="mt-1 text-xs leading-relaxed text-slate-600 dark:text-slate-300">{{ t('mp_eligible_hint', 'Newly created Sanghas appear here first. Submit selected rows to move them to Pending for admin review.') }}</p>
        </div>
                    <span class="mp-eligible-count-badge inline-flex items-center gap-1 rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs">
                        {{ t('total', 'Total') }}: {{ $eligibleSanghas->count() }}
                    </span>
        </div>
    </div>
            <form method="POST" action="{{ route('monastery.sanghas.submit-eligible') }}">
                @csrf
                @if(!empty($programmeContext))
                    <input type="hidden" name="programme" value="{{ $programmeContext }}">
                @endif
                <div class="mp-eligible-toolbar border-y border-slate-200/70 bg-white/90 px-4 py-3 dark:border-slate-700/80 dark:bg-slate-950/90 sm:px-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <label class="inline-flex items-center gap-2 text-xs text-slate-600 dark:text-slate-300">
                            <input type="checkbox" id="mp-select-all-eligible" class="mp-eligible-checkbox h-4 w-4 rounded border-slate-300 text-amber-500 focus:ring-amber-500 dark:border-slate-600">
                            <span class="font-medium">{{ t('select_all', 'Select all') }}</span>
                        </label>
                        <button type="submit" class="mp-eligible-submit inline-flex items-center justify-center rounded-lg bg-amber-500 px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-amber-400 disabled:cursor-not-allowed disabled:opacity-60" {{ $eligibleSanghas->isEmpty() ? 'disabled' : '' }}>
                            {{ t('submit_to_admin', 'Submit to Admin') }}
                        </button>
                    </div>
                </div>
                <div class="mp-eligible-table-wrap w-full min-w-0">
                    <table class="w-full min-w-0 table-fixed text-xs sm:text-sm">
                        <thead>
                            <tr class="text-left text-[11px] uppercase tracking-wider">
                                <th class="px-4 py-3 sm:px-6 w-12">✓</th>
                                <th class="px-4 py-3 sm:px-6">{{ t('roll_number', 'Roll Number') }}</th>
                                <th class="px-4 py-3 sm:px-6">
                                    <span class="block">{{ t('desk_number_short', 'Desk No.') }}</span>
                                    <span class="block whitespace-nowrap text-[10px] font-normal normal-case leading-tight text-slate-500 dark:text-slate-400 sm:text-[11px]">({{ t('exam_roll_number', 'Exam Roll Number') }})</span>
                                </th>
                                <th class="px-4 py-3 sm:px-6">{{ t('sanghas', 'Sangha') }}</th>
                                <th class="px-4 py-3 sm:px-6">{{ t('exams', 'Exam') }}</th>
                                <th class="px-4 py-3 sm:px-6">{{ t('created_at', 'Created') }}</th>
                                <th class="px-4 py-3 sm:px-6">{{ t('status', 'Status') }}</th>
                                <th class="px-4 py-3 sm:px-6 text-right">{{ t('actions', 'Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($eligibleSanghas as $sangha)
                                <tr class="border-t border-slate-200/70 dark:border-slate-700/70">
                                    <td class="px-4 py-3 sm:px-6 align-middle">
                                        <input type="checkbox" name="sangha_ids[]" value="{{ $sangha->id }}" class="mp-eligible-checkbox h-4 w-4 rounded border-slate-300 text-amber-500 focus:ring-amber-500 dark:border-slate-600">
                                    </td>
                                    <td class="px-4 py-3 sm:px-6 align-middle font-mono text-xs tracking-wide">
                                        <span class="mp-eligible-roll">{{ $sangha->eligible_roll_number ?: '—' }}</span>
                                    </td>
                                    <td class="px-4 py-3 sm:px-6 align-middle font-mono text-xs tracking-wide">
                                        @if(filled($sangha->desk_number))
                                            <span class="mp-eligible-roll">{{ ($sangha->exam?->desk_number_prefix ?? '') . str_pad((string) $sangha->desk_number, 6, '0', STR_PAD_LEFT) }}</span>
                                        @else
                                            <span class="text-slate-500 dark:text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 sm:px-6 align-middle">
                                        <p class="font-semibold">{{ $sangha->name }}</p>
                                    </td>
                                    <td class="px-4 py-3 sm:px-6 align-middle mp-eligible-muted max-w-[15rem] truncate">{{ $sangha->exam?->name ?? t('no_exam_selected', 'No exam selected') }}</td>
                                    <td class="px-4 py-3 sm:px-6 align-middle text-[10px] mp-eligible-muted whitespace-normal leading-snug sm:text-xs sm:whitespace-nowrap">{{ $sangha->created_at?->format('M d, Y H:i') }}</td>
                                    <td class="px-4 py-3 sm:px-6 align-middle">
                                        <span class="inline-flex rounded-full bg-sky-500/15 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-sky-800 ring-1 ring-sky-500/25 dark:text-sky-200">{{ t('mp_card_eligible', 'Eligible') }}</span>
                                    </td>
                                    <td class="px-4 py-3 sm:px-6 align-middle text-right">
                                        <button
                                            type="button"
                                            class="js-open-sangha-details inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-sky-300 hover:bg-sky-50 hover:text-sky-900 dark:border-slate-500 dark:bg-slate-800 dark:text-slate-100 dark:hover:border-sky-500 dark:hover:bg-slate-700 dark:hover:text-white"
                                            data-sangha-id="{{ $sangha->id }}"
                                        >
                                            {{ t('view_details', 'View Details') }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-6 text-sm text-slate-500 dark:text-slate-400 sm:px-6">{{ t('mp_eligible_empty', 'No eligible Sangha yet.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>

                <script type="application/json" id="mp-sangha-details-json">@json($portalSanghaDetailsById)</script>

                <div id="eligible-details-modal" class="fixed inset-0 z-50 hidden overflow-y-auto overscroll-y-contain" aria-hidden="true">
                    <div class="js-eligible-modal-backdrop absolute inset-0 bg-slate-950/60 backdrop-blur-[2px]" aria-hidden="true"></div>
                    {{-- min-h-full + my-auto: dialog scrolls inside viewport when content is tall; inner panel gets a real height for flex-1 scroll --}}
                    <div class="relative z-10 flex min-h-full items-center justify-center p-4 sm:p-6">
                        <div
                            class="mp-eligible-details-panel my-6 flex w-full max-w-2xl flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900 sm:my-10"
                            style="height: min(88vh, 44rem); max-height: min(88vh, 44rem);"
                            role="dialog"
                            aria-modal="true"
                            aria-labelledby="eligible-details-name"
                        >
                            <div class="shrink-0 border-b border-slate-200/80 px-5 py-4 dark:border-slate-700/80 sm:px-6">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 pr-2">
                                        <p class="text-xs font-semibold uppercase tracking-wider text-sky-700 dark:text-sky-300">{{ t('sangha_information', 'Sangha Information') }}</p>
                                        <h3 id="eligible-details-name" class="mt-1 truncate text-lg font-semibold text-slate-900 dark:text-slate-100">—</h3>
                                    </div>
                                    <button type="button" id="eligible-details-close" class="shrink-0 rounded-lg border border-slate-300 px-2.5 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800">✕</button>
                                </div>
                            </div>
                            <div id="eligible-details-scroll" class="min-h-0 flex-1 overflow-y-auto overscroll-contain px-5 py-4 sm:px-6 [scrollbar-width:thin]">
                                <div id="eligible-details-sections" class="space-y-6"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                (function() {
                    var master = document.getElementById('mp-select-all-eligible');
                    var items = Array.prototype.slice.call(document.querySelectorAll('.mp-eligible-checkbox'));
                    if (master) {
                        master.addEventListener('change', function() {
                            items.forEach(function(input) { input.checked = master.checked; });
                        });
                        items.forEach(function(input) {
                            input.addEventListener('change', function() {
                                var checkedCount = items.filter(function(el) { return el.checked; }).length;
                                master.indeterminate = checkedCount > 0 && checkedCount < items.length;
                                if (!master.indeterminate) {
                                    master.checked = checkedCount === items.length && items.length > 0;
                                }
                            });
                        });
                    }

                    var modal = document.getElementById('eligible-details-modal');
                    var closeBtn = document.getElementById('eligible-details-close');
                    if (!modal || !closeBtn) return;
                    var nameEl = document.getElementById('eligible-details-name');
                    var sectionsEl = document.getElementById('eligible-details-sections');
                    var jsonEl = document.getElementById('mp-sangha-details-json');
                    var detailsById = {};
                    if (jsonEl && jsonEl.textContent) {
                        try {
                            detailsById = JSON.parse(jsonEl.textContent) || {};
                        } catch (e) {
                            detailsById = {};
                        }
                    }

                    function closeModal() {
                        modal.classList.add('hidden');
                        modal.setAttribute('aria-hidden', 'true');
                        document.body.style.overflow = '';
                    }

                    function renderEligibleDetails(payload) {
                        if (!sectionsEl) return;
                        sectionsEl.innerHTML = '';
                        if (!payload || !Array.isArray(payload.sections)) return;
                        payload.sections.forEach(function (sec) {
                            if (sec.hint_only) {
                                var hint = document.createElement('p');
                                hint.className = 'text-xs text-slate-500 dark:text-slate-400';
                                hint.textContent = sec.hint_only;
                                sectionsEl.appendChild(hint);
                                return;
                            }
                            var wrap = document.createElement('div');
                            wrap.className = 'space-y-3';
                            if (sec.title) {
                                var h = document.createElement('h4');
                                h.className = 'text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400';
                                h.textContent = sec.title;
                                wrap.appendChild(h);
                            }
                            var grid = document.createElement('dl');
                            grid.className = 'grid grid-cols-1 gap-3 sm:grid-cols-2';
                            (sec.rows || []).forEach(function (row) {
                                var cell = document.createElement('div');
                                cell.className = 'rounded-xl border border-slate-200/80 bg-slate-50/80 p-3 dark:border-slate-700 dark:bg-slate-800/50';
                                if (row.full_width) cell.classList.add('sm:col-span-2');
                                var dt = document.createElement('dt');
                                dt.className = 'text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400';
                                dt.textContent = row.label || '';
                                var dd = document.createElement('dd');
                                dd.className = 'mt-1 text-sm text-slate-900 dark:text-slate-100 break-words';
                                if (row.link_href) {
                                    var a = document.createElement('a');
                                    a.href = row.link_href;
                                    a.target = '_blank';
                                    a.rel = 'noopener noreferrer';
                                    a.className = 'inline-flex max-w-full items-center gap-1.5 break-all font-medium text-amber-700 hover:underline dark:text-amber-300';
                                    a.textContent = row.link_label || row.value || '';
                                    dd.appendChild(a);
                                } else {
                                    dd.style.whiteSpace = row.full_width ? 'pre-wrap' : 'normal';
                                    dd.textContent = row.value != null ? String(row.value) : '—';
                                }
                                cell.appendChild(dt);
                                cell.appendChild(dd);
                                grid.appendChild(cell);
                            });
                            wrap.appendChild(grid);
                            sectionsEl.appendChild(wrap);
                        });
                    }

                    document.querySelectorAll('.js-open-sangha-details').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            var id = btn.getAttribute('data-sangha-id');
                            var payload = id ? detailsById[id] : null;
                            if (nameEl) nameEl.textContent = (payload && payload.heading) ? payload.heading : '—';
                            renderEligibleDetails(payload || { sections: [] });
                            modal.classList.remove('hidden');
                            modal.setAttribute('aria-hidden', 'false');
                            document.body.style.overflow = 'hidden';
                            var sc = document.getElementById('eligible-details-scroll');
                            if (sc) sc.scrollTop = 0;
                        });
                    });

                    closeBtn.addEventListener('click', closeModal);
                    modal.addEventListener('click', function(event) {
                        if (event.target.classList && event.target.classList.contains('js-eligible-modal-backdrop')) {
                            closeModal();
                        }
                    });
                    document.addEventListener('keydown', function(event) {
                        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                            closeModal();
                        }
                    });
                })();
                </script>
        </section>
    @endif

    @if($screen === 'needed-update')
        <section class="mp-eligible-panel mb-8 overflow-hidden rounded-2xl border border-slate-200/70 bg-gradient-to-b from-white to-slate-50/95 shadow-md ring-1 ring-slate-900/[0.03] dark:border-slate-700/65 dark:from-slate-900 dark:to-slate-950 dark:shadow-black/30 dark:ring-white/[0.05]">
            <div class="relative px-4 py-4 sm:px-6 sm:py-5">
                <div class="absolute left-0 top-0 h-1 w-full bg-gradient-to-r from-amber-400/70 via-orange-500/60 to-rose-500/55 dark:from-amber-500/45 dark:via-orange-500/35 dark:to-rose-600/30" aria-hidden="true"></div>
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-amber-800 dark:text-amber-300">{{ t('mp_needed_update_placeholder_title', 'Needed Update') }}</h2>
                        <p class="mt-1 text-xs leading-relaxed text-slate-600 dark:text-slate-300">{{ t('mp_needed_update_hint', 'Update the listed applications and resubmit them to continue the review process.') }}</p>
                    </div>
                    <span class="mp-eligible-count-badge inline-flex items-center gap-1 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs">
                        {{ t('total', 'Total') }}: {{ $neededUpdateSanghas->count() }}
                    </span>
                </div>
            </div>
            <div class="mp-eligible-table-wrap w-full min-w-0">
                <table class="w-full min-w-0 table-fixed text-xs sm:text-sm">
                    <thead>
                        <tr class="text-left text-[11px] uppercase tracking-wider">
                            <th class="px-4 py-3 sm:px-6">{{ t('roll_number', 'Roll Number') }}</th>
                            <th class="px-4 py-3 sm:px-6">{{ t('sanghas', 'Sangha') }}</th>
                            <th class="px-4 py-3 sm:px-6">{{ t('exams', 'Exam') }}</th>
                            <th class="px-4 py-3 sm:px-6">{{ t('created_at', 'Created') }}</th>
                            <th class="px-4 py-3 sm:px-6">{{ t('feedback_reason', 'Reason') }}</th>
                            <th class="px-4 py-3 sm:px-6 text-right">{{ t('actions', 'Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($neededUpdateSanghas as $sangha)
                            <tr>
                                <td class="px-4 py-3 sm:px-6 font-mono text-xs tracking-wide">
                                    <span class="mp-eligible-roll">{{ $sangha->eligible_roll_number ?: '—' }}</span>
                                </td>
                                <td class="px-4 py-3 sm:px-6">
                                    <p class="font-semibold">{{ $sangha->name }}</p>
                                </td>
                                <td class="px-4 py-3 sm:px-6 mp-eligible-muted">{{ $sangha->exam?->name ?? t('no_exam_selected', 'No exam selected') }}</td>
                                <td class="px-4 py-3 sm:px-6 text-xs mp-eligible-muted">{{ $sangha->created_at?->format('M d, Y H:i') }}</td>
                                <td class="px-4 py-3 sm:px-6">
                                    @if(filled($sangha->rejection_reason))
                                        <div class="mp-reason-card max-w-[22rem]">
                                            <p class="mp-reason-preview text-xs leading-relaxed text-amber-800/90 dark:text-amber-200/90 break-all">{{ \Illuminate\Support\Str::limit((string) $sangha->rejection_reason, 84) }}</p>
                                            <button type="button" class="mp-reason-link js-open-reason-modal mt-1.5" data-reason="{{ e($sangha->rejection_reason) }}">
                                                {{ t('view_more', 'View more') }}
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-500 dark:text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 sm:px-6 text-right">
                                    <div class="mp-row-actions">
                                        <button type="button" class="mp-action-btn mp-action-btn--secondary js-open-sangha-details" data-sangha-id="{{ $sangha->id }}">
                                            {{ t('view_details', 'View Details') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-sm text-slate-500 dark:text-slate-400 sm:px-6">{{ t('no_needed_update_sanghas', 'No applications need updates right now.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
            $editingFeedback = $editingFeedbackSangha ?? null;
            $editingNeededUpdate = $editingFeedback?->workflow_status === \App\Models\Sangha::STATUS_NEEDED_UPDATE;
        @endphp
        <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 p-5 sm:p-6 shadow-sm mb-8">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $editingFeedback ? t('edit_resubmit_application', 'Edit & resubmit') : t('new_sangha', 'New Sangha') }}</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $editingFeedback ? t('sangha_resubmit_form_hint', 'Update the information below and resubmit for administrator review.') : t('sangha_application_form', 'Sangha Application Form') }}</p>
            </div>
            {{-- Use url() for update so the page does not 500 if route name is missing from a stale route:cache; path matches Route::put('sanghas/{sangha}', …). --}}
            <form action="{{ $editingFeedback ? url('monastery/sanghas/'.$editingFeedback->id) : route('monastery.sanghas.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @if(!empty($programmeContext))
                    <input type="hidden" name="programme" value="{{ $programmeContext }}">
                @endif
                @if($editingFeedback)
                    @method('PUT')
                @endif
                <div class="grid gap-4 sm:grid-cols-2">
                    @if(!\App\Models\CustomField::isBuiltInSlugSuppressed('sangha', 'name'))
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $mpMetaName?->name ?? t('sangha_name', 'Sangha Name') }}{{ ($mpMetaName?->required ?? true) ? ' *' : '' }}</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $editingFeedback?->name ?? '') }}" class="mt-1 w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900/60 px-3 py-2.5 text-sm" placeholder="{{ $mpMetaName?->placeholder }}" @if($mpMetaName?->required ?? true) required @endif>
                        @error('name')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    @endif
                    @if(!\App\Models\CustomField::isBuiltInSlugSuppressed('sangha', 'father_name'))
                    <div>
                        <label for="father_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $mpMetaFather?->name ?? t('score_father_name_label', 'Father name') }}{{ ($mpMetaFather?->required ?? false) ? ' *' : '' }}</label>
                        <input type="text" id="father_name" name="father_name" value="{{ old('father_name', $editingFeedback?->father_name ?? '') }}" maxlength="255" class="mt-1 w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900/60 px-3 py-2.5 text-sm" placeholder="{{ $mpMetaFather?->placeholder }}" @if($mpMetaFather?->required ?? false) required @endif>
                        @error('father_name')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    @endif
                    @if(!\App\Models\CustomField::isBuiltInSlugSuppressed('sangha', 'nrc_number'))
                    <div>
                        <label for="nrc_number" class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $mpMetaNrc?->name ?? t('score_nrc_label', 'NRC number') }}{{ ($mpMetaNrc?->required ?? false) ? ' *' : '' }}</label>
                        <input type="text" id="nrc_number" name="nrc_number" value="{{ old('nrc_number', $editingFeedback?->nrc_number ?? '') }}" maxlength="100" class="mt-1 w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900/60 px-3 py-2.5 text-sm" placeholder="{{ $mpMetaNrc?->placeholder }}" @if($mpMetaNrc?->required ?? false) required @endif>
                        @error('nrc_number')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    @endif
                </div>
                @if(!\App\Models\CustomField::isBuiltInSlugSuppressed('sangha', 'exam_id'))
                <div>
                    <label for="exam_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $mpMetaExam?->name ?? t('exam') }}{{ ($mpMetaExam?->required ?? false) ? ' *' : '' }}</label>
                    <select id="exam_id" name="exam_id" class="mt-1 w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900/60 px-3 py-2.5 text-sm" @if($mpMetaExam?->required ?? false) required @endif>
                        <option value="">{{ $mpMetaExam?->placeholder ?: (($mpMetaExam?->required ?? false) ? t('select_exam', 'Select exam') : t('select_exam_optional', 'Select exam (optional)')) }}</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}" {{ (string) old('exam_id', $editingFeedback?->exam_id ?? '') === (string) $exam->id ? 'selected' : '' }}>{{ $exam->name }}{{ $exam->exam_date ? ' (' . $exam->exam_date->format('M d, Y') . ')' : '' }}</option>
                        @endforeach
                    </select>
                    @error('exam_id')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                @endif
                @if(!\App\Models\CustomField::isBuiltInSlugSuppressed('sangha', 'description'))
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $mpMetaDesc?->name ?? t('description') }}{{ ($mpMetaDesc?->required ?? false) ? ' *' : '' }}</label>
                    <textarea id="description" name="description" rows="3" class="mt-1 w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900/60 px-3 py-2.5 text-sm" placeholder="{{ $mpMetaDesc?->placeholder }}" @if($mpMetaDesc?->required ?? false) required @endif>{{ old('description', $editingFeedback?->description ?? '') }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                @endif
                @if($editingFeedback && filled($editingFeedback->rejection_reason))
                    <div class="rounded-xl border border-amber-200/80 bg-amber-50/80 p-3 dark:border-amber-700/60 dark:bg-amber-900/20">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-amber-800 dark:text-amber-200">{{ $editingNeededUpdate ? t('needed_update_note', 'Needed Update note') : t('rejection_note', 'Rejection note') }}</p>
                        <p class="mt-1 text-sm text-amber-900/95 dark:text-amber-100/95 whitespace-pre-wrap break-words">{{ $editingFeedback->rejection_reason }}</p>
                </div>
                @endif
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ t('sangha_portal_no_student_id_hint', 'Student Id for login is assigned by an administrator after review.') }}</p>
                @if(empty($programmeContext) && $sanghaCustomFields->isNotEmpty())
                    <div class="grid gap-4 sm:grid-cols-2">
                        @include('website.partials.custom-fields', [
                            'customFields' => $sanghaCustomFields,
                            'idPrefix' => 'monastery_sangha',
                            'oldPrefix' => null,
                            'customFieldValueDefaults' => $sanghaEditCustomFieldDefaults ?? [],
                        ])
                    </div>
                @endif
                @if(($programmeCustomFields ?? collect())->isNotEmpty())
                    <div class="rounded-xl border border-slate-700/65 bg-slate-900/40 p-4 shadow-[inset_0_1px_0_rgba(255,255,255,0.04)] dark:border-slate-700/70 dark:bg-slate-900/40 sm:p-5">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ t('programme_fields', 'Programme fields') }}</h3>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ t('programme_fields_hint', 'Fields below are specific to the selected programme.') }}</p>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            @include('website.partials.custom-fields', [
                                'customFields' => $programmeCustomFields,
                                'idPrefix' => 'monastery_programme',
                                'oldPrefix' => null,
                                'customFieldValueDefaults' => $programmeEditCustomFieldDefaults ?? [],
                            ])
                        </div>
                    </div>
                @endif
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-amber-500 hover:bg-amber-400 text-white text-sm font-semibold transition-colors">
                    {{ $editingFeedback ? t('resubmit_sangha_application', 'Resubmit application') : t('submit_sangha_application', 'Submit Sangha Application') }}
                </button>
            </form>
        </section>
    @endif

    @if($screen === 'pending')
        <section class="mp-eligible-panel mb-8 overflow-hidden rounded-2xl border border-slate-200/70 bg-gradient-to-b from-white to-slate-50/95 shadow-md ring-1 ring-slate-900/[0.03] dark:border-slate-700/65 dark:from-slate-900 dark:to-slate-950 dark:shadow-black/30 dark:ring-white/[0.05]">
            <div class="relative px-4 py-4 sm:px-6 sm:py-5">
                <div class="absolute left-0 top-0 h-1 w-full bg-gradient-to-r from-amber-400/80 via-amber-500/70 to-orange-500/60 dark:from-amber-500/60 dark:via-amber-500/50 dark:to-orange-600/40" aria-hidden="true"></div>
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-amber-800 dark:text-amber-300">{{ t('pending_sangha_list', 'Pending Sangha List') }}</h2>
            </div>
                    <span class="mp-eligible-count-badge inline-flex items-center gap-1 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs">
                        {{ t('total', 'Total') }}: {{ $pendingSanghas->count() }}
                    </span>
                    </div>
            </div>
            <div class="mp-eligible-table-wrap w-full min-w-0">
                <table class="w-full min-w-0 table-fixed text-xs sm:text-sm">
                    <thead>
                        <tr class="text-left text-[11px] uppercase tracking-wider">
                            <th class="px-4 py-3 sm:px-6">{{ t('roll_number', 'Roll Number') }}</th>
                            <th class="px-4 py-3 sm:px-6">
                                <span class="block">{{ t('desk_number_short', 'Desk No.') }}</span>
                                <span class="block whitespace-nowrap text-[10px] font-normal normal-case leading-tight text-slate-500 dark:text-slate-400 sm:text-[11px]">({{ t('exam_roll_number', 'Exam Roll Number') }})</span>
                            </th>
                            <th class="px-4 py-3 sm:px-6">{{ t('sanghas', 'Sangha') }}</th>
                            <th class="px-4 py-3 sm:px-6">{{ t('exams', 'Exam') }}</th>
                            <th class="px-4 py-3 sm:px-6">{{ t('created_at', 'Created') }}</th>
                            <th class="px-4 py-3 sm:px-6">{{ t('status', 'Status') }}</th>
                            <th class="px-4 py-3 sm:px-6 text-right">{{ t('actions', 'Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingSanghas as $sangha)
                            <tr>
                                <td class="px-4 py-3 sm:px-6 font-mono text-xs tracking-wide">
                                    <span class="mp-eligible-roll">{{ $sangha->eligible_roll_number ?: '—' }}</span>
                                </td>
                                <td class="px-4 py-3 sm:px-6 font-mono text-xs tracking-wide">
                                    @if(filled($sangha->desk_number))
                                        <span class="mp-eligible-roll">{{ ($sangha->exam?->desk_number_prefix ?? '') . str_pad((string) $sangha->desk_number, 6, '0', STR_PAD_LEFT) }}</span>
                                    @else
                                        <span class="text-slate-500 dark:text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 sm:px-6">
                                    <p class="font-semibold">{{ $sangha->name }}</p>
                                </td>
                                <td class="px-4 py-3 sm:px-6 mp-eligible-muted">{{ $sangha->exam?->name ?? t('no_exam_selected', 'No exam selected') }}</td>
                                <td class="px-4 py-3 sm:px-6 text-xs mp-eligible-muted">{{ $sangha->created_at?->format('M d, Y H:i') }}</td>
                                <td class="px-4 py-3 sm:px-6">
                                    <span class="inline-flex rounded-full bg-amber-500/15 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-amber-800 ring-1 ring-amber-500/25 dark:text-amber-200">{{ t('status_pending', 'Pending') }}</span>
                                </td>
                                <td class="px-4 py-3 sm:px-6 text-right">
                                    <button type="button" class="mp-action-btn mp-action-btn--secondary js-open-sangha-details" data-sangha-id="{{ $sangha->id }}">
                                        {{ t('view_details', 'View Details') }}
                                    </button>
                                </td>
                            </tr>
                @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-sm text-slate-500 dark:text-slate-400 sm:px-6">{{ t('no_pending_sangha_applications', 'No pending sangha applications.') }}</td>
                            </tr>
                @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    @if($screen === 'approved')
        <section class="mp-eligible-panel mb-8 overflow-hidden rounded-2xl border border-slate-200/70 bg-gradient-to-b from-white to-slate-50/95 shadow-md ring-1 ring-slate-900/[0.03] dark:border-slate-700/65 dark:from-slate-900 dark:to-slate-950 dark:shadow-black/30 dark:ring-white/[0.05]">
            <div class="relative px-4 py-4 sm:px-6 sm:py-5">
                <div class="absolute left-0 top-0 h-1 w-full bg-gradient-to-r from-emerald-400/80 via-teal-500/70 to-cyan-500/60 dark:from-emerald-500/60 dark:via-teal-500/50 dark:to-cyan-600/40" aria-hidden="true"></div>
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-emerald-800 dark:text-emerald-300">{{ t('approved_sangha_list', 'Approved Sangha List') }}</h2>
            </div>
                    <span class="mp-eligible-count-badge inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs">
                        {{ t('total', 'Total') }}: {{ $approvedSanghas->count() }}
                    </span>
                    </div>
            </div>
            <div class="mp-eligible-table-wrap w-full min-w-0">
                <table class="w-full min-w-0 table-fixed text-xs sm:text-sm">
                    <thead>
                        <tr class="text-left text-[11px] uppercase tracking-wider">
                            <th class="px-4 py-3 sm:px-6">{{ t('roll_number', 'Roll Number') }}</th>
                            <th class="px-4 py-3 sm:px-6">
                                <span class="block">{{ t('desk_number_short', 'Desk No.') }}</span>
                                <span class="block whitespace-nowrap text-[10px] font-normal normal-case leading-tight text-slate-500 dark:text-slate-400 sm:text-[11px]">({{ t('exam_roll_number', 'Exam Roll Number') }})</span>
                            </th>
                            <th class="px-4 py-3 sm:px-6">{{ t('sanghas', 'Sangha') }}</th>
                            <th class="px-4 py-3 sm:px-6">{{ t('exams', 'Exam') }}</th>
                            <th class="px-4 py-3 sm:px-6">{{ t('created_at', 'Created') }}</th>
                            <th class="px-4 py-3 sm:px-6">{{ t('status', 'Status') }}</th>
                            <th class="px-4 py-3 sm:px-6 text-right">{{ t('actions', 'Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($approvedSanghas as $sangha)
                            <tr>
                                <td class="px-4 py-3 sm:px-6 font-mono text-xs tracking-wide">
                                    <span class="mp-eligible-roll">{{ $sangha->eligible_roll_number ?: '—' }}</span>
                                </td>
                                <td class="px-4 py-3 sm:px-6 font-mono text-xs tracking-wide">
                                    @if(filled($sangha->desk_number))
                                        <span class="mp-eligible-roll">{{ ($sangha->exam?->desk_number_prefix ?? '') . str_pad((string) $sangha->desk_number, 6, '0', STR_PAD_LEFT) }}</span>
                                    @else
                                        <span class="text-slate-500 dark:text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 sm:px-6">
                                    <p class="font-semibold">{{ $sangha->name }}</p>
                                </td>
                                <td class="px-4 py-3 sm:px-6 mp-eligible-muted">{{ $sangha->exam?->name ?? t('no_exam_selected', 'No exam selected') }}</td>
                                <td class="px-4 py-3 sm:px-6 text-xs mp-eligible-muted">{{ $sangha->created_at?->format('M d, Y H:i') }}</td>
                                <td class="px-4 py-3 sm:px-6">
                                    <span class="inline-flex rounded-full bg-emerald-500/15 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-emerald-800 ring-1 ring-emerald-500/25 dark:text-emerald-200">{{ t('status_approved', 'Approved') }}</span>
                                </td>
                                <td class="px-4 py-3 sm:px-6 text-right">
                                    <button type="button" class="mp-action-btn mp-action-btn--secondary js-open-sangha-details" data-sangha-id="{{ $sangha->id }}">
                                        {{ t('view_details', 'View Details') }}
                                    </button>
                                </td>
                            </tr>
                @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-sm text-slate-500 dark:text-slate-400 sm:px-6">{{ t('no_approved_sanghas_yet', 'No approved sanghas yet.') }}</td>
                            </tr>
                @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    @if($screen === 'rejected')
        <section class="mp-eligible-panel mb-8 overflow-hidden rounded-2xl border border-slate-200/70 bg-gradient-to-b from-white to-slate-50/95 shadow-md ring-1 ring-slate-900/[0.03] dark:border-slate-700/65 dark:from-slate-900 dark:to-slate-950 dark:shadow-black/30 dark:ring-white/[0.05]">
            <div class="relative px-4 py-4 sm:px-6 sm:py-5">
                <div class="absolute left-0 top-0 h-1 w-full bg-gradient-to-r from-rose-400/80 via-red-500/70 to-red-600/60 dark:from-rose-500/60 dark:via-red-500/50 dark:to-red-700/40" aria-hidden="true"></div>
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-red-800 dark:text-red-300">{{ t('rejected_sangha_list', 'Rejected Sangha List') }}</h2>
            </div>
                    <span class="mp-eligible-count-badge inline-flex items-center gap-1 rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs">
                        {{ t('total', 'Total') }}: {{ $rejectedSanghas->count() }}
                    </span>
                                    </div>
                                </div>
            <div class="mp-eligible-table-wrap w-full min-w-0">
                <table class="w-full min-w-0 table-fixed text-xs sm:text-sm">
                    <thead>
                        <tr class="text-left text-[11px] uppercase tracking-wider">
                            <th class="px-4 py-3 sm:px-6">{{ t('roll_number', 'Roll Number') }}</th>
                            <th class="px-4 py-3 sm:px-6">{{ t('sanghas', 'Sangha') }}</th>
                            <th class="px-4 py-3 sm:px-6">{{ t('exams', 'Exam') }}</th>
                            <th class="px-4 py-3 sm:px-6">{{ t('created_at', 'Created') }}</th>
                            <th class="px-4 py-3 sm:px-6">{{ t('rejection_note', 'Rejection Note') }}</th>
                            <th class="px-4 py-3 sm:px-6 text-right">{{ t('actions', 'Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rejectedSanghas as $sangha)
                            <tr>
                                <td class="px-4 py-3 sm:px-6 font-mono text-xs tracking-wide">
                                    <span class="mp-eligible-roll">{{ $sangha->eligible_roll_number ?: '—' }}</span>
                                </td>
                                <td class="px-4 py-3 sm:px-6">
                                    <p class="font-semibold">{{ $sangha->name }}</p>
                                </td>
                                <td class="px-4 py-3 sm:px-6 mp-eligible-muted">{{ $sangha->exam?->name ?? t('no_exam_selected', 'No exam selected') }}</td>
                                <td class="px-4 py-3 sm:px-6 text-xs mp-eligible-muted">{{ $sangha->created_at?->format('M d, Y H:i') }}</td>
                                <td class="px-4 py-3 sm:px-6">
                                    @if(filled($sangha->rejection_reason))
                                        <div class="mp-reason-card max-w-[22rem]">
                                            <p class="mp-reason-preview text-xs leading-relaxed text-red-700/90 dark:text-red-200/90 break-all">{{ \Illuminate\Support\Str::limit((string) $sangha->rejection_reason, 84) }}</p>
                                            <button type="button" class="mp-reason-link js-open-reason-modal mt-1.5" data-reason="{{ e($sangha->rejection_reason) }}">
                                                {{ t('view_more', 'View more') }}
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-500 dark:text-slate-400">—</span>
                        @endif
                                </td>
                                <td class="px-4 py-3 sm:px-6 text-right">
                                    <div class="mp-row-actions">
                                        <button type="button" class="mp-action-btn mp-action-btn--secondary js-open-sangha-details" data-sangha-id="{{ $sangha->id }}">
                                            {{ t('view_details', 'View Details') }}
                                        </button>
                    </div>
                                </td>
                            </tr>
                @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-sm text-slate-500 dark:text-slate-400 sm:px-6">{{ t('no_rejected_sanghas', 'No rejected sanghas.') }}</td>
                            </tr>
                @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    @if(in_array($screen, ['pending', 'approved', 'needed-update', 'rejected', 'total'], true))
        <div id="mp-reason-modal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
            <div class="js-close-reason-modal absolute inset-0 bg-slate-950/55 backdrop-blur-[2px]"></div>
            <div class="relative z-10 flex min-h-full items-center justify-center p-4 sm:p-6">
                <div class="w-full max-w-xl rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">
                    <div class="flex items-center justify-between border-b border-slate-200/80 px-5 py-3.5 dark:border-slate-700/80">
                        <h4 class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ t('feedback_reason', 'Reason') }}</h4>
                        <button type="button" class="js-close-reason-modal rounded-md border border-slate-300 px-2 py-1 text-xs text-slate-600 hover:bg-slate-100 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800">✕</button>
                    </div>
                    <div id="mp-reason-modal-content" class="max-h-[62vh] overflow-y-auto px-5 py-4 text-sm leading-relaxed text-slate-800 dark:text-slate-100 whitespace-pre-wrap break-words"></div>
                </div>
            </div>
        </div>
        <script type="application/json" id="mp-sangha-details-json">@json($portalSanghaDetailsById)</script>
        <div id="eligible-details-modal" class="fixed inset-0 z-50 hidden overflow-y-auto overscroll-y-contain" aria-hidden="true">
            <div class="js-eligible-modal-backdrop absolute inset-0 bg-slate-950/60 backdrop-blur-[2px]" aria-hidden="true"></div>
            <div class="relative z-10 flex min-h-full items-center justify-center p-4 sm:p-6">
                <div class="mp-eligible-details-panel my-6 flex w-full max-w-2xl flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900 sm:my-10" style="height: min(88vh, 44rem); max-height: min(88vh, 44rem);" role="dialog" aria-modal="true" aria-labelledby="eligible-details-name">
                    <div class="shrink-0 border-b border-slate-200/80 px-5 py-4 dark:border-slate-700/80 sm:px-6">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 pr-2">
                                <p class="text-xs font-semibold uppercase tracking-wider text-sky-700 dark:text-sky-300">{{ t('sangha_information', 'Sangha Information') }}</p>
                                <h3 id="eligible-details-name" class="mt-1 truncate text-lg font-semibold text-slate-900 dark:text-slate-100">—</h3>
                            </div>
                            <button type="button" id="eligible-details-close" class="shrink-0 rounded-lg border border-slate-300 px-2.5 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800">✕</button>
                        </div>
                    </div>
                    <div id="eligible-details-scroll" class="min-h-0 flex-1 overflow-y-auto overscroll-contain px-5 py-4 sm:px-6 [scrollbar-width:thin]">
                        <div id="eligible-details-sections" class="space-y-6"></div>
                    </div>
                </div>
            </div>
        </div>
        <script>
        (function() {
            var modal = document.getElementById('eligible-details-modal');
            var closeBtn = document.getElementById('eligible-details-close');
            if (!modal || !closeBtn) return;
            var nameEl = document.getElementById('eligible-details-name');
            var sectionsEl = document.getElementById('eligible-details-sections');
            var jsonEl = document.getElementById('mp-sangha-details-json');
            var detailsById = {};
            if (jsonEl && jsonEl.textContent) {
                try { detailsById = JSON.parse(jsonEl.textContent) || {}; } catch (e) { detailsById = {}; }
            }
            function closeModal() {
                modal.classList.add('hidden');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }
            function renderDetails(payload) {
                if (!sectionsEl) return;
                sectionsEl.innerHTML = '';
                if (!payload || !Array.isArray(payload.sections)) return;
                payload.sections.forEach(function(sec) {
                    if (sec.hint_only) {
                        var hint = document.createElement('p');
                        hint.className = 'text-xs text-slate-500 dark:text-slate-400';
                        hint.textContent = sec.hint_only;
                        sectionsEl.appendChild(hint);
                        return;
                    }
                    var wrap = document.createElement('div');
                    wrap.className = 'space-y-3';
                    if (sec.title) {
                        var h = document.createElement('h4');
                        h.className = 'text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400';
                        h.textContent = sec.title;
                        wrap.appendChild(h);
                    }
                    var grid = document.createElement('dl');
                    grid.className = 'grid grid-cols-1 gap-3 sm:grid-cols-2';
                    (sec.rows || []).forEach(function(row) {
                        var cell = document.createElement('div');
                        cell.className = 'rounded-xl border border-slate-200/80 bg-slate-50/80 p-3 dark:border-slate-700 dark:bg-slate-800/50';
                        if (row.full_width) cell.classList.add('sm:col-span-2');
                        var dt = document.createElement('dt');
                        dt.className = 'text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400';
                        dt.textContent = row.label || '';
                        var dd = document.createElement('dd');
                        dd.className = 'mt-1 text-sm text-slate-900 dark:text-slate-100 break-words';
                        if (row.link_href) {
                            var a = document.createElement('a');
                            a.href = row.link_href;
                            a.target = '_blank';
                            a.rel = 'noopener noreferrer';
                            a.className = 'inline-flex max-w-full items-center gap-1.5 break-all font-medium text-amber-700 hover:underline dark:text-amber-300';
                            a.textContent = row.link_label || row.value || '';
                            dd.appendChild(a);
                        } else {
                            dd.style.whiteSpace = row.full_width ? 'pre-wrap' : 'normal';
                            dd.textContent = row.value != null ? String(row.value) : '—';
                        }
                        cell.appendChild(dt);
                        cell.appendChild(dd);
                        grid.appendChild(cell);
                    });
                    wrap.appendChild(grid);
                    sectionsEl.appendChild(wrap);
                });
            }
            document.querySelectorAll('.js-open-sangha-details').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var id = btn.getAttribute('data-sangha-id');
                    var payload = id ? detailsById[id] : null;
                    if (nameEl) nameEl.textContent = (payload && payload.heading) ? payload.heading : '—';
                    renderDetails(payload || { sections: [] });
                    modal.classList.remove('hidden');
                    modal.setAttribute('aria-hidden', 'false');
                    document.body.style.overflow = 'hidden';
                    var sc = document.getElementById('eligible-details-scroll');
                    if (sc) sc.scrollTop = 0;
                });
            });
            closeBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', function(event) {
                if (event.target.classList && event.target.classList.contains('js-eligible-modal-backdrop')) closeModal();
            });
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
            });
        })();
        </script>
        <script>
        (function() {
            var modal = document.getElementById('mp-reason-modal');
            var content = document.getElementById('mp-reason-modal-content');
            if (!modal || !content) return;
            function closeModal() {
                modal.classList.add('hidden');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }
            document.querySelectorAll('.js-open-reason-modal').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    content.textContent = btn.getAttribute('data-reason') || '';
                    modal.classList.remove('hidden');
                    modal.setAttribute('aria-hidden', 'false');
                    document.body.style.overflow = 'hidden';
                });
            });
            document.querySelectorAll('.js-close-reason-modal').forEach(function(el) {
                el.addEventListener('click', closeModal);
            });
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
            });
        })();
        </script>
    @endif

    @if($screen === 'request')
        <section class="mb-8 overflow-hidden rounded-3xl border border-slate-200/70 bg-gradient-to-b from-white to-slate-50/90 shadow-md shadow-slate-200/30 ring-1 ring-slate-900/[0.03] dark:border-slate-700/55 dark:from-slate-900/90 dark:to-slate-950/90 dark:shadow-black/20 dark:ring-white/[0.04]">
            <div class="relative px-5 py-5 sm:px-8 sm:py-6">
                <div class="absolute left-0 top-0 h-1 w-full bg-gradient-to-r from-amber-400/80 via-amber-500/70 to-amber-600/60 dark:from-amber-500/50 dark:via-amber-600/40 dark:to-amber-700/30" aria-hidden="true"></div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-amber-700/90 dark:text-amber-400/90">{{ t('monastery_transfer_sangha_kicker', 'Submit a transfer') }}</p>
                        <h2 class="mt-1.5 text-xl font-semibold tracking-tight text-slate-900 dark:text-slate-50 sm:text-2xl">{{ t('monastery_requests', 'Transfer Sangha') }}</h2>
                        <p class="mt-2 max-w-xl text-sm leading-relaxed text-slate-600 dark:text-slate-400">{{ t('request_form_portal_hint', 'Use the fields below to submit a transfer. It will appear as Pending for administrators until they approve or reject it.') }}</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-200/60 bg-slate-50/50 px-5 py-6 sm:px-8 sm:py-8 dark:border-slate-800/80 dark:bg-slate-950/35">
                <div class="mx-auto grid max-w-6xl grid-cols-1 gap-8 lg:grid-cols-12 lg:gap-10">
                    {{-- Form: left, generous column --}}
                    <div class="min-w-0 lg:col-span-7">
                        <form action="{{ route('monastery.messages.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                            @csrf
                            @if(!empty($programmeContext))
                                <input type="hidden" name="programme" value="{{ $programmeContext }}">
                            @endif
                            @if($requestCustomFields->isNotEmpty())
                                <div class="monastery-request-form-fields space-y-4">
                                    @include('website.partials.custom-fields', [
                                        'customFields' => $requestCustomFields,
                                        'idPrefix' => 'monastery_request',
                                        'oldPrefix' => null,
                                        'variant' => 'monastery',
                                        'monasteryApprovedSanghasForExam' => $monasteryExamApprovedSanghas,
                                        'monasteryApprovedSanghasForTransfer' => $monasteryApprovedSanghasForTransfer,
                                        'monasterySelectMonasteries' => $monasterySelectMonasteries,
                                        'monasteryTransferFromName' => $monastery->name,
                                    ])
                                </div>
                                <button type="submit" class="flex w-full items-center justify-center rounded-xl bg-amber-600 px-5 py-3 text-sm font-semibold text-white shadow-md shadow-amber-900/15 transition hover:bg-amber-500 active:scale-[0.995] dark:bg-amber-600 dark:shadow-amber-950/30 dark:hover:bg-amber-500">
                                    {{ t('submit_request', 'Submit transfer') }}
                                </button>
                            @else
                                <p class="rounded-2xl border border-amber-200/80 bg-amber-50/90 px-5 py-4 text-sm text-amber-900 dark:border-amber-800/40 dark:bg-amber-950/30 dark:text-amber-200">{{ t('no_request_fields_configured', 'No transfer form fields are configured yet. Please contact the administrator.') }}</p>
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
                                        'monasteryApprovedSanghasForExam' => $monasteryExamApprovedSanghas,
                                        'monasteryExamCatalogYears' => $monasteryExamCatalogYears ?? [],
                                        'monasteryExamCatalogByYear' => $monasteryExamCatalogByYear ?? [],
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
        @php
        $mpResultsRowChevron = 'h-4 w-4 shrink-0 text-slate-400 transition group-hover:translate-x-0.5 group-hover:text-slate-200 dark:text-slate-500 dark:group-hover:text-slate-300';
        @endphp

    @if($screen === 'results-year')
        <section class="mb-8 space-y-5">
            <a href="{{ route('monastery.dashboard', ['tab' => 'results', 'screen' => 'results-home']) }}" class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100">
                @include('partials.icon', ['name' => 'arrow-left', 'class' => 'h-4 w-4'])
                {{ t('back_to_results', 'Back to Results') }}
            </a>
            <div class="rounded-xl border border-slate-200/80 bg-white px-4 py-3.5 dark:border-slate-600/80 dark:bg-slate-800">
                <h2 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ t('results_pick_level', 'Choose a level') }}</h2>
                <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">{{ t('results_year_context', 'Year') }} <span class="font-medium text-slate-800 dark:text-slate-200">{{ \App\Support\MonasteryResultsExplorer::yearLabel($resultsYearKey) }}</span></p>
                    </div>
            @if($resultsExplorerLevels->isNotEmpty())
                <div class="overflow-hidden rounded-xl border border-slate-200/80 bg-white dark:border-slate-600/80 dark:bg-slate-800">
                    @foreach($resultsExplorerLevels as $lvl)
                        <a href="{{ route('monastery.dashboard', ['tab' => 'results', 'screen' => 'results-level', 'results_year' => $resultsYearKey, 'results_level' => $lvl['level_key']]) }}" class="group flex items-center gap-3 border-b border-slate-100 px-4 py-3.5 last:border-b-0 transition-colors hover:bg-slate-50 dark:border-slate-700/80 dark:hover:!bg-slate-800/90">
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400 dark:text-slate-500">{{ t('level', 'Level') }}</p>
                                <p class="mt-0.5 truncate text-[15px] font-medium text-slate-900 dark:text-slate-100">{{ $lvl['level_label'] }}</p>
                    </div>
                            <div class="flex shrink-0 items-center gap-2">
                                <span class="rounded-md bg-emerald-500/10 px-2 py-0.5 text-xs font-medium tabular-nums text-emerald-800 dark:bg-emerald-500/15 dark:text-emerald-200">{{ $lvl['pass_count'] }}</span>
                                <span class="rounded-md bg-rose-500/10 px-2 py-0.5 text-xs font-medium tabular-nums text-rose-800 dark:bg-rose-500/15 dark:text-rose-200">{{ $lvl['fail_count'] }}</span>
                                @include('partials.icon', ['name' => 'chevron-right', 'class' => $mpResultsRowChevron])
                </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="rounded-xl border border-slate-200/80 bg-slate-50 px-4 py-5 text-sm text-slate-600 dark:border-slate-600/80 dark:bg-slate-800/90 dark:text-slate-400">{{ t('monastery_results_list_empty', 'No entries in this list.') }}</p>
                @endif
        </section>
    @endif

    @if($screen === 'results-level')
        <section class="mb-8 space-y-5">
            <a href="{{ route('monastery.dashboard', ['tab' => 'results', 'screen' => 'results-year', 'results_year' => $resultsYearKey]) }}" class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100">
                @include('partials.icon', ['name' => 'arrow-left', 'class' => 'h-4 w-4'])
                {{ t('back', 'Back') }}
            </a>
            <div class="rounded-xl border border-slate-200/80 bg-white px-4 py-3.5 dark:border-slate-600/80 dark:bg-slate-800">
                <h2 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ t('results_pick_exam', 'Choose an exam') }}</h2>
                <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">
                    <span class="font-medium text-slate-700 dark:text-slate-300">{{ \App\Support\MonasteryResultsExplorer::yearLabel($resultsYearKey) }}</span>
                    <span class="mx-1.5 text-slate-300 dark:text-slate-600">/</span>
                    <span class="font-medium text-slate-700 dark:text-slate-300">{{ \App\Support\MonasteryResultsExplorer::levelLabel($resultsLevelKey) }}</span>
                </p>
            </div>
            @if($resultsExplorerExams->isNotEmpty())
                <div class="overflow-hidden rounded-xl border border-slate-200/80 bg-white dark:border-slate-600/80 dark:bg-slate-800">
                    @foreach($resultsExplorerExams as $ex)
                        <a href="{{ route('monastery.dashboard', ['tab' => 'results', 'screen' => 'results-exam', 'results_exam_id' => $ex['exam_id'], 'results_year' => $resultsYearKey, 'results_level' => $resultsLevelKey]) }}" class="group flex items-center gap-3 border-b border-slate-100 px-4 py-3.5 last:border-b-0 transition-colors hover:bg-slate-50 dark:border-slate-700/80 dark:hover:!bg-slate-800/90">
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400 dark:text-slate-500">{{ t('exams', 'Exam') }}</p>
                                <p class="mt-0.5 line-clamp-2 text-[15px] font-medium leading-snug text-slate-900 dark:text-slate-100">{{ $ex['exam_name'] }}</p>
                            </div>
                            <div class="flex shrink-0 items-center gap-2">
                                <span class="rounded-md bg-emerald-500/10 px-2 py-0.5 text-xs font-medium tabular-nums text-emerald-800 dark:bg-emerald-500/15 dark:text-emerald-200">{{ $ex['pass_count'] }}</span>
                                <span class="rounded-md bg-rose-500/10 px-2 py-0.5 text-xs font-medium tabular-nums text-rose-800 dark:bg-rose-500/15 dark:text-rose-200">{{ $ex['fail_count'] }}</span>
                                @include('partials.icon', ['name' => 'chevron-right', 'class' => $mpResultsRowChevron])
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="rounded-xl border border-slate-200/80 bg-slate-50 px-4 py-5 text-sm text-slate-600 dark:border-slate-600/80 dark:bg-slate-800/90 dark:text-slate-400">{{ t('monastery_results_list_empty', 'No entries in this list.') }}</p>
            @endif
        </section>
    @endif

    @if($screen === 'results-exam' && $resultsExplorerExamSummary)
        @php
            $rex = (int) $resultsExplorerExamSummary['exam_id'];
            $ry = request('results_year');
            $rl = request('results_level');
            $passFailBase = ['tab' => 'results', 'results_exam_id' => $rex];
            if (is_string($ry) && $ry !== '') {
                $passFailBase['results_year'] = $ry;
            }
            if (is_string($rl) && $rl !== '') {
                $passFailBase['results_level'] = $rl;
            }
        @endphp
        <section class="mb-8 space-y-5">
            <a href="{{ route('monastery.dashboard', ['tab' => 'results', 'screen' => 'results-level', 'results_year' => $resultsYearKey, 'results_level' => $resultsLevelKey]) }}" class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100">
                @include('partials.icon', ['name' => 'arrow-left', 'class' => 'h-4 w-4'])
                {{ t('back', 'Back') }}
            </a>
            <div class="rounded-xl border border-slate-200/80 bg-white px-4 py-4 dark:border-slate-600/80 dark:bg-slate-800">
                <h2 class="text-base font-semibold leading-snug text-slate-900 dark:text-slate-100">{{ $resultsExplorerExamSummary['exam_name'] }}</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    {{ \App\Support\MonasteryResultsExplorer::yearLabel($resultsExplorerExamSummary['year_key']) }}
                    <span class="mx-1 text-slate-300 dark:text-slate-600">·</span>
                    {{ \App\Support\MonasteryResultsExplorer::levelLabel($resultsExplorerExamSummary['level_key']) }}
                </p>
                @if($resultsPublishedAt)
                    <p class="mt-2 text-xs text-slate-400 dark:text-slate-500">{{ t('published_at', 'Published at') }}: {{ \Illuminate\Support\Carbon::parse($resultsPublishedAt)->format('M d, Y H:i') }}</p>
                @endif
                        </div>
            <div class="grid grid-cols-1 gap-3 min-[420px]:grid-cols-2">
                <a href="{{ route('monastery.dashboard', array_merge($passFailBase, ['screen' => 'pass'])) }}" class="group flex flex-col rounded-xl border border-slate-200/80 border-l-4 border-l-emerald-500 bg-white p-4 shadow-sm transition-colors hover:border-slate-300 hover:bg-slate-50/80 hover:shadow dark:border-slate-600 dark:border-l-emerald-600 dark:bg-slate-800 dark:shadow-none dark:hover:border-slate-500 dark:hover:!bg-slate-700 dark:hover:shadow-none">
                    <div class="flex items-start justify-between gap-2">
                        <span class="text-[11px] font-semibold uppercase tracking-wide text-emerald-800 dark:text-emerald-200/90">{{ t('pass_list', 'Pass list') }}</span>
                        @include('partials.icon', ['name' => 'chevron-right', 'class' => $mpResultsRowChevron])
                    </div>
                    <p class="mt-2 text-2xl font-bold tabular-nums text-emerald-700 dark:text-emerald-300">{{ $resultsExplorerExamSummary['pass_count'] }}</p>
                    <p class="mt-1 text-xs leading-relaxed text-slate-500 dark:text-slate-400">{{ t('monastery_results_pass_sub', 'Candidates who passed in the published list.') }}</p>
                </a>
                <a href="{{ route('monastery.dashboard', array_merge($passFailBase, ['screen' => 'fail'])) }}" class="group flex flex-col rounded-xl border border-slate-200/80 border-l-4 border-l-rose-500 bg-white p-4 shadow-sm transition-colors hover:border-slate-300 hover:bg-slate-50/80 hover:shadow dark:border-slate-600 dark:border-l-rose-600 dark:bg-slate-800 dark:shadow-none dark:hover:border-slate-500 dark:hover:!bg-slate-700 dark:hover:shadow-none">
                    <div class="flex items-start justify-between gap-2">
                        <span class="text-[11px] font-semibold uppercase tracking-wide text-rose-800 dark:text-rose-200/90">{{ t('fail_list', 'Fail list') }}</span>
                        @include('partials.icon', ['name' => 'chevron-right', 'class' => $mpResultsRowChevron])
                    </div>
                    <p class="mt-2 text-2xl font-bold tabular-nums text-rose-700 dark:text-rose-300">{{ $resultsExplorerExamSummary['fail_count'] }}</p>
                    <p class="mt-1 text-xs leading-relaxed text-slate-500 dark:text-slate-400">{{ t('monastery_results_fail_sub', 'Candidates recorded as fail in the published list.') }}</p>
                </a>
                        </div>
        </section>
    @endif

    @if($screen === 'results-home')
        @php
            $passCountAll = $passSanghasAll->count();
            $failCountAll = $failSanghasAll->count();
        @endphp
        <section class="mb-8 space-y-5">
            <div class="rounded-xl border border-slate-200/80 bg-white px-4 py-4 sm:px-5 sm:py-5 dark:border-slate-600/80 dark:bg-slate-800">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <h2 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ t('monastery_results_title', 'Examination results') }}</h2>
                        <p class="mt-1 max-w-xl text-sm leading-relaxed text-slate-600 dark:text-slate-400">{{ t('monastery_results_browse_by_year', 'Browse by year, then level and exam to open pass or fail lists for that session.') }}</p>
                    </div>
                    <div class="flex shrink-0 flex-wrap gap-2">
                        <div class="inline-flex items-center gap-2 rounded-lg border border-emerald-200/80 bg-emerald-500/[0.06] px-3 py-1.5 dark:border-emerald-500/25 dark:bg-emerald-500/15">
                            <span class="text-[10px] font-semibold uppercase tracking-wide text-emerald-800 dark:text-emerald-200">{{ t('pass_list', 'Pass') }}</span>
                            <span class="text-lg font-bold tabular-nums text-emerald-700 dark:text-emerald-300">{{ $passCountAll }}</span>
                        </div>
                        <div class="inline-flex items-center gap-2 rounded-lg border border-rose-200/80 bg-rose-500/[0.06] px-3 py-1.5 dark:border-rose-500/25 dark:bg-rose-500/15">
                            <span class="text-[10px] font-semibold uppercase tracking-wide text-rose-800 dark:text-rose-200">{{ t('fail_list', 'Fail') }}</span>
                            <span class="text-lg font-bold tabular-nums text-rose-700 dark:text-rose-300">{{ $failCountAll }}</span>
                        </div>
                    </div>
                </div>
                @if($resultsPublishedAt)
                    <p class="mt-3 border-t border-slate-100 pt-3 text-xs text-slate-400 dark:border-slate-700 dark:text-slate-500">{{ t('published_at', 'Published at') }}: {{ \Illuminate\Support\Carbon::parse($resultsPublishedAt)->format('M d, Y H:i') }}</p>
                @endif
            </div>

            <div>
                <h3 class="mb-2 text-sm font-medium text-slate-700 dark:text-slate-300">{{ t('results_pick_year', 'Choose a year') }}</h3>
                @if($resultsExplorerYears->isNotEmpty())
                    <div class="overflow-hidden rounded-xl border border-slate-200/80 bg-white dark:border-slate-600/80 dark:bg-slate-800">
                        @foreach($resultsExplorerYears as $yc)
                            <a href="{{ route('monastery.dashboard', ['tab' => 'results', 'screen' => 'results-year', 'results_year' => $yc['year_key']]) }}" class="group flex items-center gap-3 border-b border-slate-100 px-4 py-3.5 last:border-b-0 transition-colors hover:bg-slate-50 dark:border-slate-700/80 dark:hover:!bg-slate-800/90">
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400 dark:text-slate-500">{{ t('exam_year', 'Year') }}</p>
                                    <p class="mt-0.5 text-xl font-semibold tabular-nums text-slate-900 dark:text-slate-100">{{ $yc['year_label'] }}</p>
                                </div>
                                <div class="flex shrink-0 items-center gap-2">
                                    <span class="rounded-md bg-emerald-500/10 px-2 py-0.5 text-xs font-medium tabular-nums text-emerald-800 dark:bg-emerald-500/15 dark:text-emerald-200" title="{{ t('pass_list', 'Pass') }}">{{ $yc['pass_count'] }}</span>
                                    <span class="rounded-md bg-rose-500/10 px-2 py-0.5 text-xs font-medium tabular-nums text-rose-800 dark:bg-rose-500/15 dark:text-rose-200" title="{{ t('fail_list', 'Fail') }}">{{ $yc['fail_count'] }}</span>
                                    <span class="hidden text-xs tabular-nums text-slate-400 sm:inline dark:text-slate-500" title="{{ t('total', 'Total') }}">{{ $yc['total'] }}</span>
                                    @include('partials.icon', ['name' => 'chevron-right', 'class' => $mpResultsRowChevron])
                    </div>
                </a>
                        @endforeach
                    </div>
                @else
                    <p class="rounded-xl border border-slate-200/80 bg-slate-50 px-4 py-5 text-sm text-slate-600 dark:border-slate-600/80 dark:bg-slate-800/90 dark:text-slate-400">{{ t('monastery_results_no_snapshot', 'No published results yet. An administrator must generate the pass list from Scores.') }}</p>
                @endif
            </div>

            <div class="flex flex-col gap-2 border-t border-slate-200/70 pt-4 dark:border-slate-700 sm:flex-row sm:items-center sm:justify-between">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ t('results_quick_links', 'Quick links') }}</span>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('monastery.dashboard', ['tab' => 'results', 'screen' => 'pass']) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-emerald-800 shadow-sm transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-emerald-200 dark:hover:!bg-slate-700">{{ t('view_all_pass', 'All pass') }} <span class="ml-1 tabular-nums opacity-80">({{ $passCountAll }})</span></a>
                    <a href="{{ route('monastery.dashboard', ['tab' => 'results', 'screen' => 'fail']) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-rose-800 shadow-sm transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-rose-200 dark:hover:!bg-slate-700">{{ t('view_all_fail', 'All fail') }} <span class="ml-1 tabular-nums opacity-80">({{ $failCountAll }})</span></a>
                </div>
            </div>
        </section>
    @endif

    @if($screen === 'pass' || $screen === 'fail')
        @php
            $__rex = (int) request('results_exam_id', 0);
            $__ry = request('results_year');
            $__rl = request('results_level');
            $__backResults = ['tab' => 'results', 'screen' => 'results-home'];
            if ($__rex > 0) {
                $__backResults = ['tab' => 'results', 'screen' => 'results-exam', 'results_exam_id' => $__rex];
                if (is_string($__ry) && $__ry !== '') {
                    $__backResults['results_year'] = $__ry;
                }
                if (is_string($__rl) && $__rl !== '') {
                    $__backResults['results_level'] = $__rl;
                }
            }
        @endphp
        <div class="mb-4">
            <a href="{{ route('monastery.dashboard', $__backResults) }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 px-3 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 transition-colors hover:bg-slate-100 dark:hover:!bg-slate-800">
                @include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4'])
                {{ $__rex > 0 ? t('back', 'Back') : t('back_to_results', 'Back to Results') }}
            </a>
        </div>
    @endif

    @if($screen === 'pass')
    <section class="rounded-xl border border-emerald-200/70 dark:border-emerald-800/50 overflow-hidden mb-8">
        <div class="px-4 py-3 border-b border-emerald-100 dark:border-emerald-900/40 bg-emerald-50/60 dark:bg-emerald-900/10">
            <h3 class="text-sm font-semibold text-emerald-800 dark:text-emerald-300 uppercase tracking-wider">{{ t('pass_sangha_list', 'Pass Sangha List') }}</h3>
        </div>
        <div class="mp-portal-passfail-table-wrap bg-white dark:bg-slate-800/50">
            <table class="w-full min-w-0 table-fixed text-xs sm:text-sm">
                <thead class="bg-slate-50 dark:bg-slate-900/40 border-b border-slate-200 dark:border-slate-700">
                    <tr class="text-left text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <th class="px-4 py-3 pr-4">{{ t('no_abbr', 'No.') }}</th>
                        <th class="px-4 py-3 pr-4">{{ t('roll_number', 'Roll Number') }}</th>
                        <th class="px-4 py-3 pr-4">
                            <span class="block">{{ t('desk_number_short', 'Desk No.') }}</span>
                            <span class="block whitespace-nowrap font-normal normal-case text-[10px] leading-tight text-slate-500 dark:text-slate-400">({{ t('exam_roll_number', 'Exam Roll Number') }})</span>
                        </th>
                        <th class="px-4 py-3 pr-4">{{ t('level', 'Level') }}</th>
                        <th class="px-4 py-3 pr-4">{{ t('sanghas', 'Sangha') }}</th>
                        <th class="px-4 py-3 pr-4">{{ t('monastery', 'Monastery') }}</th>
                        <th class="px-4 py-3 pr-4">{{ t('father', 'Father') }}</th>
                        <th class="px-4 py-3 pr-4">{{ t('nrc', 'NRC') }}</th>
                        <th class="px-4 py-3">{{ t('result', 'Result') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($passSanghas as $sangha)
                        @php
                            $levelName = $sangha->level_name ?? $sangha->programme_level ?? null;
                            $rollShow = $sangha->roll_display ?? \App\Support\MonasteryPortalResultsSnapshot::formatRollDisplaySix($sangha->eligible_roll_number ?? null);
                            $deskShow = $sangha->desk_display ?? $sangha->desk_number ?? null;
                            $fatherShow = $sangha->father_name ?? $sangha->latest_score_father_name ?? null;
                            $nrcShow = $sangha->nrc_number ?? $sangha->latest_score_nrc_number ?? null;
                        @endphp
                        <tr>
                            <td class="px-4 py-3 pr-4 text-slate-500 dark:text-slate-400">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 pr-4 font-mono tabular-nums text-slate-700 dark:text-slate-300">{{ $rollShow ?? '—' }}</td>
                            <td class="px-4 py-3 pr-4 font-mono font-semibold tabular-nums text-yellow-700 dark:text-yellow-400">{{ $deskShow ?? '—' }}</td>
                            <td class="px-4 py-3 pr-4 text-slate-700 dark:text-slate-300">{{ $levelName ?? '—' }}</td>
                            <td class="px-4 py-3 pr-4 font-medium text-slate-900 dark:text-slate-100">{{ $sangha->name ?? '—' }}</td>
                            <td class="px-4 py-3 pr-4 text-slate-700 dark:text-slate-300">{{ $sangha->monastery_name ?? '—' }}</td>
                            <td class="px-4 py-3 pr-4 text-slate-700 dark:text-slate-300">{{ $fatherShow ?: '—' }}</td>
                            <td class="px-4 py-3 pr-4 text-slate-700 dark:text-slate-300">{{ $nrcShow ?: '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full bg-emerald-100 dark:bg-emerald-900/40 px-3 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300">{{ t('pass_result', 'Pass') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-sm text-slate-500 dark:text-slate-400">{{ t('monastery_results_list_empty', 'No entries in this list.') }}</td>
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
        <div class="mp-portal-passfail-table-wrap bg-white dark:bg-slate-800/50">
            <table class="w-full min-w-0 table-fixed text-xs sm:text-sm">
                <thead class="bg-slate-50 dark:bg-slate-900/40 border-b border-slate-200 dark:border-slate-700">
                    <tr class="text-left text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <th class="px-4 py-3 pr-4">{{ t('no_abbr', 'No.') }}</th>
                        <th class="px-4 py-3 pr-4">{{ t('roll_number', 'Roll Number') }}</th>
                        <th class="px-4 py-3 pr-4">
                            <span class="block">{{ t('desk_number_short', 'Desk No.') }}</span>
                            <span class="block whitespace-nowrap font-normal normal-case text-[10px] leading-tight text-slate-500 dark:text-slate-400">({{ t('exam_roll_number', 'Exam Roll Number') }})</span>
                        </th>
                        <th class="px-4 py-3 pr-4">{{ t('level', 'Level') }}</th>
                        <th class="px-4 py-3 pr-4">{{ t('sanghas', 'Sangha') }}</th>
                        <th class="px-4 py-3 pr-4">{{ t('monastery', 'Monastery') }}</th>
                        <th class="px-4 py-3 pr-4">{{ t('father', 'Father') }}</th>
                        <th class="px-4 py-3 pr-4">{{ t('nrc', 'NRC') }}</th>
                        <th class="px-4 py-3">{{ t('result', 'Result') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($failSanghas as $sangha)
                        @php
                            $levelNameF = $sangha->level_name ?? $sangha->programme_level ?? null;
                            $rollShowF = $sangha->roll_display ?? \App\Support\MonasteryPortalResultsSnapshot::formatRollDisplaySix($sangha->eligible_roll_number ?? null);
                            $deskShowF = $sangha->desk_display ?? $sangha->desk_number ?? null;
                            $fatherShowF = $sangha->father_name ?? $sangha->latest_score_father_name ?? null;
                            $nrcShowF = $sangha->nrc_number ?? $sangha->latest_score_nrc_number ?? null;
                        @endphp
                        <tr>
                            <td class="px-4 py-3 pr-4 text-slate-500 dark:text-slate-400">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 pr-4 font-mono tabular-nums text-slate-700 dark:text-slate-300">{{ $rollShowF ?? '—' }}</td>
                            <td class="px-4 py-3 pr-4 font-mono font-semibold tabular-nums text-yellow-700 dark:text-yellow-400">{{ $deskShowF ?? '—' }}</td>
                            <td class="px-4 py-3 pr-4 text-slate-700 dark:text-slate-300">{{ $levelNameF ?? '—' }}</td>
                            <td class="px-4 py-3 pr-4 font-medium text-slate-900 dark:text-slate-100">{{ $sangha->name ?? '—' }}</td>
                            <td class="px-4 py-3 pr-4 text-slate-700 dark:text-slate-300">{{ $sangha->monastery_name ?? '—' }}</td>
                            <td class="px-4 py-3 pr-4 text-slate-700 dark:text-slate-300">{{ $fatherShowF ?: '—' }}</td>
                            <td class="px-4 py-3 pr-4 text-slate-700 dark:text-slate-300">{{ $nrcShowF ?: '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full bg-rose-100 dark:bg-rose-900/40 px-3 py-1 text-xs font-semibold text-rose-700 dark:text-rose-300">{{ t('fail_result', 'Fail') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-sm text-slate-500 dark:text-slate-400">{{ t('monastery_results_list_empty', 'No entries in this list.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    @endif
@endif

<nav class="monastery-portal-bottom-nav" role="navigation" aria-label="{{ t('monastery_portal') }}">
    <div class="monastery-portal-bottom-nav__inner">
        <a href="{{ route('monastery.dashboard', ['tab' => 'main', 'screen' => 'main-home']) }}" class="monastery-portal-bottom-nav__tab {{ $tab === 'main' ? 'monastery-portal-bottom-nav__tab--active' : '' }}" @if($tab === 'main') aria-current="page" @endif>{{ t('main', 'Main') }}</a>
        <a href="{{ route('monastery.dashboard', ['tab' => 'results', 'screen' => 'results-home']) }}" class="monastery-portal-bottom-nav__tab {{ $tab === 'results' ? 'monastery-portal-bottom-nav__tab--active' : '' }}" @if($tab === 'results') aria-current="page" @endif>{{ t('results', 'Results') }}</a>
        <!-- <a href="{{ route('monastery.dashboard', ['tab' => 'chat']) }}" class="monastery-portal-bottom-nav__tab {{ $tab === 'chat' ? 'monastery-portal-bottom-nav__tab--active' : '' }}" @if($tab === 'chat') aria-current="page" @endif>{{ t('chat', 'Chat') }}</a> -->
    </div>
</nav>
@endsection
