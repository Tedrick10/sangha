@extends('website.layout')

@section('title', $page->title)

@section('content')
<section class="relative py-10 sm:py-14 lg:py-16">
    <div class="max-w-4xl mx-auto">
        <div class="rounded-3xl border border-stone-200/80 dark:border-slate-800 bg-white/85 dark:bg-slate-900/70 backdrop-blur-sm shadow-sm">
            <header class="px-6 sm:px-8 lg:px-10 pt-8 sm:pt-10 pb-6 border-b border-stone-200/80 dark:border-slate-800">
                <h1 class="font-heading text-3xl sm:text-4xl font-semibold text-stone-900 dark:text-slate-100 tracking-tight">
                    {{ $page->title }}
                </h1>
            </header>
            <article class="px-6 sm:px-8 lg:px-10 py-8 sm:py-10 prose prose-stone dark:prose-invert max-w-none
                prose-headings:font-heading prose-headings:font-semibold prose-headings:text-stone-900 dark:prose-headings:text-slate-100
                prose-p:text-stone-700 dark:prose-p:text-slate-300 prose-p:leading-relaxed
                prose-li:text-stone-700 dark:prose-li:text-slate-300
                prose-a:text-amber-700 dark:prose-a:text-amber-400 prose-a:no-underline hover:prose-a:underline
                prose-img:rounded-2xl prose-img:shadow-sm">
                {!! $page->content !!}
            </article>
        </div>
    </div>
</section>
@endsection
