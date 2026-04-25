<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\Monastery;
use App\Models\Sangha;
use App\Notifications\Admin\NewMonasteryRegistrationNotification;
use App\Notifications\Admin\NewPendingSanghaNotification;
use App\Support\AdminNotifications;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RegistrationController extends Controller
{
    public function show(): RedirectResponse
    {
        if (Auth::guard('monastery')->check()) {
            return redirect()->route('monastery.dashboard');
        }

        if (Auth::guard('student')->check()) {
            return redirect()->route('sangha.dashboard');
        }

        return redirect()->route('website.login', [
            'type' => request('type') === 'sangha' ? 'sangha' : 'monastery',
            'mode' => 'register',
        ]);
    }

    public function storeMonastery(Request $request): RedirectResponse
    {
        $customFields = CustomField::forEntity('monastery')
            ->where('is_built_in', false)
            ->get();

        $validated = $request->validate(array_merge(
            $this->websiteMonasteryStoreRules(),
            $this->customFieldRules($customFields)
        ));
        $this->applyWebsiteMonasteryDefaults($validated);

        $monastery = Monastery::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => $validated['password'],
            'region' => $validated['region'] ?? null,
            'city' => $validated['city'] ?? null,
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => true,
            'approved' => false,
        ]);

        $monastery->setCustomFieldValues($request->input('custom_fields', []), $request);

        AdminNotifications::notifyAll(new NewMonasteryRegistrationNotification(
            $monastery->name,
            route('admin.monasteries.edit', $monastery),
        ));

        return redirect()
            ->route('website.login', ['type' => 'monastery'])
            ->with('success', 'Monastery registration submitted successfully. You can now log in.');
    }

    public function storeSangha(Request $request): RedirectResponse
    {
        $customFields = CustomField::forEntity('sangha')
            ->where('is_built_in', false)
            ->get();
        $bySlug = CustomField::sanghaDefinitionsBySlug();

        $validated = $request->validate(array_merge(
            CustomField::sanghaCoreValidationRules($bySlug, [
                'monastery_id',
                'exam_id',
                'name',
                'father_name',
                'nrc_number',
                'description',
            ], null, 'active_only'),
            $this->customFieldRules($customFields),
            [
                'monastery_id' => ['required', Rule::exists('monasteries', 'id')->where(fn ($q) => $q->where('is_active', true))],
            ]
        ));

        $name = trim((string) ($validated['name'] ?? ''));
        if ($name === '') {
            $name = 'Candidate '.Str::upper(Str::random(8));
        }

        $sangha = Sangha::create([
            'monastery_id' => $validated['monastery_id'],
            'exam_id' => $validated['exam_id'] ?? null,
            'name' => $name,
            'father_name' => $validated['father_name'] ?? null,
            'nrc_number' => $validated['nrc_number'] ?? null,
            'username' => null,
            'password' => null,
            'description' => $validated['description'] ?? null,
            'is_active' => true,
            'approved' => false,
        ]);

        $sangha->setCustomFieldValues($request->input('custom_fields', []), $request);

        $sangha->load('monastery');
        AdminNotifications::notifyAll(new NewPendingSanghaNotification(
            $sangha->name,
            $sangha->monastery->name,
            t('notif_source_public_site', 'Public registration'),
            route('admin.sanghas.edit', $sangha),
        ));

        return redirect()
            ->route('website.login', ['type' => 'sangha'])
            ->with('success', t('sangha_registered_wait_student_id', 'Registration submitted. An administrator will assign your Student Id; you can log in after it has been set.'));
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function customFieldRules(Collection $fields): array
    {
        $rules = [];

        foreach ($fields as $field) {
            $key = 'custom_fields.'.$field->slug;
            $fieldRules = $field->required ? ['required'] : ['nullable'];

            switch ($field->type) {
                case 'textarea':
                case 'text':
                    $fieldRules[] = 'string';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'time':
                    $fieldRules[] = 'date_format:H:i';
                    break;
                case 'datetime':
                    $fieldRules[] = 'date';
                    break;
                case 'checkbox':
                    $fieldRules[] = 'boolean';
                    break;
                case 'select':
                    $fieldRules[] = 'string';
                    if (! empty($field->options) && is_array($field->options)) {
                        $fieldRules[] = Rule::in($field->options);
                    }
                    break;
                case 'media':
                    if (! $field->required) {
                        $fieldRules = ['nullable'];
                    }
                    $fieldRules[] = 'image';
                    $fieldRules[] = 'max:5120';
                    break;
                case 'document':
                    if (! $field->required) {
                        $fieldRules = ['nullable'];
                    }
                    $fieldRules[] = 'file';
                    $fieldRules[] = 'mimes:pdf,doc,docx,xls,xlsx,txt';
                    $fieldRules[] = 'max:10240';
                    break;
                case 'video':
                    if (! $field->required) {
                        $fieldRules = ['nullable'];
                    }
                    $fieldRules[] = 'file';
                    $fieldRules[] = 'mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-matroska,video/webm';
                    $fieldRules[] = 'max:51200';
                    break;
                default:
                    $fieldRules[] = 'string';
                    break;
            }

            $rules[$key] = $fieldRules;
        }

        return $rules;
    }

    /**
     * @return array<string, mixed>
     */
    private function websiteMonasteryStoreRules(): array
    {
        $usernameUnique = Rule::unique('monasteries', 'username');

        $nameRule = CustomField::isBuiltInSlugSuppressed('monastery', 'name')
            ? 'nullable|string|max:255'
            : 'required|string|max:255';

        $usernameRule = CustomField::isBuiltInSlugSuppressed('monastery', 'username')
            ? ['nullable', 'string', 'max:255', $usernameUnique]
            : ['required', 'string', 'max:255', $usernameUnique];

        $passwordRule = CustomField::isBuiltInSlugSuppressed('monastery', 'password')
            ? 'nullable|string|min:8|confirmed'
            : 'required|string|min:8|confirmed';

        return [
            'name' => $nameRule,
            'username' => $usernameRule,
            'password' => $passwordRule,
            'region' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function applyWebsiteMonasteryDefaults(array &$validated): void
    {
        if (CustomField::isBuiltInSlugSuppressed('monastery', 'name')) {
            $n = trim((string) ($validated['name'] ?? ''));
            if ($n === '') {
                $validated['name'] = 'Monastery '.Str::upper(Str::random(6));
            }
        }

        if (CustomField::isBuiltInSlugSuppressed('monastery', 'username')) {
            $u = trim((string) ($validated['username'] ?? ''));
            if ($u === '') {
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
}
