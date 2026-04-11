@php
    $isMonasteries = request()->routeIs('admin.monasteries.*');
    $isSanghas = request()->routeIs('admin.sanghas.*');
    $isMandatoryScores = request()->routeIs('admin.mandatory-scores.*');
    $isExaminations = request()->routeIs('admin.subjects.*', 'admin.exam-types.*', 'admin.exams.*', 'admin.scores.*');
    $isContent = request()->routeIs('admin.websites.*', 'admin.custom-fields.*', 'admin.site-images.*', 'admin.appearance.*');
    $isAdministration = request()->routeIs('admin.languages.*', 'admin.translations.*', 'admin.roles.*', 'admin.users.*');
    $isMonasteryRequests = request()->routeIs('admin.monastery-requests.*');
    $requestFormScope = request('form_scope', 'general');
    if (! in_array($requestFormScope, ['general', 'exam'], true)) {
        $requestFormScope = 'general';
    }
    $monasteryModerationStatus = $isMonasteries ? request('moderation_status') : null;
    $monasteryRequestStatus = $isMonasteryRequests ? request('request_status') : null;
    $sanghaStatus = $isSanghas ? request('moderation_status') : null;
    $mrGeneralActive = $isMonasteryRequests && $requestFormScope === 'general';
    $mrExamActive = $isMonasteryRequests && $requestFormScope === 'exam';
    $examTypesSidebar = \App\Models\ExamType::query()
        ->whereIn('name', \App\Models\ExamType::CANONICAL_NAME_ORDER)
        ->orderByCanonical()
        ->get();
