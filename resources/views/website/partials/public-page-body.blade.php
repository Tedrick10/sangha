@php
    $bodyText = trim(strip_tags($page->content ?? '', '<img>'));
@endphp
<div class="prose prose-stone dark:prose-invert prose-sm sm:prose-base lg:prose-lg max-w-none
    prose-headings:font-heading prose-headings:font-semibold prose-headings:text-stone-900 dark:prose-headings:text-slate-100
    prose-p:text-stone-700 dark:prose-p:text-slate-300 prose-p:leading-relaxed
    prose-li:text-stone-700 dark:prose-li:text-slate-300
    prose-a:text-amber-700 dark:prose-a:text-amber-400 prose-a:no-underline hover:prose-a:underline
    prose-img:rounded-2xl prose-img:shadow-sm">
    @if($useFallbackContent ?? false)
        @include('website.partials.public-page-fallback-body')
    @elseif($bodyText === '')
        <p class="text-stone-500 dark:text-slate-400 not-prose text-base">{{ t('page_empty_hint', 'Add body content for this page in the admin Website section.') }}</p>
    @else
        {!! $page->content !!}
    @endif
</div>
