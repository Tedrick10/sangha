@php
    $__favicon = \App\Models\SiteSetting::imageUrl('favicon');
@endphp
@if ($__favicon)
    <link rel="icon" href="{{ $__favicon }}" type="image/x-icon">
@else
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
@endif