@endphp
<nav class="admin-sidebar-nav flex flex-1 flex-col gap-0.5 overflow-y-auto overflow-x-hidden p-2 pb-4">
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-700 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">
        @include('partials.icon', ['name' => 'view-grid', 'class' => 'h-5 w-5 shrink-0 text-amber-600 opacity-90 dark:text-amber-400'])
        <span class="admin-sidebar-label truncate">{{ t('dashboard') }}</span>
    </a>
    {{-- Monasteries --}}
    <div class="sidebar-group">
        <button type="button" class="sidebar-group-btn flex w-full items-center justify-between gap-2 rounded-lg border border-transparent px-3 py-2.5 text-left text-sm font-medium text-stone-800 transition-colors duration-200 hover:border-stone-200/80 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-200 dark:hover:border-white/5 dark:hover:bg-white/5 dark:hover:text-amber-200 {{ $isMonasteries ? 'border-stone-200/80 bg-stone-100/90 text-amber-900 dark:border-white/10 dark:bg-white/10 dark:text-amber-200' : '' }}" data-toggle="sidebar-group" aria-expanded="{{ $isMonasteries ? 'true' : 'false' }}">
            <span class="flex min-w-0 flex-1 items-center gap-3">
                <svg class="h-5 w-5 shrink-0 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                <span class="admin-sidebar-label truncate">{{ t('monasteries') }}</span>
            </span>
            <svg class="sidebar-group-chevron h-5 w-5 shrink-0 text-stone-400 transition-transform duration-200 ease-out dark:text-slate-500 {{ $isMonasteries ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
        </button>
        <div class="sidebar-group-content {{ $isMonasteries ? 'is-expanded' : '' }}">
            <div class="sidebar-group-inner">
            <div class="pl-4 pt-1 pb-2 space-y-0.5">
                @php
                    $moAll = $isMonasteries && ! request()->filled('moderation_status');
                @endphp
                <a href="{{ route('admin.monasteries.index') }}" class="block px-3 py-2 rounded-lg text-sm transition-colors duration-200 {{ $moAll ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">{{ t('all', 'All') }}</a>
                <a href="{{ route('admin.monasteries.index', ['moderation_status' => 'pending']) }}" class="block px-3 py-2 rounded-lg text-sm transition-colors duration-200 {{ $isMonasteries && $monasteryModerationStatus === 'pending' ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">{{ t('status_pending', 'Pending') }}</a>
                <a href="{{ route('admin.monasteries.index', ['moderation_status' => 'approved']) }}" class="block px-3 py-2 rounded-lg text-sm transition-colors duration-200 {{ $isMonasteries && $monasteryModerationStatus === 'approved' ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">{{ t('status_approved', 'Approved') }}</a>
                <a href="{{ route('admin.monasteries.index', ['moderation_status' => 'rejected']) }}" class="block px-3 py-2 rounded-lg text-sm transition-colors duration-200 {{ $isMonasteries && $monasteryModerationStatus === 'rejected' ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">{{ t('status_rejected', 'Rejected') }}</a>
            </div>
            </div>
        </div>
    </div>

    {{-- Sanghas --}}
    <div class="sidebar-group">
        <button type="button" class="sidebar-group-btn flex w-full items-center justify-between gap-2 rounded-lg border border-transparent px-3 py-2.5 text-left text-sm font-medium text-stone-800 transition-colors duration-200 hover:border-stone-200/80 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-200 dark:hover:border-white/5 dark:hover:bg-white/5 dark:hover:text-amber-200 {{ $isSanghas ? 'border-stone-200/80 bg-stone-100/90 text-amber-900 dark:border-white/10 dark:bg-white/10 dark:text-amber-200' : '' }}" data-toggle="sidebar-group" aria-expanded="{{ $isSanghas ? 'true' : 'false' }}">
            <span class="flex min-w-0 flex-1 items-center gap-3">
                <svg class="h-5 w-5 shrink-0 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                <span class="admin-sidebar-label truncate">{{ t('sanghas') }}</span>
            </span>
            <svg class="sidebar-group-chevron h-5 w-5 shrink-0 text-stone-400 transition-transform duration-200 ease-out dark:text-slate-500 {{ $isSanghas ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
        </button>
        <div class="sidebar-group-content {{ $isSanghas ? 'is-expanded' : '' }}">
            <div class="sidebar-group-inner">
            <div class="pl-4 pt-1 pb-2 space-y-0.5">
                <a href="{{ route('admin.sanghas.index') }}" class="block px-3 py-2 rounded-lg text-sm transition-colors duration-200 {{ $isSanghas && ! $sanghaStatus ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">{{ t('all', 'All') }}</a>
                <a href="{{ route('admin.sanghas.index', ['moderation_status' => 'pending']) }}" class="block px-3 py-2 rounded-lg text-sm transition-colors duration-200 {{ $isSanghas && $sanghaStatus === 'pending' ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">{{ t('status_pending', 'Pending') }}</a>
                <a href="{{ route('admin.sanghas.index', ['moderation_status' => 'approved']) }}" class="block px-3 py-2 rounded-lg text-sm transition-colors duration-200 {{ $isSanghas && $sanghaStatus === 'approved' ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">{{ t('status_approved', 'Approved') }}</a>
                <a href="{{ route('admin.sanghas.index', ['moderation_status' => 'rejected']) }}" class="block px-3 py-2 rounded-lg text-sm transition-colors duration-200 {{ $isSanghas && $sanghaStatus === 'rejected' ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">{{ t('status_rejected', 'Rejected') }}</a>
            </div>
            </div>
        </div>
    </div>

    {{-- Monastery Requests (general portal) --}}
    <div class="sidebar-group">
        <button type="button" class="sidebar-group-btn flex w-full items-center justify-between gap-2 rounded-lg border border-transparent px-3 py-2.5 text-left text-sm font-medium text-stone-800 transition-colors duration-200 hover:border-stone-200/80 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-200 dark:hover:border-white/5 dark:hover:bg-white/5 dark:hover:text-amber-200 {{ $mrGeneralActive ? 'border-stone-200/80 bg-stone-100/90 text-amber-900 dark:border-white/10 dark:bg-white/10 dark:text-amber-200' : '' }}" data-toggle="sidebar-group" aria-expanded="{{ $mrGeneralActive ? 'true' : 'false' }}">
            <span class="flex min-w-0 flex-1 items-center gap-3">
                <svg class="h-5 w-5 shrink-0 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                <span class="admin-sidebar-label truncate">{{ t('monastery_requests', 'Transfer Sangha') }}</span>
            </span>
            <svg class="sidebar-group-chevron h-5 w-5 shrink-0 text-stone-400 transition-transform duration-200 ease-out dark:text-slate-500 {{ $mrGeneralActive ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
        </button>
        <div class="sidebar-group-content {{ $mrGeneralActive ? 'is-expanded' : '' }}">
            <div class="sidebar-group-inner">
            <div class="pl-4 pt-1 pb-2 space-y-0.5">
                @php
                    $mrAll = $mrGeneralActive && (! request()->filled('request_status') || request('request_status') === 'all');
                @endphp
                <a href="{{ route('admin.monastery-requests.index', ['form_scope' => 'general']) }}" class="block px-3 py-2 rounded-lg text-sm transition-colors duration-200 {{ $mrAll ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">{{ t('all', 'All') }}</a>
                <a href="{{ route('admin.monastery-requests.index', ['form_scope' => 'general', 'request_status' => 'pending']) }}" class="block px-3 py-2 rounded-lg text-sm transition-colors duration-200 {{ $mrGeneralActive && $monasteryRequestStatus === 'pending' ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">{{ t('status_pending', 'Pending') }}</a>
                <a href="{{ route('admin.monastery-requests.index', ['form_scope' => 'general', 'request_status' => 'approved']) }}" class="block px-3 py-2 rounded-lg text-sm transition-colors duration-200 {{ $mrGeneralActive && $monasteryRequestStatus === 'approved' ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">{{ t('status_approved', 'Approved') }}</a>
                <a href="{{ route('admin.monastery-requests.index', ['form_scope' => 'general', 'request_status' => 'rejected']) }}" class="block px-3 py-2 rounded-lg text-sm transition-colors duration-200 {{ $mrGeneralActive && $monasteryRequestStatus === 'rejected' ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">{{ t('status_rejected', 'Rejected') }}</a>
            </div>
            </div>
        </div>
    </div>

    {{-- Exam form uploads (per programme) --}}
    <div class="sidebar-group">
        <button type="button" class="sidebar-group-btn flex w-full items-center justify-between gap-2 rounded-lg border border-transparent px-3 py-2.5 text-left text-sm font-medium text-stone-800 transition-colors duration-200 hover:border-stone-200/80 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-200 dark:hover:border-white/5 dark:hover:bg-white/5 dark:hover:text-amber-200 {{ $mrExamActive ? 'border-stone-200/80 bg-stone-100/90 text-amber-900 dark:border-white/10 dark:bg-white/10 dark:text-amber-200' : '' }}" data-toggle="sidebar-group" aria-expanded="{{ $mrExamActive ? 'true' : 'false' }}">
            <span class="flex min-w-0 flex-1 items-center gap-3">
                <svg class="h-5 w-5 shrink-0 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                <span class="admin-sidebar-label truncate">{{ t('admin_exam_form_submissions', 'Exam Form') }}</span>
            </span>
            <svg class="sidebar-group-chevron h-5 w-5 shrink-0 text-stone-400 transition-transform duration-200 ease-out dark:text-slate-500 {{ $mrExamActive ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
        </button>
        <div class="sidebar-group-content {{ $mrExamActive ? 'is-expanded' : '' }}">
            <div class="sidebar-group-inner">
            <div class="pl-4 pt-1 pb-2 space-y-0.5">
                @php
                    $mrExamAll = $mrExamActive && (! request()->filled('request_status') || request('request_status') === 'all');
                @endphp
                <a href="{{ route('admin.monastery-requests.index', ['form_scope' => 'exam']) }}" class="block px-3 py-2 rounded-lg text-sm transition-colors duration-200 {{ $mrExamAll ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">{{ t('all', 'All') }}</a>
                <a href="{{ route('admin.monastery-requests.index', ['form_scope' => 'exam', 'request_status' => 'pending']) }}" class="block px-3 py-2 rounded-lg text-sm transition-colors duration-200 {{ $mrExamActive && $monasteryRequestStatus === 'pending' ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">{{ t('status_pending', 'Pending') }}</a>
                <a href="{{ route('admin.monastery-requests.index', ['form_scope' => 'exam', 'request_status' => 'approved']) }}" class="block px-3 py-2 rounded-lg text-sm transition-colors duration-200 {{ $mrExamActive && $monasteryRequestStatus === 'approved' ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">{{ t('status_approved', 'Approved') }}</a>
                <a href="{{ route('admin.monastery-requests.index', ['form_scope' => 'exam', 'request_status' => 'rejected']) }}" class="block px-3 py-2 rounded-lg text-sm transition-colors duration-200 {{ $mrExamActive && $monasteryRequestStatus === 'rejected' ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">{{ t('status_rejected', 'Rejected') }}</a>
                <p class="px-3 pt-2 text-[10px] font-semibold uppercase tracking-wider text-stone-400 dark:text-slate-500">{{ t('exam_programme', 'Programme') }}</p>
                @foreach($examTypesSidebar as $etNav)
                    @php $etActive = $mrExamActive && (int) request('exam_type_id') === (int) $etNav->id; @endphp
                    @php
                        $etNavParams = ['form_scope' => 'exam', 'exam_type_id' => $etNav->id];
                        if (in_array(request('request_status'), ['pending', 'approved', 'rejected'], true)) {
                            $etNavParams['request_status'] = request('request_status');
                        }
                    @endphp
                    <a href="{{ route('admin.monastery-requests.index', $etNavParams) }}" class="block px-3 py-1.5 rounded-lg text-xs transition-colors duration-200 {{ $etActive ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/12 dark:text-amber-100 dark:ring-amber-400/35' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">{{ $etNav->name }}</a>
                @endforeach
            </div>
            </div>
        </div>
    </div>

    {{-- Mandatory score entry --}}
    <div class="sidebar-group">
        <button type="button" class="sidebar-group-btn flex w-full items-center justify-between gap-2 rounded-lg border border-transparent px-3 py-2.5 text-left text-sm font-medium text-stone-800 transition-colors duration-200 hover:border-stone-200/80 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-200 dark:hover:border-white/5 dark:hover:bg-white/5 dark:hover:text-amber-200 {{ $isMandatoryScores ? 'border-stone-200/80 bg-stone-100/90 text-amber-900 dark:border-white/10 dark:bg-white/10 dark:text-amber-200' : '' }}" data-toggle="sidebar-group" aria-expanded="{{ $isMandatoryScores ? 'true' : 'false' }}">
            <span class="flex min-w-0 flex-1 items-center gap-3">
                <svg class="h-5 w-5 shrink-0 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                <span class="admin-sidebar-label truncate">{{ t('menu_mandatory_score_entry', 'Score entry') }}</span>
            </span>
            <svg class="sidebar-group-chevron h-5 w-5 shrink-0 text-stone-400 transition-transform duration-200 ease-out dark:text-slate-500 {{ $isMandatoryScores ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
        </button>
        <div class="sidebar-group-content {{ $isMandatoryScores ? 'is-expanded' : '' }}">
            <div class="sidebar-group-inner">
            <div class="pl-4 pt-1 pb-2 space-y-0.5">
                <a href="{{ route('admin.mandatory-scores.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-colors duration-200 {{ request()->routeIs('admin.mandatory-scores.index') || request()->routeIs('admin.mandatory-scores.store') ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">
                    <svg class="w-4 h-4 shrink-0 text-amber-600/90 dark:text-amber-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                    {{ t('mandatory_scores_nav_entry', 'Enter By Exam Score') }}
                </a>
                <a href="{{ route('admin.mandatory-scores.grid') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-colors duration-200 {{ request()->routeIs('admin.mandatory-scores.grid') || request()->routeIs('admin.mandatory-scores.grid-row') ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">
                    <svg class="w-4 h-4 shrink-0 text-amber-600/90 dark:text-amber-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                    {{ t('mandatory_scores_nav_grid', 'Exam Desk Score Grid') }}
                </a>
            </div>
            </div>
        </div>
    </div>

    {{-- Examinations --}}
    <div class="sidebar-group">
        <button type="button" class="sidebar-group-btn flex w-full items-center justify-between gap-2 rounded-lg border border-transparent px-3 py-2.5 text-left text-sm font-medium text-stone-800 transition-colors duration-200 hover:border-stone-200/80 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-200 dark:hover:border-white/5 dark:hover:bg-white/5 dark:hover:text-amber-200 {{ $isExaminations ? 'border-stone-200/80 bg-stone-100/90 text-amber-900 dark:border-white/10 dark:bg-white/10 dark:text-amber-200' : '' }}" data-toggle="sidebar-group" aria-expanded="{{ $isExaminations ? 'true' : 'false' }}">
            <span class="flex min-w-0 flex-1 items-center gap-3">
                <svg class="h-5 w-5 shrink-0 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                <span class="admin-sidebar-label truncate">{{ t('menu_examinations') }}</span>
            </span>
            <svg class="sidebar-group-chevron h-5 w-5 shrink-0 text-stone-400 transition-transform duration-200 ease-out dark:text-slate-500 {{ $isExaminations ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
        </button>
        <div class="sidebar-group-content {{ $isExaminations ? 'is-expanded' : '' }}">
            <div class="sidebar-group-inner">
            <div class="pl-4 pt-1 pb-2 space-y-0.5">
                <a href="{{ route('admin.subjects.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-colors duration-200 {{ request()->routeIs('admin.subjects.*') ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">
                    <svg class="w-4 h-4 shrink-0 text-amber-600/90 dark:text-amber-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    {{ t('subjects') }}
                </a>
                <a href="{{ route('admin.exam-types.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-colors duration-200 {{ request()->routeIs('admin.exam-types.*') ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">
                    <svg class="w-4 h-4 shrink-0 text-amber-600/90 dark:text-amber-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z" /></svg>
                    {{ t('exam_types') }}
                </a>
                <a href="{{ route('admin.exams.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-colors duration-200 {{ request()->routeIs('admin.exams.*') ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">
                    <svg class="w-4 h-4 shrink-0 text-amber-600/90 dark:text-amber-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                    {{ t('exams') }}
                </a>
                <a href="{{ route('admin.scores.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-colors duration-200 {{ request()->routeIs('admin.scores.*') ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">
                    <svg class="w-4 h-4 shrink-0 text-amber-600/90 dark:text-amber-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    {{ t('scores') }}
                </a>
            </div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="sidebar-group">
        <button type="button" class="sidebar-group-btn flex w-full items-center justify-between gap-2 rounded-lg border border-transparent px-3 py-2.5 text-left text-sm font-medium text-stone-800 transition-colors duration-200 hover:border-stone-200/80 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-200 dark:hover:border-white/5 dark:hover:bg-white/5 dark:hover:text-amber-200 {{ $isContent ? 'border-stone-200/80 bg-stone-100/90 text-amber-900 dark:border-white/10 dark:bg-white/10 dark:text-amber-200' : '' }}" data-toggle="sidebar-group" aria-expanded="{{ $isContent ? 'true' : 'false' }}">
            <span class="flex min-w-0 flex-1 items-center gap-3">
                <svg class="h-5 w-5 shrink-0 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>
                <span class="admin-sidebar-label truncate">{{ t('menu_content') }}</span>
            </span>
            <svg class="sidebar-group-chevron h-5 w-5 shrink-0 text-stone-400 transition-transform duration-200 ease-out dark:text-slate-500 {{ $isContent ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
        </button>
        <div class="sidebar-group-content {{ $isContent ? 'is-expanded' : '' }}">
            <div class="sidebar-group-inner">
            <div class="pl-4 pt-1 pb-2 space-y-0.5">
                <a href="{{ route('admin.websites.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-colors duration-200 {{ request()->routeIs('admin.websites.*') ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">
                    <svg class="w-4 h-4 shrink-0 text-amber-600/90 dark:text-amber-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                    {{ t('website') }}
                </a>
                <a href="{{ route('admin.custom-fields.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-colors duration-200 {{ request()->routeIs('admin.custom-fields.*') ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">
                    <svg class="w-4 h-4 shrink-0 text-amber-600/90 dark:text-amber-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>
                    {{ t('custom_fields') }}
                </a>
                <a href="{{ route('admin.site-images.edit') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-colors duration-200 {{ request()->routeIs('admin.site-images.*') ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">
                    <svg class="w-4 h-4 shrink-0 text-amber-600/90 dark:text-amber-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    {{ t('site_images') }}
                </a>
                <a href="{{ route('admin.appearance.edit') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-colors duration-200 {{ request()->routeIs('admin.appearance.*') ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">
                    <svg class="w-4 h-4 shrink-0 text-amber-600/90 dark:text-amber-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></svg>
                    {{ t('menu_appearance_colors', 'Colors') }}
                </a>
            </div>
            </div>
        </div>
    </div>

    {{-- Administration --}}
    <div class="sidebar-group">
        <button type="button" class="sidebar-group-btn flex w-full items-center justify-between gap-2 rounded-lg border border-transparent px-3 py-2.5 text-left text-sm font-medium text-stone-800 transition-colors duration-200 hover:border-stone-200/80 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-200 dark:hover:border-white/5 dark:hover:bg-white/5 dark:hover:text-amber-200 {{ $isAdministration ? 'border-stone-200/80 bg-stone-100/90 text-amber-900 dark:border-white/10 dark:bg-white/10 dark:text-amber-200' : '' }}" data-toggle="sidebar-group" aria-expanded="{{ $isAdministration ? 'true' : 'false' }}">
            <span class="flex min-w-0 flex-1 items-center gap-3">
                <svg class="h-5 w-5 shrink-0 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                <span class="admin-sidebar-label truncate">{{ t('menu_administration') }}</span>
            </span>
            <svg class="sidebar-group-chevron h-5 w-5 shrink-0 text-stone-400 transition-transform duration-200 ease-out dark:text-slate-500 {{ $isAdministration ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
        </button>
        <div class="sidebar-group-content {{ $isAdministration ? 'is-expanded' : '' }}">
            <div class="sidebar-group-inner">
            <div class="pl-4 pt-1 pb-2 space-y-0.5">
                <a href="{{ route('admin.languages.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-colors duration-200 {{ request()->routeIs('admin.languages.*', 'admin.translations.*') ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">
                    <svg class="w-4 h-4 shrink-0 text-amber-600/90 dark:text-amber-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" /></svg>
                    {{ t('languages') }}
                </a>
                <a href="{{ route('admin.roles.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-colors duration-200 {{ request()->routeIs('admin.roles.*') ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">
                    <svg class="w-4 h-4 shrink-0 text-amber-600/90 dark:text-amber-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    {{ t('roles') }}
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-colors duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-amber-500/15 text-amber-900 ring-1 ring-amber-500/25 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-amber-200' }}">
                    <svg class="w-4 h-4 shrink-0 text-amber-600/90 dark:text-amber-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    {{ t('users') }}
                </a>
            </div>
            </div>
        </div>
    </div>
</nav>
<script>
(function() {
    var nav = document.querySelector('.admin-sidebar-nav');
    if (!nav) return;
    nav.querySelectorAll('.sidebar-group-btn[data-toggle="sidebar-group"]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var group = btn.closest('.sidebar-group');
            var content = group && group.querySelector('.sidebar-group-content');
            if (!content) return;
            var wasExpanded = content.classList.contains('is-expanded');
            var openThis = !wasExpanded;
            nav.querySelectorAll('.sidebar-group').forEach(function(g) {
                var c = g.querySelector('.sidebar-group-content');
                var ch = g.querySelector('.sidebar-group-chevron');
                var b = g.querySelector('.sidebar-group-btn');
                if (!c || !ch || !b) return;
                var expand = g === group ? openThis : false;
                c.classList.toggle('is-expanded', expand);
                ch.classList.toggle('rotate-180', expand);
                b.setAttribute('aria-expanded', expand ? 'true' : 'false');
            });
        });
    });
})();
</script>
