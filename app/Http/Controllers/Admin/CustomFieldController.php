<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CustomFieldController extends Controller
{
    /**
     * @return array<int, string>
     */
    private function allowedEntityTypes(): array
    {
        return array_keys(CustomField::entityTypes());
    }

    public function index(Request $request): View
    {
        CustomField::syncBuiltInFieldDefinitions();

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
        $linkedSanghaCoreFieldsByProgramme = collect();

        $activeEntity = $request->get('entity_type');
        $programmeEntities = ['programme_primary', 'programme_intermediate', 'programme_level_1', 'programme_level_2', 'programme_level_3'];
        if (in_array($activeEntity, $programmeEntities, true)) {
            $sanghaCoreSlugs = ['name', 'father_name', 'nrc_number', 'exam_id', 'description'];
            $orderMap = array_flip($sanghaCoreSlugs);
            $linkedSanghaCoreFieldsByProgramme = CustomField::forEntity('sangha')
                ->whereIn('slug', $sanghaCoreSlugs)
                ->get()
                ->sortBy(fn (CustomField $f) => $orderMap[$f->slug] ?? 999)
                ->values();
        }

        return view('admin.custom-fields.index', compact('customFields', 'groupedByForm', 'linkedSanghaCoreFieldsByProgramme'));
    }

    public function create(Request $request): View
    {
        $entityType = $request->get('entity_type', 'monastery');

        return view('admin.custom-fields.create', compact('entityType'));
    }

    public function store(Request $request): RedirectResponse
    {
        $allowedEntityTypes = implode(',', $this->allowedEntityTypes());
        $validated = $request->validate([
            'entity_type' => 'required|in:'.$allowedEntityTypes,
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'type' => 'required|in:text,textarea,number,date,time,datetime,select,dependent_select,checkbox,media,document,video,monastery_select',
            'options' => 'nullable|array',
            'options.*' => 'nullable|string|max:255',
            'required' => 'boolean',
            'placeholder' => 'nullable|string|max:255',
        ]);
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['required'] = $request->boolean('required');
        if (($validated['type'] ?? '') === 'monastery_select' && ($validated['entity_type'] ?? '') !== 'request') {
            return redirect()->back()->withInput()->withErrors([
                'type' => 'Monastery (dropdown) is only supported on the Transfer form (entity type Transfer).',
            ]);
        }
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
        $allowedEntityTypes = implode(',', $this->allowedEntityTypes());
        $rules = [
            'entity_type' => 'required|in:'.$allowedEntityTypes,
            'name' => 'required|string|max:255',
            'type' => 'required|in:text,textarea,number,date,time,datetime,select,dependent_select,checkbox,media,document,video,approved_sangha,monastery_select',
            'options' => 'nullable|array',
            'options.*' => 'nullable|string|max:255',
            'dependent_options_json' => 'nullable|string',
            'required' => 'boolean',
            'placeholder' => 'nullable|string|max:255',
        ];

        $validated = $request->validate($rules);
        $validated['slug'] = $customField->slug;
        $validated['required'] = $request->boolean('required');
        $validated['sort_order'] = $customField->sort_order;

        if ($customField->is_built_in && in_array($customField->slug, ['approved_sangha_id', 'transfer_sangha_id'], true)) {
            $validated['type'] = 'approved_sangha';
            $validated['options'] = null;
        } elseif ($customField->is_built_in && $customField->slug === 'transfer_to') {
            $validated['type'] = 'monastery_select';
            $validated['options'] = null;
        } elseif ($customField->is_built_in && $customField->slug === 'exam_session') {
            $validated['type'] = 'dependent_select';
            $validated['options'] = null;
        } elseif ($customField->is_built_in && $customField->slug === 'exam_year') {
            $validated['options'] = null;
        } else {
            $validated['options'] = isset($validated['options'])
                ? array_values(array_filter(array_map('trim', $validated['options'])))
                : null;
        }

        if (($validated['type'] ?? '') === 'monastery_select' && ($validated['entity_type'] ?? '') !== 'request') {
            return redirect()->back()->withInput()->withErrors([
                'type' => 'Monastery (dropdown) is only supported on the Transfer form (entity type Transfer).',
            ]);
        }

        $customField->update($validated);

        return redirect()->route('admin.custom-fields.index', ['entity_type' => $customField->entity_type])->with('success', 'Custom field updated successfully.');
    }

    public function destroy(CustomField $customField): RedirectResponse
    {
        if (CustomField::builtInDeleteForbidden($customField)) {
            return redirect()
                ->route('admin.custom-fields.index', ['entity_type' => $customField->entity_type])
                ->with('error', 'This built-in field cannot be deleted. You can still edit its label and options.');
        }

        if ($customField->is_built_in) {
            CustomField::suppressBuiltInSlug($customField->entity_type, $customField->slug);
        }

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

    public function reorder(Request $request): JsonResponse
    {
        $allowedEntityTypes = implode(',', $this->allowedEntityTypes());
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:custom_fields,id',
            'entity_type' => 'required|in:'.$allowedEntityTypes,
        ]);

        foreach ($request->order as $position => $id) {
            CustomField::where('id', $id)->where('entity_type', $request->entity_type)->update(['sort_order' => $position]);
        }

        return response()->json(['success' => true]);
    }
}
