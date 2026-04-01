@php
    $websiteTheme = session('website_theme', 'system');
    $navPages = \App\Models\Website::where('type', 'page')->where('is_published', true)->orderBy('sort_order')->orderBy('title')->get();
    $navLinks = collect();

    foreach ($navPages as $navPage) {
        if (in_array($navPage->slug, ['home', 'login'], true)) {
            continue;
        }

        $isRegistration = $navPage->slug === 'registration';
        $url = $isRegistration
            ? route('website.login', ['type' => 'monastery', 'mode' => 'register'])
            : route('website.page', $navPage->slug);
        $isActive = $isRegistration
            ? (request()->routeIs('website.login') && request('mode') === 'register')
            : (request()->routeIs('website.page') && (request()->route('slug') ?? '') === $navPage->slug);

        $navLinks->push([
            'title' => $navPage->title,
            'slug' => $navPage->slug,
            'url' => $url,
            'active' => $isActive,
        ]);
    }

    $categorizedLinks = [
        t('menu_examinations') => collect(),
        t('menu_more', 'More') => collect(),
    ];

    foreach ($navLinks as $link) {
        $text = strtolower($link['slug'] . ' ' . $link['title']);

        if (str_contains($text, 'exam') || str_contains($text, 'schedule') || str_contains($text, 'subject')) {
            $categorizedLinks[t('menu_examinations')]->push($link);
        } else {
            $categorizedLinks[t('menu_more', 'More')]->push($link);
        }
    }

    $categorizedLinks[t('menu_examinations')]->push([
        'title' => t('pass_sangha_list', 'Pass Sangha List'),
        'slug' => 'pass-sanghas',
        'url' => route('website.pass-sanghas'),
        'active' => request()->routeIs('website.pass-sanghas'),
    ]);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ $websiteTheme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', t('sangha_exam')) - {{ config('app.name', 'Sangha Exam') }}</title>
    @php $favicon = \App\Models\SiteSetting::imageUrl('favicon'); $ogImage = \App\Models\SiteSetting::imageUrl('og_image'); @endphp
    @if($favicon)<link rel="icon" href="{{ $favicon }}" type="image/x-icon">@endif
    @if($ogImage)<meta property="og:image" content="{{ url($ogImage) }}">@endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:400,500,600,700|instrument-sans:400,500,600" rel="stylesheet" />
    <script>
        (function() {
            var theme = document.documentElement.getAttribute('data-theme') || 'system';
            var dark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (dark) document.documentElement.classList.add('dark');
            else document.documentElement.classList.remove('dark');
        })();
    </script>
    <style>
        .font-heading { font-family: 'Cormorant Garamond', ui-serif, Georgia, serif; }
    </style>
