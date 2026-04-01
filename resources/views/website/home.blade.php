@extends('website.layout')

@section('title', t('home'))

@section('content')
@php
    $featuredPages = \App\Models\Website::where('type', 'page')
        ->where('is_published', true)
        ->whereNotIn('slug', ['home', 'login', 'footer'])
        ->orderBy('sort_order')
        ->orderBy('title')
        ->take(6)
        ->get();
@endphp
@if($page && $page->content)
    <section class="relative py-10 sm:py-14 lg:py-16">
        <div class="grid gap-6 lg:gap-8">
            <div class="rounded-3xl border border-stone-200/80 dark:border-slate-800 bg-white/85 dark:bg-slate-900/70 backdrop-blur-sm shadow-sm p-6 sm:p-8 lg:p-10">
                <article class="prose prose-stone dark:prose-invert prose-lg max-w-none
                    prose-headings:font-heading prose-headings:font-semibold prose-headings:text-stone-900 dark:prose-headings:text-slate-100
                    prose-p:text-stone-700 dark:prose-p:text-slate-300 prose-p:leading-relaxed
                    prose-a:text-amber-700 dark:prose-a:text-amber-400 prose-a:no-underline hover:prose-a:underline
                    prose-strong:text-stone-900 dark:prose-strong:text-slate-100">
                    {!! $page->content !!}
                </article>
            </div>
            @if($featuredPages->isNotEmpty())
                <div class="rounded-3xl border border-stone-200/80 dark:border-slate-800 bg-white/70 dark:bg-slate-900/60 p-6 sm:p-8">
                    <h2 class="font-heading text-2xl sm:text-3xl text-stone-900 dark:text-slate-100 mb-5">Browse pages</h2>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($featuredPages as $featuredPage)
                            <a href="{{ $featuredPage->slug === 'registration' ? route('website.register') : route('website.page', $featuredPage->slug) }}" class="group rounded-2xl border border-stone-200 dark:border-slate-700 bg-white dark:bg-slate-900/80 px-4 py-3.5 hover:border-amber-300 dark:hover:border-amber-700 hover:shadow-sm transition-all">
                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-sm font-medium text-stone-700 dark:text-slate-300 group-hover:text-stone-900 dark:group-hover:text-slate-100">{{ $featuredPage->title }}</span>
                                    <span class="text-amber-600 dark:text-amber-400 transition-transform group-hover:translate-x-0.5">@include('partials.icon', ['name' => 'chevron-right', 'class' => 'w-4 h-4'])</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@else
    <section class="relative min-h-[70vh] flex flex-col justify-center overflow-hidden py-10 sm:py-14 lg:py-16">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_60%_at_50%_-20%,rgba(251,191,36,0.15),transparent)] dark:bg-[radial-gradient(ellipse_80%_60%_at_50%_-20%,rgba(251,191,36,0.08),transparent)] pointer-events-none" aria-hidden="true"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiM5OTkiIGZpbGwtb3BhY2l0eT0iMC4wNCI+PGNpcmNsZSBjeD0iMzAiIGN5PSIzMCIgcj0iMS41Ii8+PC9nPjwvZz48L3N2Zz4=')] opacity-60 dark:opacity-40 pointer-events-none" aria-hidden="true"></div>
        <div class="relative max-w-4xl mx-auto py-20 lg:py-28 text-center">
            <h1 class="font-heading text-4xl sm:text-5xl lg:text-6xl font-semibold text-stone-900 dark:text-slate-100 tracking-tight mb-6">
                {{ t('sangha_exam') }}
            </h1>
            <p class="text-lg sm:text-xl text-stone-600 dark:text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed">
                {{ t('welcome_home_default') }}
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('website.login') }}" class="inline-flex items-center gap-2 whitespace-nowrap px-8 py-4 rounded-xl bg-amber-500 text-white font-semibold shadow-lg shadow-amber-500/25 hover:bg-amber-600 hover:shadow-amber-500/30 focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-slate-900 transition-all duration-200">
                    @include('partials.icon', ['name' => 'login', 'class' => 'w-5 h-5']) {{ t('login') }}
                </a>
                @if($featuredPages->first())
                    <a href="{{ $featuredPages->first()->slug === 'registration' ? route('website.register') : route('website.page', $featuredPages->first()->slug) }}" class="inline-flex items-center gap-2 whitespace-nowrap px-8 py-4 rounded-xl border-2 border-stone-200 dark:border-slate-600 text-stone-800 dark:text-slate-200 font-semibold hover:bg-stone-100 dark:hover:bg-slate-800 hover:border-stone-300 dark:hover:border-slate-500 transition-all duration-200">
                        @include('partials.icon', ['name' => 'information-circle', 'class' => 'w-5 h-5']) {{ $featuredPages->first()->title }}
                    </a>
                @endif
            </div>
            @if($featuredPages->isNotEmpty())
                <div class="mt-14 rounded-3xl border border-stone-200/80 dark:border-slate-800 bg-white/80 dark:bg-slate-900/70 backdrop-blur-sm p-5 sm:p-7">
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($featuredPages as $featuredPage)
                            <a href="{{ $featuredPage->slug === 'registration' ? route('website.register') : route('website.page', $featuredPage->slug) }}" class="group rounded-2xl border border-stone-200 dark:border-slate-700 bg-white dark:bg-slate-900/80 px-4 py-3.5 hover:border-amber-300 dark:hover:border-amber-700 hover:shadow-sm transition-all">
                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-sm font-medium text-stone-700 dark:text-slate-300 group-hover:text-stone-900 dark:group-hover:text-slate-100">{{ $featuredPage->title }}</span>
                                    <span class="text-amber-600 dark:text-amber-400 transition-transform group-hover:translate-x-0.5">@include('partials.icon', ['name' => 'chevron-right', 'class' => 'w-4 h-4'])</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endif
@endsection
