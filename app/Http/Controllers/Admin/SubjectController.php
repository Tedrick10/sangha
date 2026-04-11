<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(Request $request): View
    {
        $query = Subject::query();

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

        $sortCols = ['name', 'description', 'moderation_mark', 'full_mark', 'pass_mark', 'is_active'];
        $sort = $request->get('sort', 'name');
        $order = $request->get('order', 'asc') === 'asc' ? 'asc' : 'desc';
        if (in_array($sort, $sortCols)) {
            $query->orderBy($sort, $order);
        } else {
            $query->orderBy('name');
        }
        $subjects = $query->paginate(admin_per_page(10))->withQueryString();

        return view('admin.subjects.index', compact('subjects'));
    }

    public function create(): View
    {
        return view('admin.subjects.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('subjects', 'name')],
            'description' => 'nullable|string',
            'moderation_mark' => 'nullable|numeric|min:0',
            'full_mark' => 'nullable|numeric|min:0',
            'pass_mark' => 'nullable|numeric|min:0|lte:full_mark',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        Subject::create($validated);

        return redirect()->route('admin.subjects.index')->with('success', 'Subject created successfully.');
    }

    public function edit(Subject $subject): View
    {
        return view('admin.subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('subjects', 'name')->ignore($subject->id)],
            'description' => 'nullable|string',
            'moderation_mark' => 'nullable|numeric|min:0',
            'full_mark' => 'nullable|numeric|min:0',
            'pass_mark' => 'nullable|numeric|min:0|lte:full_mark',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        $subject->update($validated);

        return redirect()->route('admin.subjects.index')->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();

        return redirect()->route('admin.subjects.index')->with('success', 'Subject deleted successfully.');
    }
}
