<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\ExamType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamTypeController extends Controller
{
    public function index(Request $request): View
    {
        $query = ExamType::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($qry) use ($search) {
                $qry->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === '1');
        }
        if ($request->filled('approved')) {
            $query->where('approved', $request->approved === '1');
        }

        $sortCols = ['name', 'description', 'is_active', 'approved'];
        $sort = $request->get('sort', 'name');
        $order = $request->get('order', 'asc') === 'asc' ? 'asc' : 'desc';
        if (in_array($sort, $sortCols)) {
            $query->orderBy($sort, $order);
        } else {
            $query->orderBy('name');
        }
        $examTypes = $query->paginate(admin_per_page(10))->withQueryString();
        return view('admin.exam-types.index', compact('examTypes'));
    }

    public function create(): View
    {
        $customFields = CustomField::forEntity('exam_type')->get();
        return view('admin.exam-types.create', compact('customFields'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'approved' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['approved'] = $request->boolean('approved');

        $examType = ExamType::create($validated);
        $examType->setCustomFieldValues($request->input('custom_fields', []), $request);
        return redirect()->route('admin.exam-types.index')->with('success', 'Exam type created successfully.');
    }

    public function edit(ExamType $examType): View
    {
        $customFields = CustomField::forEntity('exam_type')->get();
        $customFieldValues = $examType->getCustomFieldValuesArray();
        return view('admin.exam-types.edit', compact('examType', 'customFields', 'customFieldValues'));
    }

    public function update(Request $request, ExamType $examType): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'approved' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['approved'] = $request->boolean('approved');

        $examType->update($validated);
        $examType->setCustomFieldValues($request->input('custom_fields', []), $request);
        return redirect()->route('admin.exam-types.index')->with('success', 'Exam type updated successfully.');
    }

    public function destroy(ExamType $examType): RedirectResponse
    {
        $examType->delete();
        return redirect()->route('admin.exam-types.index')->with('success', 'Exam type deleted successfully.');
    }
}
