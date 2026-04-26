<?php

namespace App\Http\Controllers\Monastery;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\Monastery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function edit(): View
    {
        $monastery = Auth::guard('monastery')->user();

        return view('monastery.account', compact('monastery'));
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var Monastery $monastery */
        $monastery = Auth::guard('monastery')->user();

        $validated = $request->validate($this->portalAccountRules($monastery));
        $this->applyPortalMonasteryDefaults($validated, $monastery);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $monastery->update($validated);

        return redirect()
            ->route('monastery.account.edit')
            ->with('success', t('monastery_account_updated', 'Account updated successfully.'));
    }

    /**
     * @return array<string, mixed>
     */
    private function portalAccountRules(Monastery $monastery): array
    {
        $id = $monastery->id;
        $usernameUnique = Rule::unique('monasteries', 'username')->ignore($id);

        $nameRule = CustomField::isBuiltInSlugSuppressed('monastery', 'name')
            ? 'nullable|string|max:255'
            : 'required|string|max:255';

        $usernameRule = CustomField::isBuiltInSlugSuppressed('monastery', 'username')
            ? ['nullable', 'string', 'max:255', $usernameUnique]
            : ['required', 'string', 'max:255', $usernameUnique];

        $passwordRule = CustomField::isBuiltInSlugSuppressed('monastery', 'password')
            ? 'nullable|string|min:8|confirmed'
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
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function applyPortalMonasteryDefaults(array &$validated, Monastery $monastery): void
    {
        if (CustomField::isBuiltInSlugSuppressed('monastery', 'name')) {
            $n = trim((string) ($validated['name'] ?? ''));
            if ($n === '') {
                $validated['name'] = $monastery->name;
            }
        }

        if (CustomField::isBuiltInSlugSuppressed('monastery', 'username')) {
            $u = trim((string) ($validated['username'] ?? ''));
            if ($u === '') {
                $validated['username'] = $monastery->username;
            }
        }

        if (CustomField::isBuiltInSlugSuppressed('monastery', 'password')) {
            unset($validated['password']);
        }
    }
}
