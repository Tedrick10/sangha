@extends('admin.layout')

@section('title', 'Sangha: ' . $sangha->name)

@section('content')
<div class="admin-form-page-header">
    <a href="{{ route('admin.sanghas.index') }}" class="admin-back-link">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4 shrink-0']) Sanghas</a>
    <h1 class="admin-page-title">{{ $sangha->name }}</h1>
    <p class="text-slate-600 dark:text-slate-400 mt-1">{{ $sangha->monastery->name }}</p>
</div>

<div class="admin-table-card">
    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-600 bg-gradient-to-r from-slate-50 to-slate-100/80 dark:from-slate-700 dark:to-slate-700/80">
        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Exams taken</h2>
        <p class="text-sm text-slate-600 dark:text-slate-400 mt-0.5">Click an exam to see marks by subject</p>
    </div>
    <table class="admin-table divide-y divide-slate-100">
        <thead>
            <tr>
                <th class="w-12 text-left">No.</th>
                <th>Exam</th>
                <th>Date</th>
                <th>Exam Type</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($exams as $exam)
                <tr>
                    <td class="text-slate-600 dark:text-slate-400">{{ $loop->iteration }}</td>
                    <td><span class="font-semibold text-slate-900 dark:text-slate-100">{{ $exam->name }}</span></td>
                    <td>{{ $exam->exam_date?->format('M d, Y') ?? '—' }}</td>
                    <td>{{ $exam->examType?->name ?? '—' }}</td>
                    <td class="text-right">
                        <a href="{{ route('admin.sanghas.exam-scores', [$sangha, $exam]) }}" class="admin-action-link admin-action-view">@include('partials.icon', ['name' => 'view', 'class' => 'w-4 h-4']) View marks</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="admin-table-empty">No exams with scores yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