</head>
<body class="min-h-screen bg-stone-50 dark:bg-slate-950 text-stone-900 dark:text-slate-100 font-sans antialiased">
    <div class="fixed inset-0 -z-10 pointer-events-none bg-[radial-gradient(ellipse_68%_46%_at_50%_-10%,rgba(251,191,36,0.14),transparent)] dark:bg-[radial-gradient(ellipse_68%_46%_at_50%_-10%,rgba(251,191,36,0.08),transparent)]"></div>
    <header class="sticky top-0 z-[90] border-b border-stone-200/80 dark:border-slate-800/80 bg-white/88 dark:bg-slate-950/88 backdrop-blur-xl shadow-sm">
        {{-- Mobile menu overlay --}}
        <div id="website-mobile-overlay" class="fixed inset-0 bg-stone-900/50 dark:bg-slate-900/70 z-40 md:hidden hidden" aria-hidden="true"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-18">
                <div class="flex items-center gap-3">
                    <button type="button" id="website-hamburger-btn" aria-label="{{ t('menu') }}" aria-expanded="false" aria-controls="website-mobile-menu" class="md:hidden p-2 -ml-2 rounded-lg text-stone-600 dark:text-slate-400 hover:text-stone-900 dark:hover:text-slate-100 hover:bg-stone-100 dark:hover:bg-slate-800 transition-colors">
                        <svg id="website-hamburger-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        <svg id="website-close-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                    <a href="{{ route('website.home') }}" class="flex items-center gap-2 shrink-0 group">
                        @php $logo = \App\Models\SiteSetting::imageUrl('logo'); @endphp
                        @if($logo)
                            <img src="{{ $logo }}" alt="{{ t('sangha_exam') }}" class="h-9 max-w-[160px] object-contain transition opacity-90 group-hover:opacity-100">
                        @else
                            <span class="font-heading text-xl font-semibold text-amber-800 dark:text-amber-300">{{ t('sangha_exam') }}</span>
                        @endif
                    </a>
                </div>
                <nav class="hidden md:flex flex-1 min-w-0 px-4 relative z-[50] overflow-visible">
                    <div class="flex items-center gap-2 py-1 overflow-visible">
                        <a href="{{ route('website.home') }}" class="inline-flex items-center gap-2 whitespace-nowrap px-3.5 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('website.home') ? 'text-amber-700 dark:text-amber-400 bg-amber-50/80 dark:bg-amber-900/20' : 'text-stone-600 dark:text-slate-400 hover:text-stone-900 dark:hover:text-slate-100 hover:bg-stone-100 dark:hover:bg-slate-800' }}">@include('partials.icon', ['name' => 'home', 'class' => 'w-4 h-4 shrink-0']) {{ t('home') }}</a>
                        @foreach($categorizedLinks as $categoryLabel => $items)
                            @if($items->isNotEmpty())
                                @php $categoryActive = $items->contains(fn ($item) => $item['active']); @endphp
                                <div class="relative group">
                                    <button type="button" class="inline-flex items-center gap-1 whitespace-nowrap px-3.5 py-2 rounded-xl text-sm font-medium transition-colors {{ $categoryActive ? 'text-amber-700 dark:text-amber-400 bg-amber-50/80 dark:bg-amber-900/20' : 'text-stone-600 dark:text-slate-400 hover:text-stone-900 dark:hover:text-slate-100 hover:bg-stone-100 dark:hover:bg-slate-800' }}">
                                        {{ $categoryLabel }}
                                        <span class="text-xs transition-transform group-hover:rotate-180">▾</span>
                                    </button>
                                    <div class="invisible opacity-0 pointer-events-none group-hover:visible group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-150 absolute left-0 top-full pt-2 z-[70]">
                                        <div class="min-w-[220px] rounded-xl border border-stone-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-xl p-2">
                                            @foreach($items as $item)
                                                <a href="{{ $item['url'] }}" class="flex items-center rounded-lg px-3 py-2 text-sm transition-colors {{ $item['active'] ? 'text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20' : 'text-stone-600 dark:text-slate-300 hover:bg-stone-100 dark:hover:bg-slate-800 hover:text-stone-900 dark:hover:text-slate-100' }}">{{ $item['title'] }}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </nav>
                <div class="relative z-[80] flex items-center gap-2 shrink-0">
                    @include('website.partials.appbar-language')
                    @include('website.partials.appbar-theme')
                    @if(!auth()->guard('monastery')->check() && !auth()->guard('student')->check())
                        <div class="hidden sm:block relative group">
                            <button type="button" class="inline-flex items-center gap-2 whitespace-nowrap px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('website.login') ? 'text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20' : 'text-stone-500 dark:text-slate-400 hover:text-stone-700 dark:hover:text-slate-200 hover:bg-stone-100 dark:hover:bg-slate-800' }} transition-colors">
                                @include('partials.icon', ['name' => 'login', 'class' => 'w-4 h-4 shrink-0']) {{ t('login') }}
                                <span class="text-xs transition-transform group-hover:rotate-180">▾</span>
                            </button>
                            <div class="invisible opacity-0 pointer-events-none group-hover:visible group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-150 absolute right-0 top-full pt-2 z-[90]">
                                <div class="min-w-[180px] rounded-xl border border-stone-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-xl p-2">
                                    <a href="{{ route('website.login', ['type' => 'monastery']) }}" class="flex items-center rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('website.login') && request('type', 'monastery') !== 'sangha' ? 'text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20' : 'text-stone-600 dark:text-slate-300 hover:bg-stone-100 dark:hover:bg-slate-800 hover:text-stone-900 dark:hover:text-slate-100' }}">{{ t('monastery') }}</a>
                                    <a href="{{ route('website.login', ['type' => 'sangha']) }}" class="flex items-center rounded-lg px-3 py-2 text-sm transition-colors {{ request()->routeIs('website.login') && request('type') === 'sangha' ? 'text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20' : 'text-stone-600 dark:text-slate-300 hover:bg-stone-100 dark:hover:bg-slate-800 hover:text-stone-900 dark:hover:text-slate-100' }}">{{ t('sangha_label', 'Sangha') }}</a>
                                </div>
                            </div>
                        </div>
                    @endif
                    <a href="{{ url('/admin') }}" class="hidden sm:inline-flex items-center gap-2 whitespace-nowrap px-3 py-2 rounded-lg text-sm font-medium text-stone-500 dark:text-slate-400 hover:text-stone-700 dark:hover:text-slate-200 hover:bg-stone-100 dark:hover:bg-slate-800 transition-colors">@include('partials.icon', ['name' => 'cog', 'class' => 'w-4 h-4 shrink-0']) {{ t('admin_panel') }}</a>
                    @if(auth()->guard('monastery')->check())
                        <a href="{{ route('monastery.dashboard') }}" class="inline-flex items-center gap-2 whitespace-nowrap px-4 py-2 rounded-lg text-sm font-medium text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors">@include('partials.icon', ['name' => 'home', 'class' => 'w-4 h-4 shrink-0']) {{ t('monastery_portal') }}</a>
                    @elseif(auth()->guard('student')->check())
                        <a href="{{ route('sangha.dashboard') }}" class="inline-flex items-center gap-2 whitespace-nowrap px-4 py-2 rounded-lg text-sm font-medium text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors">@include('partials.icon', ['name' => 'view', 'class' => 'w-4 h-4 shrink-0']) {{ t('my_scores') }}</a>
                    @endif
                </div>
            </div>
        </div>
        {{-- Mobile menu --}}
        <div id="website-mobile-menu" class="fixed inset-y-0 right-0 z-50 w-full max-w-[300px] bg-white dark:bg-slate-950 border-l border-stone-200 dark:border-slate-800 shadow-xl transform translate-x-full md:hidden transition-transform duration-300 ease-out" aria-hidden="true">
            <div class="flex flex-col h-full pt-20 pb-6 px-4">
                <nav class="flex flex-col gap-1">
                    <a href="{{ route('website.home') }}" class="flex items-center gap-2 whitespace-nowrap px-4 py-3 rounded-xl text-base font-medium transition-colors {{ request()->routeIs('website.home') ? 'text-amber-700 dark:text-amber-400 bg-amber-50/80 dark:bg-amber-900/20' : 'text-stone-600 dark:text-slate-400 hover:text-stone-900 dark:hover:text-slate-100 hover:bg-stone-100 dark:hover:bg-slate-800' }}">@include('partials.icon', ['name' => 'home', 'class' => 'w-5 h-5 shrink-0']) {{ t('home') }}</a>
                    @foreach($categorizedLinks as $categoryLabel => $items)
                        @if($items->isNotEmpty())
                            <div class="px-4 pt-3 pb-1 text-[11px] uppercase tracking-wider text-stone-400 dark:text-slate-500">{{ $categoryLabel }}</div>
                            @foreach($items as $item)
                                <a href="{{ $item['url'] }}" class="flex items-center gap-2 whitespace-nowrap px-4 py-3 rounded-xl text-base font-medium transition-colors {{ $item['active'] ? 'text-amber-700 dark:text-amber-400 bg-amber-50/80 dark:bg-amber-900/20' : 'text-stone-600 dark:text-slate-400 hover:text-stone-900 dark:hover:text-slate-100 hover:bg-stone-100 dark:hover:bg-slate-800' }}">{{ $item['title'] }}</a>
                            @endforeach
                        @endif
                    @endforeach
                    @if(!auth()->guard('monastery')->check() && !auth()->guard('student')->check())
                        <div class="px-4 pt-3 pb-1 text-[11px] uppercase tracking-wider text-stone-400 dark:text-slate-500">{{ t('login') }}</div>
                        <a href="{{ route('website.login', ['type' => 'monastery']) }}" class="flex items-center gap-2 whitespace-nowrap px-4 py-3 rounded-xl text-base font-medium transition-colors {{ request()->routeIs('website.login') && request('type', 'monastery') !== 'sangha' ? 'text-amber-700 dark:text-amber-400 bg-amber-50/80 dark:bg-amber-900/20' : 'text-stone-600 dark:text-slate-400 hover:text-stone-900 dark:hover:text-slate-100 hover:bg-stone-100 dark:hover:bg-slate-800' }}">@include('partials.icon', ['name' => 'home', 'class' => 'w-5 h-5 shrink-0']) {{ t('monastery') }}</a>
                        <a href="{{ route('website.login', ['type' => 'sangha']) }}" class="flex items-center gap-2 whitespace-nowrap px-4 py-3 rounded-xl text-base font-medium transition-colors {{ request()->routeIs('website.login') && request('type') === 'sangha' ? 'text-amber-700 dark:text-amber-400 bg-amber-50/80 dark:bg-amber-900/20' : 'text-stone-600 dark:text-slate-400 hover:text-stone-900 dark:hover:text-slate-100 hover:bg-stone-100 dark:hover:bg-slate-800' }}">@include('partials.icon', ['name' => 'login', 'class' => 'w-5 h-5 shrink-0']) {{ t('sangha_label', 'Sangha') }}</a>
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
    </header>
    <script>
    (function() {
        var btn = document.getElementById('website-hamburger-btn');
        var menu = document.getElementById('website-mobile-menu');
        var overlay = document.getElementById('website-mobile-overlay');
        var hamburgerIcon = document.getElementById('website-hamburger-icon');
        var closeIcon = document.getElementById('website-close-icon');
        if (!btn || !menu || !overlay) return;
        function open() {
            menu.classList.remove('translate-x-full');
            menu.setAttribute('aria-hidden', 'false');
            overlay.classList.remove('hidden');
            overlay.setAttribute('aria-hidden', 'false');
            hamburgerIcon.classList.add('hidden');
            closeIcon.classList.remove('hidden');
            btn.setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden';
        }
        function close() {
            menu.classList.add('translate-x-full');
            menu.setAttribute('aria-hidden', 'true');
            overlay.classList.add('hidden');
            overlay.setAttribute('aria-hidden', 'true');
            hamburgerIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');
            btn.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        }
        btn.addEventListener('click', function() {
            if (menu.classList.contains('translate-x-full')) open();
            else close();
        });
        overlay.addEventListener('click', close);
        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') close();
        });
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) close();
        });
        menu.querySelectorAll('a').forEach(function(a) {
            a.addEventListener('click', close);
        });
    })();
    </script>
    <main class="flex-1 pb-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>
    @php $footer = \App\Models\Website::getBySlug('footer'); @endphp
    <footer class="border-t border-stone-200 dark:border-slate-800 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm py-12 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-10 md:grid-cols-[1.2fr_1fr]">
                <div>
                    <h3 class="font-heading text-2xl text-stone-900 dark:text-slate-100 mb-4">{{ t('sangha_exam') }}</h3>
                    @if($footer)
                        <div class="prose prose-stone dark:prose-invert max-w-none text-sm text-stone-600 dark:text-slate-400 [&_a]:text-amber-700 dark:[&_a]:text-amber-400 [&_a]:no-underline hover:[&_a]:underline [&_a]:transition-colors">
                            {!! $footer->content !!}
                        </div>
                    @endif
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-stone-700 dark:text-slate-300 uppercase tracking-[0.14em] mb-4">{{ t('quick_links') }}</h4>
                    <div class="grid sm:grid-cols-2 gap-2">
                        <a href="{{ route('website.home') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm text-stone-600 dark:text-slate-400 hover:text-stone-900 dark:hover:text-slate-100 hover:bg-stone-100 dark:hover:bg-slate-800 transition-colors">@include('partials.icon', ['name' => 'home', 'class' => 'w-4 h-4']) {{ t('home') }}</a>
                        @foreach($navPages->whereNotIn('slug', ['home', 'login', 'registration'])->take(7) as $navPage)
                            <a href="{{ $navPage->slug === 'registration' ? route('website.login', ['type' => 'monastery', 'mode' => 'register']) : route('website.page', $navPage->slug) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm text-stone-600 dark:text-slate-400 hover:text-stone-900 dark:hover:text-slate-100 hover:bg-stone-100 dark:hover:bg-slate-800 transition-colors">{{ $navPage->title }}</a>
                        @endforeach
                        <a href="{{ route('website.pass-sanghas') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm text-stone-600 dark:text-slate-400 hover:text-stone-900 dark:hover:text-slate-100 hover:bg-stone-100 dark:hover:bg-slate-800 transition-colors">{{ t('pass_sangha_list', 'Pass Sangha List') }}</a>
                        <a href="{{ route('website.login') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm text-amber-700 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors">@include('partials.icon', ['name' => 'login', 'class' => 'w-4 h-4']) {{ t('login') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
