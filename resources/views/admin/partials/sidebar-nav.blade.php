@php
    $isMonasteries = request()->routeIs('admin.monasteries.*');
    $isSanghas = request()->routeIs('admin.sanghas.*');
    $isExaminations = request()->routeIs('admin.subjects.*', 'admin.exam-types.*', 'admin.exams.*', 'admin.scores.*');
    $isContent = request()->routeIs('admin.websites.*', 'admin.custom-fields.*', 'admin.site-images.*');
    $isAdministration = request()->routeIs('admin.languages.*', 'admin.translations.*', 'admin.roles.*', 'admin.users.*', 'admin.monastery-requests.*');
    $monasteryStatus = $isMonasteries ? request('moderation_status') : null;
    $sanghaStatus = $isSanghas ? request('moderation_status') : null;
@endphp
<nav class="p-4 pt-6 space-y-1 flex-1 overflow-y-auto">
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-slate-700 text-amber-400' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
        @include('partials.icon', ['name' => 'home', 'class' => 'w-5 h-5 shrink-0'])
        {{ t('dashboard') }}
    </a>
    {{-- Monasteries --}}
    <div class="sidebar-group">
        <button type="button" class="sidebar-group-btn flex items-center justify-between w-full gap-3 px-4 py-3 rounded-lg text-white hover:bg-slate-700/50 transition-colors text-left {{ $isMonasteries ? 'bg-slate-700/50' : '' }}" data-toggle="sidebar-group" aria-expanded="{{ $isMonasteries ? 'true' : 'false' }}">
            <span class="flex items-center gap-3">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                {{ t('monasteries') }}
            </span>
            <svg class="sidebar-group-chevron w-5 h-5 text-slate-400 shrink-0 transition-transform {{ $isMonasteries ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
        </button>
        <div class="sidebar-group-content {{ $isMonasteries ? '' : 'hidden' }}">
            <div class="pl-4 pt-1 pb-2 space-y-0.5">
                <a href="{{ route('admin.monasteries.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ $isMonasteries && !$monasteryStatus ? 'bg-slate-700 text-amber-400' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">All</a>
                <a href="{{ route('admin.monasteries.index', ['moderation_status' => 'pending']) }}" class="block px-3 py-2 rounded-lg text-sm {{ $isMonasteries && $monasteryStatus === 'pending' ? 'bg-slate-700 text-amber-400' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">Pending</a>
                <a href="{{ route('admin.monasteries.index', ['moderation_status' => 'approved']) }}" class="block px-3 py-2 rounded-lg text-sm {{ $isMonasteries && $monasteryStatus === 'approved' ? 'bg-slate-700 text-amber-400' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">Approved</a>
                <a href="{{ route('admin.monasteries.index', ['moderation_status' => 'rejected']) }}" class="block px-3 py-2 rounded-lg text-sm {{ $isMonasteries && $monasteryStatus === 'rejected' ? 'bg-slate-700 text-amber-400' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">Rejected</a>
            </div>
        </div>
    </div>

    {{-- Sanghas --}}
    <div class="sidebar-group">
        <button type="button" class="sidebar-group-btn flex items-center justify-between w-full gap-3 px-4 py-3 rounded-lg text-white hover:bg-slate-700/50 transition-colors text-left {{ $isSanghas ? 'bg-slate-700/50' : '' }}" data-toggle="sidebar-group" aria-expanded="{{ $isSanghas ? 'true' : 'false' }}">
            <span class="flex items-center gap-3">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                {{ t('sanghas') }}
            </span>
            <svg class="sidebar-group-chevron w-5 h-5 text-slate-400 shrink-0 transition-transform {{ $isSanghas ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
        </button>
        <div class="sidebar-group-content {{ $isSanghas ? '' : 'hidden' }}">
            <div class="pl-4 pt-1 pb-2 space-y-0.5">
                <a href="{{ route('admin.sanghas.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ $isSanghas && !$sanghaStatus ? 'bg-slate-700 text-amber-400' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">All</a>
                <a href="{{ route('admin.sanghas.index', ['moderation_status' => 'pending']) }}" class="block px-3 py-2 rounded-lg text-sm {{ $isSanghas && $sanghaStatus === 'pending' ? 'bg-slate-700 text-amber-400' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">Pending</a>
                <a href="{{ route('admin.sanghas.index', ['moderation_status' => 'approved']) }}" class="block px-3 py-2 rounded-lg text-sm {{ $isSanghas && $sanghaStatus === 'approved' ? 'bg-slate-700 text-amber-400' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">Approved</a>
                <a href="{{ route('admin.sanghas.index', ['moderation_status' => 'rejected']) }}" class="block px-3 py-2 rounded-lg text-sm {{ $isSanghas && $sanghaStatus === 'rejected' ? 'bg-slate-700 text-amber-400' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">Rejected</a>
            </div>
        </div>
    </div>

    {{-- Examinations --}}
    <div class="sidebar-group">
        <button type="button" class="sidebar-group-btn flex items-center justify-between w-full gap-3 px-4 py-3 rounded-lg text-white hover:bg-slate-700/50 transition-colors text-left {{ $isExaminations ? 'bg-slate-700/50' : '' }}" data-toggle="sidebar-group" aria-expanded="{{ $isExaminations ? 'true' : 'false' }}">
            <span class="flex items-center gap-3">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                {{ t('menu_examinations') }}
            </span>
            <svg class="sidebar-group-chevron w-5 h-5 text-slate-400 shrink-0 transition-transform {{ $isExaminations ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
        </button>
        <div class="sidebar-group-content {{ $isExaminations ? '' : 'hidden' }}">
            <div class="pl-4 pt-1 pb-2 space-y-0.5">
                <a href="{{ route('admin.subjects.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.subjects.*') ? 'bg-slate-700 text-amber-400' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    {{ t('subjects') }}
                </a>
                <a href="{{ route('admin.exam-types.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.exam-types.*') ? 'bg-slate-700 text-amber-400' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z" /></svg>
                    {{ t('exam_types') }}
                </a>
                <a href="{{ route('admin.exams.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.exams.*') ? 'bg-slate-700 text-amber-400' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                    {{ t('exams') }}
                </a>
                <a href="{{ route('admin.scores.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.scores.*') ? 'bg-slate-700 text-amber-400' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    {{ t('scores') }}
                </a>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="sidebar-group">
        <button type="button" class="sidebar-group-btn flex items-center justify-between w-full gap-3 px-4 py-3 rounded-lg text-white hover:bg-slate-700/50 transition-colors text-left {{ $isContent ? 'bg-slate-700/50' : '' }}" data-toggle="sidebar-group" aria-expanded="{{ $isContent ? 'true' : 'false' }}">
            <span class="flex items-center gap-3">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>
                {{ t('menu_content') }}
            </span>
            <svg class="sidebar-group-chevron w-5 h-5 text-slate-400 shrink-0 transition-transform {{ $isContent ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
        </button>
        <div class="sidebar-group-content {{ $isContent ? '' : 'hidden' }}">
            <div class="pl-4 pt-1 pb-2 space-y-0.5">
                <a href="{{ route('admin.websites.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.websites.*') ? 'bg-slate-700 text-amber-400' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                    {{ t('website') }}
                </a>
                <a href="{{ route('admin.custom-fields.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.custom-fields.*') ? 'bg-slate-700 text-amber-400' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>
                    {{ t('custom_fields') }}
                </a>
                <a href="{{ route('admin.site-images.edit') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.site-images.*') ? 'bg-slate-700 text-amber-400' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    {{ t('site_images') }}
                </a>
            </div>
        </div>
    </div>

    {{-- Administration --}}
    <div class="sidebar-group">
        <button type="button" class="sidebar-group-btn flex items-center justify-between w-full gap-3 px-4 py-3 rounded-lg text-white hover:bg-slate-700/50 transition-colors text-left {{ $isAdministration ? 'bg-slate-700/50' : '' }}" data-toggle="sidebar-group" aria-expanded="{{ $isAdministration ? 'true' : 'false' }}">
            <span class="flex items-center gap-3">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                {{ t('menu_administration') }}
            </span>
            <svg class="sidebar-group-chevron w-5 h-5 text-slate-400 shrink-0 transition-transform {{ $isAdministration ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
        </button>
        <div class="sidebar-group-content {{ $isAdministration ? '' : 'hidden' }}">
            <div class="pl-4 pt-1 pb-2 space-y-0.5">
                <a href="{{ route('admin.languages.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.languages.*', 'admin.translations.*') ? 'bg-slate-700 text-amber-400' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" /></svg>
                    {{ t('languages') }}
                </a>
                <a href="{{ route('admin.roles.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.roles.*') ? 'bg-slate-700 text-amber-400' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    {{ t('roles') }}
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.users.*') ? 'bg-slate-700 text-amber-400' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    {{ t('users') }}
                </a>
                <a href="{{ route('admin.monastery-requests.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.monastery-requests.*') ? 'bg-slate-700 text-amber-400' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h6m-9 8h16a2 2 0 002-2V6a2 2 0 00-2-2H4a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    Monastery Requests
                </a>
            </div>
        </div>
    </div>
</nav>
<script>
(function() {
    document.querySelectorAll('.sidebar-group-btn[data-toggle="sidebar-group"]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var group = btn.closest('.sidebar-group');
            var content = group.querySelector('.sidebar-group-content');
            var chevron = group.querySelector('.sidebar-group-chevron');
            var isOpen = !content.classList.contains('hidden');
            content.classList.toggle('hidden', isOpen);
            chevron.classList.toggle('rotate-180', !isOpen);
            btn.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        });
    });
})();
</script>
