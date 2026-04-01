@extends('admin.layout')

@section('title', 'Monastery Requests')

@section('content')
<div class="admin-page-header">
    <h1>Monastery Requests</h1>
</div>

<div class="admin-table-card overflow-x-auto">
    <table class="admin-table divide-y divide-slate-100">
        <thead>
            <tr>
                <th>Monastery</th>
                <th>Unread</th>
                <th>Last Message</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($monasteries as $monastery)
                <tr>
                    <td>
                        <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $monastery->name }}</span>
                        <span class="block text-xs text-slate-500 dark:text-slate-400">{{ $monastery->username ?? '—' }}</span>
                    </td>
                    <td>
                        @if($monastery->unread_messages_count > 0)
                            <span class="inline-flex rounded-full bg-amber-100 dark:bg-amber-900/40 px-3 py-1 text-xs font-semibold text-amber-700 dark:text-amber-300">{{ $monastery->unread_messages_count }}</span>
                        @else
                            <span class="inline-flex rounded-full bg-slate-100 dark:bg-slate-700 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-300">0</span>
                        @endif
                    </td>
                    <td>{{ $monastery->messages_max_created_at ? \Illuminate\Support\Carbon::parse($monastery->messages_max_created_at)->format('M d, Y H:i') : '—' }}</td>
                    <td class="text-right">
                        <a href="{{ route('admin.monastery-requests.show', $monastery) }}" class="admin-action-link admin-action-edit">Open</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="admin-table-empty">No monastery request messages yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@include('admin.partials.table-pagination', ['paginator' => $monasteries, 'routeName' => 'admin.monastery-requests.index'])
@endsection

