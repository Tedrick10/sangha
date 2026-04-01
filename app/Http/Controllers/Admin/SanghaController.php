<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\Exam;
use App\Models\Monastery;
use App\Models\Sangha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
                    ->orWhereHas('monastery', fn ($m) => $m->where('name', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('monastery_id')) {
            $query->where('monastery_id', $request->monastery_id);
        }
        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === '1');
        }
        if ($request->filled('approved')) {
            $query->where('approved', $request->approved === '1');
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
        $sortCols = ['name', 'username', 'is_active', 'approved', 'created_at'];
        if ($sort === 'monastery') {
            $query->join('monasteries', 'sanghas.monastery_id', '=', 'monasteries.id')
                ->orderBy('monasteries.name', $order)
                ->select('sanghas.*');
        } elseif ($sort === 'exam') {
            $query->leftJoin('exams', 'sanghas.exam_id', '=', 'exams.id')
                ->orderBy('exams.name', $order)
                ->select('sanghas.*');
        } elseif (in_array($sort, $sortCols)) {
            $query->orderBy('sanghas.' . $sort, $order);
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
        return view('admin.sanghas.create', compact('monasteries', 'exams', 'customFields'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'monastery_id' => 'required|exists:monasteries,id',
            'exam_id' => 'nullable|exists:exams,id',
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:sanghas,username',
            'password' => 'required|string|min:8|confirmed',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'approved' => 'boolean',
            'moderation_status' => 'nullable|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string|required_if:moderation_status,rejected|max:2000',
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $this->applyModerationState($validated, $request);

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
        return view('admin.sanghas.edit', compact('sangha', 'monasteries', 'exams', 'customFields', 'customFieldValues'));
    }

    public function update(Request $request, Sangha $sangha): RedirectResponse
    {
        $validated = $request->validate([
            'monastery_id' => 'required|exists:monasteries,id',
            'exam_id' => 'nullable|exists:exams,id',
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:sanghas,username,' . $sangha->id,
            'password' => 'nullable|string|min:8|confirmed',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'approved' => 'boolean',
            'moderation_status' => 'nullable|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string|required_if:moderation_status,rejected|max:2000',
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $this->applyModerationState($validated, $request);
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $sangha->update($validated);
        $sangha->setCustomFieldValues($request->input('custom_fields', []), $request);
        return redirect()->route('admin.sanghas.index')->with('success', 'Sangha updated successfully.');
    }

    public function destroy(Sangha $sangha): RedirectResponse
    {
        $sangha->delete();
        return redirect()->route('admin.sanghas.index')->with('success', 'Sangha deleted successfully.');
    }

    private function applyModerationState(array &$validated, Request $request): void
    {
        $status = $request->input('moderation_status');
        if (! in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $status = $request->boolean('approved') ? 'approved' : 'pending';
        }

        $validated['approved'] = $status === 'approved';
        $validated['rejection_reason'] = $status === 'rejected'
            ? trim((string) $request->input('rejection_reason'))
            : null;

        unset($validated['moderation_status']);
    }
}
