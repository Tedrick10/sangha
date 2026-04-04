@extends('admin.layout')

@section('title', 'Marks: ' . $sangha->name . ' – ' . $exam->name)

@section('content')
<div class="admin-form-page-header">
    <a href="{{ route('admin.sanghas.show', $sangha) }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) {{ $sangha->name }}</a>
    <h1 class="admin-page-title">Marks for {{ $exam->name }}</h1>
    <p class="text-slate-600 dark:text-slate-400 mt-1">{{ $sangha->name }} · {{ $exam->exam_date?->format('M d, Y') ?? '—' }}</p>
</div>

<div class="admin-table-card">
    <table class="admin-table divide-y divide-slate-100">
        <thead>
            <tr>
                <th class="w-12 text-left">No.</th>
                <th>Subject</th>
                <th>Mark</th>
            </tr>
        </thead>
        <tbody>
            @forelse($scores as $score)
                <tr>
                    <td class="text-slate-600 dark:text-slate-400">{{ $loop->iteration }}</td>
                    <td><span class="font-semibold text-slate-900 dark:text-slate-100">{{ $score->subject->name }}</span></td>
                    <td><span class="font-medium text-slate-800 dark:text-slate-100">{{ format_number_display($score->value) }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="admin-table-empty">No marks recorded for this exam.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
