<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CustomFieldController extends Controller
{
    public function index(Request $request): View
    {
        $this->syncBuiltInFields();

        $query = CustomField::query();
        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }
        $sortCols = ['name', 'type', 'placeholder', 'required', 'sort_order'];
        $sort = $request->get('sort', 'sort_order');
        $order = $request->get('order', 'asc') === 'asc' ? 'asc' : 'desc';
        if (in_array($sort, $sortCols)) {
            $query->orderBy($sort, $order);
        } else {
            $query->orderBy('sort_order')->orderBy('name');
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($qry) use ($search) {
                $qry->where('name', 'like', "%{$search}%")
                    ->orWhere('placeholder', 'like', "%{$search}%");
            });
        }
        $customFields = $query->get();
        $groupedByForm = $customFields->sortBy('sort_order')->groupBy('entity_type');
        return view('admin.custom-fields.index', compact('customFields', 'groupedByForm'));
    }

    protected function syncBuiltInFields(): void
    {
        foreach (CustomField::builtInFields() as $entityType => $fields) {
            $order = 0;
            foreach ($fields as $def) {
                $field = CustomField::firstOrCreate(
                    ['entity_type' => $entityType, 'slug' => $def['slug']],
                    [
                        'name' => $def['name'],
                        'type' => $def['type'],
                        'required' => $def['required'],
                        'placeholder' => $def['placeholder'] ?? null,
                        'sort_order' => $order++,
                        'is_built_in' => true,
                    ]
                );
                if (! $field->wasRecentlyCreated) {
                    $updates = $field->is_built_in ? [] : ['is_built_in' => true];
                    if (($def['placeholder'] ?? null) !== null && empty($field->placeholder)) {
                        $updates['placeholder'] = $def['placeholder'];
                    }
                    if (! empty($updates)) {
                        $field->update($updates);
                    }
                }
            }
        }
    }

    public function create(Request $request): View
    {
        $entityType = $request->get('entity_type', 'monastery');
        return view('admin.custom-fields.create', compact('entityType'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'entity_type' => 'required|in:monastery,sangha,request,exam,exam_type',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'type' => 'required|in:text,textarea,number,date,time,datetime,select,checkbox,media,document,video',
            'options' => 'nullable|array',
            'options.*' => 'nullable|string|max:255',
            'required' => 'boolean',
            'placeholder' => 'nullable|string|max:255',
        ]);
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['required'] = $request->boolean('required');
        $maxOrder = CustomField::where('entity_type', $validated['entity_type'])->max('sort_order') ?? -1;
        $validated['sort_order'] = $maxOrder + 1;
        $validated['options'] = isset($validated['options'])
            ? array_values(array_filter(array_map('trim', $validated['options'])))
            : null;

        $created = CustomField::create($validated);
        return redirect()->route('admin.custom-fields.index', ['entity_type' => $created->entity_type])->with('success', 'Custom field created successfully.');
    }

    public function edit(CustomField $customField): View
    {
        return view('admin.custom-fields.edit', compact('customField'));
    }

    public function update(Request $request, CustomField $customField): RedirectResponse
    {
        $validated = $request->validate([
            'entity_type' => 'required|in:monastery,sangha,request,exam,exam_type',
            'name' => 'required|string|max:255',
            'type' => 'required|in:text,textarea,number,date,time,datetime,select,checkbox,media,document,video',
            'options' => 'nullable|array',
            'options.*' => 'nullable|string|max:255',
            'required' => 'boolean',
            'placeholder' => 'nullable|string|max:255',
        ]);
        $validated['slug'] = $customField->slug;
        $validated['required'] = $request->boolean('required');
        $validated['sort_order'] = $customField->sort_order;
        $validated['options'] = isset($validated['options'])
            ? array_values(array_filter(array_map('trim', $validated['options'])))
            : null;

        $customField->update($validated);
        return redirect()->route('admin.custom-fields.index', ['entity_type' => $customField->entity_type])->with('success', 'Custom field updated successfully.');
    }

    public function destroy(CustomField $customField): RedirectResponse
    {
        $customField->delete();
        return redirect()->route('admin.custom-fields.index', ['entity_type' => $customField->entity_type])->with('success', 'Custom field deleted successfully.');
    }

    public function moveUp(CustomField $customField): RedirectResponse
    {
        $previous = CustomField::where('entity_type', $customField->entity_type)
            ->where('sort_order', '<', $customField->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();

        if ($previous) {
            [$customField->sort_order, $previous->sort_order] = [$previous->sort_order, $customField->sort_order];
            $customField->save();
            $previous->save();
        }

        return redirect()->route('admin.custom-fields.index', ['entity_type' => $customField->entity_type])->with('success', 'Field position updated.');
    }

    public function moveDown(CustomField $customField): RedirectResponse
    {
        $next = CustomField::where('entity_type', $customField->entity_type)
            ->where('sort_order', '>', $customField->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first();

        if ($next) {
            [$customField->sort_order, $next->sort_order] = [$next->sort_order, $customField->sort_order];
            $customField->save();
            $next->save();
        }

        return redirect()->route('admin.custom-fields.index', ['entity_type' => $customField->entity_type])->with('success', 'Field position updated.');
    }

    public function reorder(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:custom_fields,id',
            'entity_type' => 'required|in:monastery,sangha,request,exam,exam_type',
        ]);

        foreach ($request->order as $position => $id) {
            CustomField::where('id', $id)->where('entity_type', $request->entity_type)->update(['sort_order' => $position]);
        }

        return response()->json(['success' => true]);
    }
}
