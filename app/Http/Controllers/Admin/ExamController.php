<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function index(Request $request): View
    {
        $query = Exam::with('examType');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($qry) use ($search) {
                $qry->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }
        if ($request->filled('exam_type_id')) {
            $query->where('exam_type_id', $request->exam_type_id);
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === '1');
        }
        if ($request->filled('approved')) {
            $query->where('approved', $request->approved === '1');
        }

        $sortCols = ['name', 'exam_date', 'is_active', 'approved'];
        $sort = $request->get('sort', 'exam_date');
        $order = $request->get('order', 'desc') === 'asc' ? 'asc' : 'desc';
        if ($sort === 'exam_type_location') {
            $query->leftJoin('exam_types', 'exams.exam_type_id', '=', 'exam_types.id')
                ->orderByRaw('COALESCE(exam_types.name, exams.location) ' . ($order === 'asc' ? 'ASC' : 'DESC'))
                ->select('exams.*');
        } elseif (in_array($sort, $sortCols)) {
            $query->orderBy($sort, $order);
        } else {
            $query->latest('exam_date');
        }
        $exams = $query->paginate(admin_per_page(10))->withQueryString();
        $examTypes = ExamType::orderBy('name')->get();
        return view('admin.exams.index', compact('exams', 'examTypes'));
    }

    public function create(): View
    {
        $examTypes = ExamType::where('is_active', true)->orderBy('name')->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $customFields = CustomField::forEntity('exam')->where('is_built_in', false)->get();
        return view('admin.exams.create', compact('examTypes', 'subjects', 'customFields'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exam_date' => 'nullable|date',
            'exam_type_id' => 'nullable|exists:exam_types,id',
            'location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'approved' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['approved'] = $request->boolean('approved');

        $exam = Exam::create($validated);
        $exam->subjects()->sync($request->input('subjects', []));
        $exam->setCustomFieldValues($request->input('custom_fields', []), $request);
        return redirect()->route('admin.exams.index')->with('success', 'Exam created successfully.');
    }

    public function edit(Exam $exam): View
    {
        $exam->load('subjects');
        $examTypes = ExamType::where('is_active', true)->orderBy('name')->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $customFields = CustomField::forEntity('exam')->where('is_built_in', false)->get();
        $customFieldValues = $exam->getCustomFieldValuesArray();
        return view('admin.exams.edit', compact('exam', 'examTypes', 'subjects', 'customFields', 'customFieldValues'));
    }

    public function update(Request $request, Exam $exam): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exam_date' => 'nullable|date',
            'exam_type_id' => 'nullable|exists:exam_types,id',
            'location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'approved' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['approved'] = $request->boolean('approved');

        $exam->update($validated);
        $exam->subjects()->sync($request->input('subjects', []));
        $exam->setCustomFieldValues($request->input('custom_fields', []), $request);
        return redirect()->route('admin.exams.index')->with('success', 'Exam updated successfully.');
    }

    public function destroy(Exam $exam): RedirectResponse
    {
        $exam->delete();
        return redirect()->route('admin.exams.index')->with('success', 'Exam deleted successfully.');
    }
}
