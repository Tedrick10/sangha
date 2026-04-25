<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\Monastery;
use App\Notifications\Monastery\MonasteryAccountDecidedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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
        if ($request->filled('moderation_status')) {
            if ($request->moderation_status === 'approved') {
                $query->where('approved', true);
            } elseif ($request->moderation_status === 'pending') {
                $query->where('approved', false)->whereNull('rejection_reason');
            } elseif ($request->moderation_status === 'rejected') {
                $query->where('approved', false)->whereNotNull('rejection_reason');
            }
        }

        $sortCols = ['name', 'username', 'region', 'city', 'created_at'];
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
        $validated = $request->validate($this->monasteryValidationRules());
        $this->applyMonasteryDefaultsForSuppressedBuiltIns($validated, isCreate: true);
        $validated['is_active'] = true;
        // Admin-created monasteries: approved by default (create form has no moderation UI; public registration stays pending elsewhere).
        $validated['approved'] = true;
        $validated['rejection_reason'] = null;
        unset($validated['moderation_status']);

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
        $validated = $request->validate($this->monasteryValidationRules($monastery));
        $this->applyMonasteryDefaultsForSuppressedBuiltIns($validated, isCreate: false, monastery: $monastery);
        $this->applyModerationState($validated, $request);
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $beforeStatus = $monastery->moderationStatus();
        $monastery->update($validated);
        $monastery->refresh();
        $afterStatus = $monastery->moderationStatus();
        if ($beforeStatus !== $afterStatus && in_array($afterStatus, ['approved', 'rejected'], true)) {
            $actionUrl = route('monastery.dashboard', ['tab' => 'main', 'screen' => 'main-home']);
            $preview = $afterStatus === 'rejected' && filled($monastery->rejection_reason)
                ? Str::limit($monastery->rejection_reason, 160)
                : null;
            $monastery->notify(new MonasteryAccountDecidedNotification(
                $afterStatus,
                $preview,
                $actionUrl,
            ));
        }

        $monastery->setCustomFieldValues($request->input('custom_fields', []), $request);

        return redirect()->route('admin.monasteries.index')->with('success', 'Monastery updated successfully.');
    }

    public function destroy(Monastery $monastery): RedirectResponse
    {
        $monastery->delete();

        return redirect()->route('admin.monasteries.index')->with('success', 'Monastery deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function monasteryValidationRules(?Monastery $monastery = null): array
    {
        $id = $monastery?->id;
        $usernameUnique = Rule::unique('monasteries', 'username')->ignore($id);

        $nameRule = CustomField::isBuiltInSlugSuppressed('monastery', 'name')
            ? 'nullable|string|max:255'
            : 'required|string|max:255';

        $usernameRule = CustomField::isBuiltInSlugSuppressed('monastery', 'username')
            ? ['nullable', 'string', 'max:255', $usernameUnique]
            : ['required', 'string', 'max:255', $usernameUnique];

        $passwordRule = $monastery === null
            ? (CustomField::isBuiltInSlugSuppressed('monastery', 'password')
                ? 'nullable|string|min:8|confirmed'
                : 'required|string|min:8|confirmed')
            : 'nullable|string|min:8|confirmed';

        return [
            'name' => $nameRule,
            'username' => $usernameRule,
            'password' => $passwordRule,
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'region' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'moderation_status' => 'nullable|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string|required_if:moderation_status,rejected|max:2000',
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function applyMonasteryDefaultsForSuppressedBuiltIns(array &$validated, bool $isCreate, ?Monastery $monastery = null): void
    {
        if (CustomField::isBuiltInSlugSuppressed('monastery', 'name')) {
            $n = trim((string) ($validated['name'] ?? ''));
            if ($n === '' && $monastery !== null) {
                $validated['name'] = $monastery->name;
            } elseif ($n === '') {
                $validated['name'] = 'Monastery '.Str::upper(Str::random(6));
            }
        }

        if (CustomField::isBuiltInSlugSuppressed('monastery', 'username')) {
            $u = trim((string) ($validated['username'] ?? ''));
            if ($u === '' && $monastery !== null) {
                $validated['username'] = $monastery->username;
            } elseif ($u === '') {
                do {
                    $u = 'm_'.Str::lower(Str::random(12));
                } while (Monastery::where('username', $u)->exists());
                $validated['username'] = $u;
            }
        }

        if (CustomField::isBuiltInSlugSuppressed('monastery', 'password')) {
            unset($validated['password']);
        }
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
