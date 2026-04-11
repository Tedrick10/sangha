@php
    $lines = app_brand_title_lines();
    $outerClass = $outerClass ?? '';
    $lineClass = $lineClass ?? '';
    $align = $align ?? 'start';
    $center = $align === 'center';
    $centerBlock = $center ? 'min-h-[2.5rem] justify-center sm:min-h-[2.65rem] lg:min-h-0' : '';
    $lineGap = count($lines) >= 3 ? 'gap-y-0.5' : 'gap-y-2';
@endphp
<span class="{{ $center ? 'inline-flex items-center text-center' : 'inline-flex items-start text-left' }} max-w-full min-w-0 flex-col {{ $lineGap }} {{ $centerBlock }} {{ $outerClass }}" title="{{ config('app.name') }}">
    @foreach ($lines as $line)
        <span class="{{ $center ? 'w-full text-center' : 'w-full text-left' }} block max-w-full break-words {{ $lineClass }}">{{ $line }}</span>
    @endforeach
</span>
