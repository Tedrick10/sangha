<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\Exam;
use App\Models\Monastery;
use App\Models\Sangha;
use App\Notifications\Monastery\SanghaApplicationDecidedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SanghaController extends Controller
{
    public function index(Request $request): View
    {
        $query = Sangha::with(['monastery', 'exam']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($qry) use ($search) {
                $qry->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('father_name', 'like', "%{$search}%")
                    ->orWhere('nrc_number', 'like', "%{$search}%")
                    ->orWhereHas('monastery', fn ($m) => $m->where('name', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('monastery_id')) {
            $query->where('monastery_id', $request->monastery_id);
        }
        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }
        if ($request->filled('moderation_status')) {
            if ($request->moderation_status === 'approved') {
                $query->where('sanghas.approved', true);
            } elseif ($request->moderation_status === 'pending') {
                $query->where('sanghas.approved', false)->whereNull('sanghas.rejection_reason');
            } elseif ($request->moderation_status === 'rejected') {
                $query->where('sanghas.approved', false)->whereNotNull('sanghas.rejection_reason');
            }
        }

        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc') === 'asc' ? 'asc' : 'desc';
        $sortCols = ['name', 'username', 'created_at'];
        if ($sort === 'monastery') {
            $query->join('monasteries', 'sanghas.monastery_id', '=', 'monasteries.id')
                ->orderBy('monasteries.name', $order)
                ->select('sanghas.*');
        } elseif ($sort === 'exam') {
            $query->leftJoin('exams', 'sanghas.exam_id', '=', 'exams.id')
                ->orderBy('exams.name', $order)
                ->select('sanghas.*');
        } elseif (in_array($sort, $sortCols)) {
            $query->orderBy('sanghas.'.$sort, $order);
        } else {
            $query->latest();
        }
        $sanghas = $query->paginate(admin_per_page(10))->withQueryString();
        $monasteries = Monastery::orderBy('name')->get();
        $exams = Exam::orderBy('exam_date', 'desc')->orderBy('name')->get();

        return view('admin.sanghas.index', compact('sanghas', 'monasteries', 'exams'));
    }

    public function show(Sangha $sangha): View
    {
        $sangha->load('monastery');
        $exams = Exam::whereHas('scores', fn ($q) => $q->where('sangha_id', $sangha->id))
            ->orderBy('exam_date', 'desc')
            ->get();

        return view('admin.sanghas.show', compact('sangha', 'exams'));
    }

    public function examScores(Sangha $sangha, Exam $exam): View
    {
        $scores = $sangha->scores()
            ->with('subject')
            ->where('exam_id', $exam->id)
            ->orderBy('subject_id')
            ->get();

        return view('admin.sanghas.exam-scores', compact('sangha', 'exam', 'scores'));
    }

    public function create(): View
    {
        $monasteries = Monastery::where('is_active', true)->orderBy('name')->get();
        $exams = Exam::where('is_active', true)->orderBy('exam_date', 'desc')->orderBy('name')->get();
        $customFields = CustomField::forEntity('sangha')->where('is_built_in', false)->get();
        $sanghaFieldMeta = CustomField::sanghaDefinitionsBySlug();

        return view('admin.sanghas.create', compact('monasteries', 'exams', 'customFields', 'sanghaFieldMeta'));
    }

    public function store(Request $request): RedirectResponse
    {
        $bySlug = CustomField::sanghaDefinitionsBySlug();
        $validated = $request->validate(
            CustomField::sanghaCoreValidationRules($bySlug, [
                'monastery_id',
                'exam_id',
                'name',
                'father_name',
                'nrc_number',
                'username',
                'description',
            ], null, 'any')
        );
        if (($validated['username'] ?? null) === '') {
            $validated['username'] = null;
        }
        $validated['is_active'] = true;
        $validated['approved'] = false;
        $validated['rejection_reason'] = null;

        $sangha = Sangha::create($validated);
        $sangha->setCustomFieldValues($request->input('custom_fields', []), $request);

        return redirect()->route('admin.sanghas.index')->with('success', 'Sangha created successfully.');
    }

    public function edit(Sangha $sangha): View
    {
        $monasteries = Monastery::where('is_active', true)->orderBy('name')->get();
        $exams = Exam::where('is_active', true)->orderBy('exam_date', 'desc')->orderBy('name')->get();
        $customFields = CustomField::forEntity('sangha')->where('is_built_in', false)->get();
        $customFieldValues = $sangha->getCustomFieldValuesArray();
        $sanghaFieldMeta = CustomField::sanghaDefinitionsBySlug();

        return view('admin.sanghas.edit', compact('sangha', 'monasteries', 'exams', 'customFields', 'customFieldValues', 'sanghaFieldMeta'));
    }

    public function update(Request $request, Sangha $sangha): RedirectResponse
    {
        $bySlug = CustomField::sanghaDefinitionsBySlug();
        $validated = $request->validate(array_merge(
            CustomField::sanghaCoreValidationRules($bySlug, [
                'monastery_id',
                'exam_id',
                'name',
                'father_name',
                'nrc_number',
                'username',
                'description',
            ], $sangha->id, 'any'),
            [
                'moderation_status' => 'nullable|in:pending,approved,rejected',
                'rejection_reason' => 'nullable|string|required_if:moderation_status,rejected|max:2000',
            ]
        ));
        $this->applyModerationState($validated, $request, $sangha);
        if (($validated['username'] ?? null) === '') {
            $validated['username'] = null;
        }

        $beforeStatus = $sangha->moderationStatus();
        $sangha->update($validated);
        $sangha->refresh();
        $afterStatus = $sangha->moderationStatus();
        if ($beforeStatus !== $afterStatus && in_array($afterStatus, ['approved', 'rejected'], true)) {
            $sangha->load('monastery');
            if ($sangha->monastery) {
                $screen = $afterStatus === 'approved' ? 'approved' : 'rejected';
                $actionUrl = route('monastery.dashboard', ['tab' => 'main', 'screen' => $screen]);
                $preview = $afterStatus === 'rejected' && filled($sangha->rejection_reason)
                    ? Str::limit($sangha->rejection_reason, 160)
                    : null;
                $sangha->monastery->notify(new SanghaApplicationDecidedNotification(
                    $sangha->name,
                    $afterStatus,
                    $preview,
                    $actionUrl,
                ));
            }
        }

        $sangha->setCustomFieldValues($request->input('custom_fields', []), $request);

        return redirect()->route('admin.sanghas.index')->with('success', 'Sangha updated successfully.');
    }

    public function destroy(Sangha $sangha): RedirectResponse
    {
        $sangha->delete();

        return redirect()->route('admin.sanghas.index')->with('success', 'Sangha deleted successfully.');
    }

    private function applyModerationState(array &$validated, Request $request, ?Sangha $existing = null): void
    {
        $status = $request->input('moderation_status');
        if (! in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $status = $existing ? $existing->moderationStatus() : 'pending';
        }

        $validated['approved'] = $status === 'approved';
        $validated['rejection_reason'] = $status === 'rejected'
            ? trim((string) $request->input('rejection_reason'))
            : null;

        unset($validated['moderation_status']);
    }
}
