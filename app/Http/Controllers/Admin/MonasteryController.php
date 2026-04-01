<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\Monastery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonasteryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Monastery::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($qry) use ($search) {
                $qry->where('name', 'like', "%{$search}%")
                    ->orWhere('region', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === '1');
        }
        if ($request->filled('approved')) {
            $query->where('approved', $request->approved === '1');
        }
        if ($request->filled('moderation_status')) {
            if ($request->moderation_status === 'approved') {
                $query->where('approved', true);
            } elseif ($request->moderation_status === 'pending') {
                $query->where('approved', false)->whereNull('rejection_reason');
            } elseif ($request->moderation_status === 'rejected') {
                $query->where('approved', false)->whereNotNull('rejection_reason');
            }
        }

        $sortCols = ['name', 'username', 'region', 'city', 'is_active', 'approved', 'created_at'];
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc') === 'asc' ? 'asc' : 'desc';
        if ($sort === 'region_city') {
            $query->orderBy('region', $order)->orderBy('city', $order);
        } elseif (in_array($sort, $sortCols)) {
            $query->orderBy($sort, $order);
        } else {
            $query->latest();
        }
        $monasteries = $query->paginate(admin_per_page(10))->withQueryString();
        return view('admin.monasteries.index', compact('monasteries'));
    }

    public function create(): View
    {
        $customFields = CustomField::forEntity('monastery')->where('is_built_in', false)->get();
        return view('admin.monasteries.create', compact('customFields'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:monasteries,username',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'region' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'approved' => 'boolean',
            'moderation_status' => 'nullable|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string|required_if:moderation_status,rejected|max:2000',
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $this->applyModerationState($validated, $request);

        $monastery = Monastery::create($validated);
        $monastery->setCustomFieldValues($request->input('custom_fields', []), $request);
        return redirect()->route('admin.monasteries.index')->with('success', 'Monastery created successfully.');
    }

    public function edit(Monastery $monastery): View
    {
        $customFields = CustomField::forEntity('monastery')->where('is_built_in', false)->get();
        $customFieldValues = $monastery->getCustomFieldValuesArray();
        return view('admin.monasteries.edit', compact('monastery', 'customFields', 'customFieldValues'));
    }

    public function update(Request $request, Monastery $monastery): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:monasteries,username,' . $monastery->id,
            'password' => 'nullable|string|min:8|confirmed',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'region' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
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

        $monastery->update($validated);
        $monastery->setCustomFieldValues($request->input('custom_fields', []), $request);
        return redirect()->route('admin.monasteries.index')->with('success', 'Monastery updated successfully.');
    }

    public function destroy(Monastery $monastery): RedirectResponse
    {
        $monastery->delete();
        return redirect()->route('admin.monasteries.index')->with('success', 'Monastery deleted successfully.');
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
