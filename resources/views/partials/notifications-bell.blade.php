@php
    $goRouteName = $goRouteName ?? 'admin.notifications.go';
    $readAllRouteName = $readAllRouteName ?? 'admin.notifications.read-all';
    $jsonRouteName = $jsonRouteName ?? 'admin.notifications.recent';
    $unreadCount = $notifiable->unreadNotifications()->count();
    $items = $notifiable->notifications()->limit(15)->get();
@endphp
<div
    class="js-notifications-bell-root"
    data-json-url="{{ route($jsonRouteName) }}"
    data-empty-text="{{ e(t('no_notifications', 'No notifications yet.')) }}"
>
    <details class="relative z-[60] group">
        <summary class="relative flex cursor-pointer list-none items-center justify-center rounded-lg p-2 text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-100 [&::-webkit-details-marker]:hidden">
            <span class="sr-only">{{ t('notifications', 'Notifications') }}</span>
            @include('partials.icon', ['name' => 'bell', 'class' => 'h-5 w-5 sm:h-6 sm:w-6'])
            <span
                class="js-notif-badge absolute right-1 top-1 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-amber-500 px-1 text-[10px] font-bold leading-none text-white dark:bg-amber-600 {{ $unreadCount > 0 ? '' : 'hidden' }}"
                aria-hidden="true"
            >{{ $unreadCount > 99 ? '99+' : (string) $unreadCount }}</span>
        </summary>
        <div class="absolute right-0 top-full mt-2 w-[min(100vw-2rem,22rem)] overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900">
            <div class="flex items-center justify-between border-b border-slate-200/80 px-3 py-2 dark:border-slate-700">
                <span class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ t('notifications', 'Notifications') }}</span>
                <div class="js-notif-markall-wrap {{ $unreadCount > 0 ? '' : 'hidden' }}">
                    <form action="{{ route($readAllRouteName) }}" method="post" class="inline">
                        @csrf
                        <button type="submit" class="text-xs font-medium text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300">{{ t('mark_all_read', 'Mark all read') }}</button>
                    </form>
                </div>
            </div>
            <div class="js-notif-list max-h-[min(70vh,24rem)] overflow-y-auto">
                @forelse($items as $n)
                    @php
                        $d = $n->data ?? [];
                        $title = $d['title'] ?? '';
                        $body = $d['body'] ?? '';
                        $isUnread = $n->read_at === null;
                    @endphp
                    <a href="{{ route($goRouteName, $n->id) }}" class="block border-b border-slate-100 px-3 py-2.5 text-left transition-colors last:border-b-0 hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/80 {{ $isUnread ? 'bg-amber-50/80 dark:bg-amber-950/20' : '' }}">
                        <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $title }}</p>
                        <p class="mt-0.5 line-clamp-2 text-xs text-slate-600 dark:text-slate-400">{{ $body }}</p>
                        <p class="mt-1 text-[10px] text-slate-400 dark:text-slate-500">{{ $n->created_at?->diffForHumans() }}</p>
                    </a>
                @empty
                    <p class="js-notif-empty px-3 py-8 text-center text-sm text-slate-500 dark:text-slate-400">{{ t('no_notifications', 'No notifications yet.') }}</p>
                @endforelse
            </div>
        </div>
    </details>
</div>
