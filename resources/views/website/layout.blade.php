@php
    $websiteTheme = resolved_app_theme();
    $navPages = \App\Models\Website::where('type', 'page')->where('is_published', true)->orderBy('sort_order')->orderBy('title')->get();

    $makeNavLink = function (object $navPage): array {
        $isRegistration = $navPage->slug === 'registration';
        $url = $isRegistration
            ? route('website.login', ['type' => 'monastery', 'mode' => 'register'])
            : route('website.page', $navPage->slug);
        $active = $isRegistration
            ? (request()->routeIs('website.login') && request('mode') === 'register')
            : (request()->routeIs('website.page') && (request()->route('slug') ?? '') === $navPage->slug);

        return [
            'title' => $navPage->title,
            'slug' => $navPage->slug,
            'url' => $url,
            'active' => $active,
            'sort_order' => (int) $navPage->sort_order,
        ];
    };

    $examSlugs = ['exam-schedule', 'results', 'syllabus', 'past-papers', 'guidelines'];
    $aboutSlugs = ['about', 'contact', 'news', 'events', 'gallery'];
    $helpSlugs = ['faq', 'privacy', 'terms-of-use', 'accessibility'];

    $navExams = collect();
    $navAbout = collect();
    $navHelp = collect();
    $navMore = collect();

    foreach ($navPages as $navPage) {
        if (in_array($navPage->slug, ['home', 'login'], true) || $navPage->slug === 'registration') {
            continue;
        }
        $link = $makeNavLink($navPage);
        if (in_array($navPage->slug, $examSlugs, true)) {
            $navExams->push($link);
        } elseif (in_array($navPage->slug, $aboutSlugs, true)) {
            $navAbout->push($link);
        } elseif (in_array($navPage->slug, $helpSlugs, true)) {
            $navHelp->push($link);
        } else {
            $navMore->push($link);
        }
    }

    $navExams->push([
        'title' => t('pass_sangha_list', 'Pass Sangha List'),
        'slug' => 'pass-sanghas',
        'url' => route('website.pass-sanghas'),
        'active' => request()->routeIs('website.pass-sanghas'),
        'sort_order' => 100,
    ]);

    $navExams = $navExams->sortBy('sort_order')->values();
    $navAbout = $navAbout->sortBy('sort_order')->values();
    $navHelp = $navHelp->sortBy('sort_order')->values();
    $navMore = $navMore->sortBy('sort_order')->values();

    $navGroups = array_values(array_filter([
        ['label' => t('menu_examinations'), 'items' => $navExams],
        ['label' => t('menu_about', 'About'), 'items' => $navAbout],
        ['label' => t('menu_help', 'Help'), 'items' => $navHelp],
        ['label' => t('menu_more', 'More'), 'items' => $navMore],
    ], fn ($g) => $g['items']->isNotEmpty()));

    $footerQuickOrder = ['exam-schedule', 'results', 'guidelines', 'about', 'contact', 'faq', 'privacy'];
    $footerQuickLinks = collect([
        ['title' => t('home'), 'url' => route('website.home')],
    ]);
    foreach ($footerQuickOrder as $fs) {
        $fp = $navPages->firstWhere('slug', $fs);
        if ($fp) {
            $footerQuickLinks->push(['title' => $fp->title, 'url' => route('website.page', $fp->slug)]);
        }
    }
    $footerQuickLinks->push(['title' => t('pass_sangha_list', 'Pass Sangha List'), 'url' => route('website.pass-sanghas')]);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ $websiteTheme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', t('sangha_exam')) - {{ config('app.name', 'Sangha Exam') }}</title>
    @php $favicon = \App\Models\SiteSetting::imageUrl('favicon'); $ogImage = \App\Models\SiteSetting::imageUrl('og_image'); @endphp
    @if($favicon)<link rel="icon" href="{{ $favicon }}" type="image/x-icon">@endif
    @if($ogImage)<meta property="og:image" content="{{ url($ogImage) }}">@endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:400,500,600,700|instrument-sans:400,500,600" rel="stylesheet" />
    <script>
        (function() {
            function applyWebsiteTheme() {
                var theme = document.documentElement.getAttribute('data-theme') || 'system';
                var dark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                document.documentElement.classList.toggle('dark', dark);
            }
            window.sanghaApplyWebsiteTheme = applyWebsiteTheme;
            window.sanghaSetWebsiteTheme = function(theme) {
                if (theme !== 'light' && theme !== 'dark' && theme !== 'system') return;
                document.documentElement.setAttribute('data-theme', theme);
                applyWebsiteTheme();
            };
            applyWebsiteTheme();
            try {
                var mq = window.matchMedia('(prefers-color-scheme: dark)');
                var onChange = function () {
                    var t = document.documentElement.getAttribute('data-theme') || 'system';
                    if (t === 'system') applyWebsiteTheme();
                };
                if (mq.addEventListener) mq.addEventListener('change', onChange);
                else if (mq.addListener) mq.addListener(onChange);
            } catch (e) {}
        })();
    </script>
    <style>
        .font-heading { font-family: 'Cormorant Garamond', ui-serif, Georgia, serif; }
        /* Clip horizontal scroll without trapping position:fixed overlays (mobile Safari). */
        html { overflow-x: clip; }
        /* Solid panel behind menu text — avoids transparent drawer over hero on some compositors. */
        .website-mobile-drawer { background-color: #ffffff; }
        .dark .website-mobile-drawer { background-color: #020617; }
    </style>
</head>
<body class="min-h-screen bg-stone-50 dark:bg-slate-950 text-stone-900 dark:text-slate-100 font-sans antialiased" data-app-locale-url="{{ route('app.set-locale') }}" data-app-theme-url="{{ route('app.set-theme') }}">
    <div class="fixed inset-0 -z-10 pointer-events-none bg-[radial-gradient(ellipse_68%_46%_at_50%_-10%,rgba(251,191,36,0.14),transparent)] dark:bg-[radial-gradient(ellipse_68%_46%_at_50%_-10%,rgba(251,191,36,0.08),transparent)]"></div>
    <header class="sticky top-0 z-[120] border-b border-stone-200/70 dark:border-slate-800/80 bg-white dark:bg-slate-950 backdrop-blur-md supports-[backdrop-filter]:bg-white/92 dark:supports-[backdrop-filter]:bg-slate-950/92 shadow-[0_1px_0_0_rgba(0,0,0,0.04)] dark:shadow-[0_1px_0_0_rgba(255,255,255,0.04)]">
        <div class="max-w-7xl mx-auto w-full min-w-0 px-3 sm:px-6 lg:px-8">
            {{-- Single row on lg+: logo | scrollable nav | utilities (no stacked rows). --}}
            <div class="flex items-center gap-2 sm:gap-3 lg:gap-4 min-h-[3.25rem] lg:min-h-14">
                <div class="flex items-center gap-1.5 sm:gap-2 shrink-0 min-w-0">
                    <button type="button" id="website-hamburger-btn" aria-label="{{ t('menu') }}" aria-expanded="false" aria-controls="website-mobile-menu" class="lg:hidden shrink-0 p-2 -ml-1 rounded-lg text-stone-600 dark:text-slate-400 hover:text-stone-900 dark:hover:text-slate-100 hover:bg-stone-100 dark:hover:bg-slate-800 transition-colors">
                        <svg id="website-hamburger-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        <svg id="website-close-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                    <a href="{{ route('website.home') }}" class="flex items-center gap-2 min-w-0 shrink group rounded-md outline-none ring-amber-600/0 focus-visible:ring-2 focus-visible:ring-amber-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-950">
                        @php $logo = \App\Models\SiteSetting::imageUrl('logo'); @endphp
                        @if($logo)
                            <img src="{{ $logo }}" alt="{{ t('sangha_exam') }}" class="h-8 sm:h-9 max-w-[min(140px,42vw)] object-contain object-left transition-opacity opacity-95 group-hover:opacity-100">
                        @else
                            <span class="font-heading text-lg sm:text-[1.125rem] font-semibold text-amber-900 dark:text-amber-300 truncate tracking-tight">{{ t('sangha_exam') }}</span>
                        @endif
                    </a>
                </div>
                <nav class="hidden lg:flex flex-1 min-w-0 self-stretch items-center relative" aria-label="{{ t('main_navigation', 'Main') }}">
                    <div class="flex min-w-0 max-w-full flex-nowrap items-center gap-0.5 overflow-visible py-1">
                        <a href="{{ route('website.home') }}" class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap rounded-lg px-2.5 py-2 text-sm font-medium transition-colors {{ request()->routeIs('website.home') ? 'text-amber-900 dark:text-amber-300 bg-amber-100/90 dark:bg-amber-900/30 shadow-sm shadow-amber-900/5' : 'text-stone-800 dark:text-slate-100 hover:bg-stone-100 dark:hover:bg-slate-800/90' }}">@include('partials.icon', ['name' => 'home', 'class' => 'w-4 h-4 shrink-0 opacity-90']) {{ t('home') }}</a>
                        @foreach($navGroups as $group)
                            @if($group['items']->isNotEmpty())
                                @php $categoryActive = $group['items']->contains(fn ($item) => $item['active']); @endphp
                                <div class="relative group shrink-0">
                                    <button type="button" class="inline-flex items-center gap-1 whitespace-nowrap rounded-lg px-2.5 py-2 text-sm font-medium transition-colors {{ $categoryActive ? 'text-amber-900 dark:text-amber-300 bg-amber-100/90 dark:bg-amber-900/30' : 'text-stone-800 dark:text-slate-100 hover:bg-stone-100 dark:hover:bg-slate-800/90' }}" aria-expanded="false" aria-haspopup="true">
                                        {{ $group['label'] }}
                                        <span class="text-[10px] text-stone-500 dark:text-slate-400 transition-transform group-hover:rotate-180" aria-hidden="true">▾</span>
                                    </button>
                                    <div class="invisible opacity-0 pointer-events-none group-hover:visible group-hover:opacity-100 group-hover:pointer-events-auto group-focus-within:visible group-focus-within:opacity-100 group-focus-within:pointer-events-auto transition-all duration-150 absolute left-0 top-full z-[70] pt-1.5">
                                        <div class="min-w-[min(100vw-2rem,260px)] max-w-[min(100vw-2rem,320px)] rounded-xl border border-stone-200/90 dark:border-slate-600 bg-white dark:bg-slate-900 shadow-lg shadow-stone-200/40 dark:shadow-black/40 p-1.5 max-h-[min(70vh,24rem)] overflow-y-auto">
                                            @foreach($group['items'] as $item)
                                                <a href="{{ $item['url'] }}" class="flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-colors {{ $item['active'] ? 'text-amber-900 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/25' : 'text-stone-800 dark:text-slate-200 hover:bg-stone-100 dark:hover:bg-slate-800' }}">{{ $item['title'] }}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </nav>
                <div class="website-header-toolbar hidden lg:flex relative z-[80] shrink-0 items-center gap-1 sm:gap-1.5 border-l border-stone-200/80 dark:border-slate-700/90 pl-2 sm:pl-3 ml-0.5">
                    @include('website.partials.appbar-language')
                    @include('website.partials.appbar-theme')
                    @if(!auth()->guard('monastery')->check() && !auth()->guard('student')->check())
                        <div class="relative group">
                            <button type="button" class="inline-flex items-center gap-2 whitespace-nowrap rounded-lg border border-stone-200/90 dark:border-slate-600 bg-white dark:bg-slate-900 px-2.5 py-1.5 text-sm font-medium transition-colors {{ request()->routeIs('website.login') ? 'text-amber-900 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/25' : 'text-stone-800 dark:text-slate-100 hover:bg-stone-50 dark:hover:bg-slate-800' }}">
                                @include('partials.icon', ['name' => 'login', 'class' => 'w-4 h-4 shrink-0']) {{ t('login') }}
                                <span class="text-xs transition-transform group-hover:rotate-180">▾</span>
                            </button>
                            <div class="invisible opacity-0 pointer-events-none group-hover:visible group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-150 absolute right-0 top-full pt-2 z-[90]">
                                <div class="min-w-[180px] rounded-xl border border-stone-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-xl p-2">
                                    <a href="{{ route('website.login', ['type' => 'monastery']) }}" class="flex items-center rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('website.login') && request('type', 'monastery') !== 'sangha' && request('mode') !== 'register' ? 'text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20' : 'text-stone-600 dark:text-slate-300 hover:bg-stone-100 dark:hover:bg-slate-800 hover:text-stone-900 dark:hover:text-slate-100' }}">{{ t('monastery') }}</a>
                                    <a href="{{ route('website.login', ['type' => 'sangha']) }}" class="flex items-center rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('website.login') && request('type') === 'sangha' && request('mode') !== 'register' ? 'text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20' : 'text-stone-600 dark:text-slate-300 hover:bg-stone-100 dark:hover:bg-slate-800 hover:text-stone-900 dark:hover:text-slate-100' }}">{{ t('sangha_label', 'Sangha') }}</a>
                                </div>
                            </div>
                        </div>
                    @endif
                    <a href="{{ url('/admin') }}" class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-lg px-2.5 py-1.5 text-sm font-medium text-stone-800 dark:text-slate-100 hover:bg-stone-100 dark:hover:bg-slate-800 transition-colors">@include('partials.icon', ['name' => 'cog', 'class' => 'w-4 h-4 shrink-0 opacity-80']) {{ t('admin_panel') }}</a>
                    @if(auth()->guard('monastery')->check())
                        <a href="{{ route('monastery.dashboard') }}" class="inline-flex items-center gap-2 whitespace-nowrap px-4 py-2 rounded-lg text-sm font-medium text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors">@include('partials.icon', ['name' => 'home', 'class' => 'w-4 h-4 shrink-0']) {{ t('monastery_portal') }}</a>
                    @elseif(auth()->guard('student')->check())
                        <a href="{{ route('sangha.dashboard') }}" class="inline-flex items-center gap-2 whitespace-nowrap px-4 py-2 rounded-lg text-sm font-medium text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors">@include('partials.icon', ['name' => 'view', 'class' => 'w-4 h-4 shrink-0']) {{ t('my_scores') }}</a>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 w-full min-w-0 max-w-full pb-14 relative z-0">
        <div class="max-w-7xl w-full min-w-0 mx-auto px-3 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>
    @php
        $footer = \App\Models\Website::getBySlug('footer');
        $footerPrivacy = $navPages->firstWhere('slug', 'privacy');
    @endphp
    <footer class="mt-16 sm:mt-20 border-t border-stone-200/90 dark:border-slate-800/90 bg-gradient-to-b from-white/95 to-stone-50/90 dark:from-slate-950 dark:to-slate-900/95">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-14 lg:py-16">
            <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-12 xl:gap-20">
                <div class="max-w-lg shrink-0">
                    <a href="{{ route('website.home') }}" class="inline-flex font-heading text-2xl sm:text-[1.65rem] font-semibold text-stone-900 dark:text-slate-100 tracking-tight hover:text-amber-800 dark:hover:text-amber-300 transition-colors">
                        {{ t('sangha_exam') }}
                    </a>
                    @if($footer)
                        <div class="mt-5 prose prose-stone dark:prose-invert prose-sm max-w-none text-stone-600 dark:text-slate-400 leading-relaxed [&_p]:my-2 [&_a]:text-amber-700 dark:[&_a]:text-amber-400 [&_a]:no-underline hover:[&_a]:underline">
                            {!! $footer->content !!}
                        </div>
                    @else
                        <p class="mt-5 text-sm sm:text-[0.9375rem] text-stone-600 dark:text-slate-400 leading-relaxed">
                            {{ t('footer_tagline', 'Monastic examinations, registration, and results—presented with clarity and care.') }}
                        </p>
                    @endif
                </div>
                <div class="flex-1 min-w-0 xl:max-w-2xl">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-stone-400 dark:text-slate-500 mb-5">{{ t('quick_links') }}</p>
                    <nav class="flex flex-wrap gap-2 sm:gap-2.5" aria-label="{{ t('quick_links') }}">
                        @foreach($footerQuickLinks as $fl)
                            <a href="{{ $fl['url'] }}" class="inline-flex items-center rounded-full border border-transparent bg-stone-100/90 dark:bg-slate-800/80 px-4 py-2.5 text-sm font-medium text-stone-700 dark:text-slate-300 hover:border-amber-300/60 dark:hover:border-amber-600/40 hover:bg-white dark:hover:bg-slate-800 hover:text-stone-900 dark:hover:text-slate-100 transition-all duration-200">
                                {{ $fl['title'] }}
                            </a>
                        @endforeach
                    </nav>
                </div>
            </div>
            <div class="mt-12 sm:mt-14 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-8 border-t border-stone-200/70 dark:border-slate-800/80 text-xs sm:text-sm text-stone-500 dark:text-slate-500">
                <p class="tabular-nums">© {{ date('Y') }} {{ t('sangha_exam') }}. {{ t('footer_rights', 'All rights reserved.') }}</p>
                <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                    @if($footerPrivacy)
                        <a href="{{ route('website.page', $footerPrivacy->slug) }}" class="text-stone-600 dark:text-slate-400 hover:text-amber-800 dark:hover:text-amber-400 transition-colors">{{ $footerPrivacy->title }}</a>
                    @endif
                    @php $sitemapPage = $navPages->firstWhere('slug', 'sitemap'); @endphp
                    @if($sitemapPage)
                        <a href="{{ route('website.page', $sitemapPage->slug) }}" class="text-stone-600 dark:text-slate-400 hover:text-amber-800 dark:hover:text-amber-400 transition-colors">{{ $sitemapPage->title }}</a>
                    @endif
                </div>
            </div>
        </div>
    </footer>

    {{-- Drawer last in body: above header (z-120) + transformed page layers; avoids body overflow-x clipping fixed layers on iOS. --}}
    <div id="website-mobile-overlay" class="fixed inset-0 z-[5000] bg-stone-950/60 dark:bg-slate-950/80 lg:hidden hidden" aria-hidden="true"></div>
    <div id="website-mobile-menu" class="website-mobile-drawer fixed inset-y-0 right-0 z-[5010] isolate flex w-full max-w-[min(100vw,20.5rem)] flex-col border-l border-stone-200 dark:border-slate-700 bg-white dark:bg-slate-950 shadow-2xl shadow-stone-900/20 dark:shadow-black/50 translate-x-full lg:hidden transition-transform duration-300 ease-out overscroll-y-contain" aria-hidden="true" role="dialog" aria-modal="true" aria-label="{{ t('main_navigation', 'Main') }}">
        <div class="flex h-14 shrink-0 items-center justify-between gap-3 border-b border-stone-200 dark:border-slate-800 bg-white dark:bg-slate-950 px-4">
            <span class="font-heading text-lg font-semibold text-stone-900 dark:text-slate-100 truncate">{{ t('menu') }}</span>
            <button type="button" id="website-drawer-close-btn" class="shrink-0 rounded-xl p-2 text-stone-500 hover:bg-stone-100 hover:text-stone-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-100 transition-colors" aria-label="{{ t('close', 'Close') }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <div class="flex flex-1 flex-col min-h-0 overflow-y-auto bg-white dark:bg-slate-950 px-3 pb-8 pt-4">
            @include('website.partials.mobile-drawer-preferences')
            <nav class="flex flex-col gap-0.5" aria-label="{{ t('main_navigation', 'Main') }}">
                    <a href="{{ route('website.home') }}" class="flex items-center gap-3 min-h-[48px] px-4 py-3 rounded-xl text-base font-medium transition-colors {{ request()->routeIs('website.home') ? 'text-amber-700 dark:text-amber-400 bg-amber-50/80 dark:bg-amber-900/20' : 'text-stone-600 dark:text-slate-400 hover:text-stone-900 dark:hover:text-slate-100 hover:bg-stone-100 dark:hover:bg-slate-800' }}">@include('partials.icon', ['name' => 'home', 'class' => 'w-5 h-5 shrink-0']) {{ t('home') }}</a>
                    @foreach($navGroups as $group)
                        @if($group['items']->isNotEmpty())
                            <div class="px-4 pt-4 pb-1.5 text-[11px] font-semibold uppercase tracking-wider text-stone-400 dark:text-slate-500">{{ $group['label'] }}</div>
                            @foreach($group['items'] as $item)
                                <a href="{{ $item['url'] }}" class="flex items-center gap-3 min-h-[48px] px-4 py-3 rounded-xl text-base font-medium transition-colors {{ $item['active'] ? 'text-amber-700 dark:text-amber-400 bg-amber-50/80 dark:bg-amber-900/20' : 'text-stone-600 dark:text-slate-400 hover:text-stone-900 dark:hover:text-slate-100 hover:bg-stone-100 dark:hover:bg-slate-800' }}">{{ $item['title'] }}</a>
                            @endforeach
                        @endif
                    @endforeach
                    @if(!auth()->guard('monastery')->check() && !auth()->guard('student')->check())
                        <div class="px-4 pt-4 pb-1.5 text-[11px] font-semibold uppercase tracking-wider text-stone-400 dark:text-slate-500">{{ t('login') }}</div>
                        <a href="{{ route('website.login', ['type' => 'monastery']) }}" class="flex items-center gap-3 min-h-[48px] px-4 py-3 rounded-xl text-base font-medium transition-colors {{ request()->routeIs('website.login') && request('type', 'monastery') !== 'sangha' && request('mode') !== 'register' ? 'text-amber-700 dark:text-amber-400 bg-amber-50/80 dark:bg-amber-900/20' : 'text-stone-600 dark:text-slate-400 hover:text-stone-900 dark:hover:text-slate-100 hover:bg-stone-100 dark:hover:bg-slate-800' }}">@include('partials.icon', ['name' => 'home', 'class' => 'w-5 h-5 shrink-0']) {{ t('monastery') }}</a>
                        <a href="{{ route('website.login', ['type' => 'sangha']) }}" class="flex items-center gap-3 min-h-[48px] px-4 py-3 rounded-xl text-base font-medium transition-colors {{ request()->routeIs('website.login') && request('type') === 'sangha' && request('mode') !== 'register' ? 'text-amber-700 dark:text-amber-400 bg-amber-50/80 dark:bg-amber-900/20' : 'text-stone-600 dark:text-slate-400 hover:text-stone-900 dark:hover:text-slate-100 hover:bg-stone-100 dark:hover:bg-slate-800' }}">@include('partials.icon', ['name' => 'login', 'class' => 'w-5 h-5 shrink-0']) {{ t('sangha_label', 'Sangha') }}</a>
                    @endif
                    @if(auth()->guard('monastery')->check())
                        <a href="{{ route('monastery.dashboard') }}" class="flex items-center gap-2 whitespace-nowrap px-4 py-3 rounded-lg text-base font-medium text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors mt-2">@include('partials.icon', ['name' => 'home', 'class' => 'w-5 h-5 shrink-0']) {{ t('monastery_portal') }}</a>
                    @elseif(auth()->guard('student')->check())
                        <a href="{{ route('sangha.dashboard') }}" class="flex items-center gap-2 whitespace-nowrap px-4 py-3 rounded-lg text-base font-medium text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors mt-2">@include('partials.icon', ['name' => 'view', 'class' => 'w-5 h-5 shrink-0']) {{ t('my_scores') }}</a>
                    @else
                        <a href="{{ url('/admin') }}" class="flex items-center gap-2 whitespace-nowrap px-4 py-3 rounded-lg text-base font-medium text-stone-600 dark:text-slate-400 hover:text-stone-900 dark:hover:text-slate-100 hover:bg-stone-100 dark:hover:bg-slate-800 transition-colors mt-2">@include('partials.icon', ['name' => 'cog', 'class' => 'w-5 h-5 shrink-0']) {{ t('admin_panel') }}</a>
                    @endif
                </nav>
        </div>
    </div>
    <script>
    (function() {
        var btn = document.getElementById('website-hamburger-btn');
        var menu = document.getElementById('website-mobile-menu');
        var overlay = document.getElementById('website-mobile-overlay');
        var drawerClose = document.getElementById('website-drawer-close-btn');
        var hamburgerIcon = document.getElementById('website-hamburger-icon');
        var closeIcon = document.getElementById('website-close-icon');
        if (!btn || !menu || !overlay) return;
        function open() {
            menu.classList.remove('translate-x-full');
            menu.setAttribute('aria-hidden', 'false');
            overlay.classList.remove('hidden');
            overlay.setAttribute('aria-hidden', 'false');
            if (hamburgerIcon) hamburgerIcon.classList.add('hidden');
            if (closeIcon) closeIcon.classList.remove('hidden');
            btn.setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden';
        }
        function close() {
            menu.classList.add('translate-x-full');
            menu.setAttribute('aria-hidden', 'true');
            overlay.classList.add('hidden');
            overlay.setAttribute('aria-hidden', 'true');
            if (hamburgerIcon) hamburgerIcon.classList.remove('hidden');
            if (closeIcon) closeIcon.classList.add('hidden');
            btn.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        }
        window.sanghaWebsiteDrawerClose = close;
        btn.addEventListener('click', function() {
            if (menu.classList.contains('translate-x-full')) open();
            else close();
        });
        if (drawerClose) drawerClose.addEventListener('click', close);
        overlay.addEventListener('click', close);
        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') close();
        });
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) close();
        });
        menu.querySelectorAll('a').forEach(function(a) {
            a.addEventListener('click', close);
        });
        menu.addEventListener('click', function(e) {
            var opt = e.target.closest('.website-theme-choice');
            if (!opt || !menu.contains(opt)) return;
            e.preventDefault();
            e.stopPropagation();
            var theme = opt.getAttribute('data-website-theme');
            if (theme && typeof window.sanghaApplyWebsiteThemeChoice === 'function') {
                window.sanghaApplyWebsiteThemeChoice(theme);
            }
            close();
        });
    })();
    </script>
</body>
</html>
