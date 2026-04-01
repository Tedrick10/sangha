@extends('admin.layout')

@section('title', t('site_images'))

@section('content')
<div class="mb-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-slate-100">{{ t('site_images') }}</h1>
</div>

<form action="{{ route('admin.site-images.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="grid gap-6 sm:grid-cols-2">
        @foreach($images as $key => $img)
            @php
                $icons = [
                    'logo' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />',
                    'favicon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />',
                    'og_image' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />',
                    'apple_touch_icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />',
                ];
            @endphp
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-700/80 bg-white dark:bg-slate-800/50 shadow-sm shadow-slate-200/30 dark:shadow-slate-900/50 hover:shadow-lg hover:shadow-amber-500/5 dark:hover:shadow-amber-500/5 hover:border-amber-200/60 dark:hover:border-amber-800/40 transition-all duration-300">
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-50 to-amber-100/80 dark:from-amber-900/30 dark:to-amber-800/20 text-amber-600 dark:text-amber-400 group-hover:from-amber-100 dark:group-hover:from-amber-900/40 group-hover:to-amber-200/60 dark:group-hover:to-amber-800/30 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icons[$key] ?? $icons['logo'] !!}</svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ $img['label'] }}</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ $img['hint'] }}</p>
                        </div>
                    </div>

                    <div class="mt-5">
                        <div class="relative rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-600 bg-slate-50/50 dark:bg-slate-900/30 overflow-hidden min-h-[120px] flex flex-col items-center justify-center p-6 transition-colors group-hover:border-amber-300/50 dark:group-hover:border-amber-700/50 group-hover:bg-amber-50/30 dark:group-hover:bg-amber-900/10">
                            @if($img['current'])
                                <div class="relative w-full flex items-center justify-center">
                                    @if(str_ends_with(strtolower($img['current'] ?? ''), '.svg'))
                                        <img src="{{ $img['current'] }}" alt="{{ $img['label'] }}" class="max-h-24 max-w-full object-contain drop-shadow-sm dark:brightness-95">
                                    @else
                                        <img src="{{ $img['current'] }}" alt="{{ $img['label'] }}" class="max-h-24 max-w-full object-contain rounded-lg drop-shadow-sm">
                                    @endif
                                    <form action="{{ route('admin.site-images.destroy', $key) }}" method="POST" class="absolute -top-1 -right-1" onsubmit="return confirm('{{ t('remove_image_confirm') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-full bg-red-500 text-white shadow-md hover:bg-red-600 focus:ring-2 focus:ring-red-400 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition-colors" aria-label="{{ t('remove') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <svg class="w-10 h-10 text-slate-300 dark:text-slate-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                <p class="text-sm text-slate-400 dark:text-slate-500">{{ t('no_image') }}</p>
                            @endif
                            <label for="{{ $key }}" class="mt-4 cursor-pointer inline-flex items-center gap-2 rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-600 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                {{ $img['current'] ? t('replace') : t('upload') }}
                            </label>
                            <input type="file" name="{{ $key }}" id="{{ $key }}" accept=".png,.jpg,.jpeg,.svg,.gif,.webp,.ico" class="sr-only">
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-8 flex flex-wrap gap-3 justify-end">
        <button type="submit" class="admin-btn-primary">{{ t('update') }}</button>
        <a href="{{ route('admin.custom-fields.index') }}" class="admin-btn-secondary">{{ t('cancel') }}</a>
    </div>
</form>
@endsection
