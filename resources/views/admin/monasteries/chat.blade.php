@extends('admin.layout')

@section('title', t('monastery_chat_admin_title', 'Monastery chat'))

@section('content')
<div class="admin-page-header">
    <a href="{{ route('admin.monasteries.edit', $monastery) }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) {{ t('monasteries', 'Monasteries') }}</a>
    <h1>{{ t('monastery_chat_admin_title', 'Monastery chat') }} — {{ $monastery->name }}</h1>
</div>

<p class="mx-auto mb-6 max-w-3xl text-sm leading-relaxed text-slate-600 dark:text-slate-400">{{ t('monastery_chat_admin_intro', 'Messages sync automatically. The monastery sees replies in their portal under the Chat tab.') }}</p>

@include('partials.chat-thread', [
    'messages' => $messages,
    'fetchUrl' => route('admin.monasteries.chat.messages', $monastery),
    'sendUrl' => route('admin.monasteries.chat.messages.store', $monastery),
    'isAdminViewer' => true,
])
@endsection
