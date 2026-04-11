@php
    $bodyText = trim(strip_tags($page->content ?? '', '<img>'));
    $isLegalTheme = ($pageTheme ?? '') === 'legal';
@endphp
<div @class([
    'prose prose-stone dark:prose-invert max-w-none',
    'prose-sm sm:prose-base lg:prose-lg' => ! $isLegalTheme,
    'prose-lg sm:prose-xl' => $isLegalTheme,
    'prose-headings:font-heading prose-headings:font-semibold prose-headings:text-stone-900 dark:prose-headings:text-slate-50',
    'prose-p:text-stone-700 dark:prose-p:text-slate-300',
    'prose-p:leading-relaxed' => ! $isLegalTheme,
    'prose-p:leading-[1.85] prose-p:mb-5 prose-p:text-[1.0625rem] sm:prose-p:text-[1.075rem]' => $isLegalTheme,
    'prose-h2:mt-14 prose-h2:mb-5 prose-h2:pb-0 prose-h2:text-xl sm:prose-h2:text-2xl prose-h2:font-semibold prose-h2:tracking-tight prose-h2:scroll-mt-24 prose-h2:pl-[1.125rem] prose-h2:border-l-[3px] prose-h2:border-yellow-500 dark:prose-h2:border-yellow-400 prose-h2:text-stone-900 dark:prose-h2:text-slate-50 first:prose-h2:mt-0' => $isLegalTheme,
    'prose-ul:my-6 prose-ul:pl-1 prose-ul:list-disc prose-ul:marker:text-yellow-600 dark:prose-ul:marker:text-yellow-400' => $isLegalTheme,
    'prose-li:text-stone-700 dark:prose-li:text-slate-300 prose-li:my-2.5 prose-li:pl-1' => $isLegalTheme,
    'prose-li:text-stone-700 dark:prose-li:text-slate-300' => ! $isLegalTheme,
    'prose-strong:font-semibold prose-strong:text-stone-900 dark:prose-strong:text-slate-50' => $isLegalTheme,
    'prose-a:text-yellow-700 dark:prose-a:text-yellow-400 prose-a:font-medium prose-a:no-underline hover:prose-a:underline',
    'prose-img:rounded-2xl prose-img:shadow-sm',
])>
    @if($useFallbackContent ?? false)
        @if(($page->slug ?? '') === 'faq')
            @include('website.partials.faq-accordion')
        @else
            @include('website.partials.public-page-fallback-body')
        @endif
    @elseif(($page->slug ?? '') === 'faq')
        @include('website.partials.faq-accordion')
    @elseif($bodyText === '')
        <p class="text-stone-500 dark:text-slate-400 not-prose text-base">{{ t('page_empty_hint', 'Add body content for this page in the admin Website section.') }}</p>
    @else
        {!! $page->content !!}
    @endif
</div>
