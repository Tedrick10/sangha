<?php

namespace App\Http\Controllers\Monastery;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\MonasteryFormRequest;
use App\Models\MonasteryMessage;
use App\Models\Sangha;
use App\Models\Score;
use App\Models\SiteSetting;
use App\Notifications\Admin\NewMonasteryRequestNotification;
use App\Notifications\Admin\NewPendingSanghaNotification;
use App\Support\AdminNotifications;
use App\Support\MonasteryPortalResultsSnapshot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $tab = in_array($request->get('tab'), ['main', 'results', 'exam', 'chat'], true)
            ? $request->get('tab')
            : 'main';

        $allowedScreens = ['main-home', 'results-home', 'exam-home', 'total', 'register', 'pending', 'approved', 'rejected', 'request', 'pass', 'fail', 'chat'];
        if ($tab === 'chat') {
            $screen = 'chat';
        } elseif ($tab === 'exam') {
            $screen = 'exam-home';
        } else {
            $screen = in_array($request->get('screen'), $allowedScreens, true)
                ? $request->get('screen')
                : ($tab === 'results' ? 'results-home' : 'main-home');
        }

        $monastery = Auth::guard('monastery')->user();
        $monastery->loadCount(['sanghas']);

        $totalApplications = $monastery->sanghas_count;
        $pendingCount = $monastery->sanghas()
            ->where('approved', false)
            ->whereNull('rejection_reason')
            ->count();
        $approvedCount = $monastery->sanghas()->where('approved', true)->count();
        $rejectedCount = $monastery->sanghas()
            ->where('approved', false)
            ->whereNotNull('rejection_reason')
            ->count();

        $pendingSanghas = $monastery->sanghas()
            ->with('exam')
            ->where('approved', false)
            ->whereNull('rejection_reason')
            ->latest()
            ->limit(20)
            ->get();
        $approvedSanghas = $monastery->sanghas()
            ->with('exam')
            ->where('approved', true)
            ->latest()
            ->limit(20)
            ->get();
        $rejectedSanghas = $monastery->sanghas()
            ->with('exam')
            ->where('approved', false)
            ->whereNotNull('rejection_reason')
            ->latest()
            ->limit(20)
            ->get();

        $scoresCount = Score::whereHas('sangha', fn ($query) => $query->where('monastery_id', $monastery->id))->count();
        $recentScores = Score::whereHas('sangha', fn ($query) => $query->where('monastery_id', $monastery->id))
            ->with(['sangha:id,name', 'subject:id,name', 'exam:id,name'])
            ->latest()
            ->limit(5)
            ->get();

        $exams = Exam::where('is_active', true)->orderBy('exam_date', 'desc')->orderBy('name')->get();
        $sanghaFieldMeta = CustomField::sanghaDefinitionsBySlug();
        $sanghaCustomFields = CustomField::forEntity('sangha')
            ->where('is_built_in', false)
            ->get();
        if ($screen === 'request' || $tab === 'exam') {
            CustomField::syncBuiltInFieldDefinitions();
        }
        $requestCustomFields = CustomField::forEntity('request')->get();
        $monasteryExamCustomFields = CustomField::forEntity('monastery_exam')->get();
        $monasteryExamApprovedSanghas = $monastery->sanghas()
            ->with('exam:id,name')
            ->where('approved', true)
            ->orderBy('name')
            ->get(['id', 'name', 'username', 'exam_id']);

        $myFormRequests = $monastery->formRequests()->whereNull('exam_type_id')->latest()->limit(25)->get();

        $examTypesCanonical = ExamType::query()
            ->whereIn('name', ExamType::CANONICAL_NAME_ORDER)
            ->orderByCanonical()
            ->get();
        $canonicalExamTypeIds = $examTypesCanonical->pluck('id')->all();
        $examTypeId = (int) $request->get('exam_type_id', 0);
        if ($tab === 'exam') {
            if ($canonicalExamTypeIds === [] || ! in_array($examTypeId, $canonicalExamTypeIds, true)) {
                $examTypeId = (int) ($canonicalExamTypeIds[0] ?? 0);
            }
        } else {
            $examTypeId = 0;
        }
        $myExamFormRequests = $tab === 'exam' && $examTypeId > 0
            ? $monastery->formRequests()->where('exam_type_id', $examTypeId)->latest()->limit(25)->get()
            : collect();

        $monasteryExamCatalogYears = [];
        $monasteryExamCatalogByYear = [];
        if ($tab === 'exam') {
            $catalog = Exam::monasteryExamFormCatalog();
            $monasteryExamCatalogYears = $catalog['years'];
            $monasteryExamCatalogByYear = $catalog['byYear'];
        }

        $resultsRaw = SiteSetting::get(MonasteryPortalResultsSnapshot::key());
        $resultsDecoded = $resultsRaw ? json_decode($resultsRaw, true) : null;
        $resultsBlock = is_array($resultsDecoded)
            ? ($resultsDecoded['monasteries'][(string) $monastery->id] ?? null)
            : null;
        $resultsPublishedAt = is_array($resultsDecoded) ? ($resultsDecoded['generated_at'] ?? null) : null;

        $passSanghas = collect($resultsBlock['pass'] ?? [])->map(fn (array $row) => (object) $row);
        $failSanghas = collect($resultsBlock['fail'] ?? [])->map(fn (array $row) => (object) $row);

        $recentChatMessages = collect();
        if ($tab === 'chat') {
            $recentChatMessages = MonasteryMessage::query()
                ->where('monastery_id', $monastery->id)
                ->with(['user:id,name', 'monastery:id,name'])
                ->orderByDesc('id')
                ->limit(100)
                ->get()
                ->reverse()
                ->values();
        }

        $editingRejectedSangha = null;
        $sanghaEditCustomFieldDefaults = [];
        if ($screen === 'register' && $request->filled('edit')) {
            $editId = (int) $request->query('edit');
            if ($editId > 0) {
                $candidate = $monastery->sanghas()->whereKey($editId)->first();
                if ($candidate
                    && ! $candidate->approved
                    && filled($candidate->rejection_reason)) {
                    $editingRejectedSangha = $candidate->load('exam');
                    foreach ($sanghaCustomFields as $cf) {
                        $sanghaEditCustomFieldDefaults[$cf->slug] = $editingRejectedSangha->getCustomFieldValue($cf->slug);
                    }
                }
            }
        }

        return view('monastery.dashboard', compact(
            'monastery',
            'tab',
            'totalApplications',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'pendingSanghas',
            'approvedSanghas',
            'rejectedSanghas',
            'scoresCount',
            'recentScores',
            'exams',
            'sanghaFieldMeta',
            'sanghaCustomFields',
            'requestCustomFields',
            'monasteryExamCustomFields',
            'monasteryExamApprovedSanghas',
            'myFormRequests',
            'examTypesCanonical',
            'examTypeId',
            'myExamFormRequests',
            'screen',
            'passSanghas',
            'failSanghas',
            'resultsPublishedAt',
            'recentChatMessages',
            'monasteryExamCatalogYears',
            'monasteryExamCatalogByYear',
            'editingRejectedSangha',
            'sanghaEditCustomFieldDefaults'
        ));
    }

    public function storeSangha(Request $request): RedirectResponse
    {
        $monastery = Auth::guard('monastery')->user();
        $customFields = CustomField::forEntity('sangha')
            ->where('is_built_in', false)
            ->get();
        $bySlug = CustomField::sanghaDefinitionsBySlug();

        $validated = $request->validate(array_merge(
            CustomField::sanghaCoreValidationRules($bySlug, ['exam_id', 'name', 'father_name', 'nrc_number', 'description']),
            $this->customFieldRules($customFields, $request)
        ));

        $name = trim((string) ($validated['name'] ?? ''));
        if ($name === '') {
            $name = 'Candidate '.Str::upper(Str::random(8));
        }

        $sangha = Sangha::create([
            'monastery_id' => $monastery->id,
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

        AdminNotifications::notifyAll(new NewPendingSanghaNotification(
            $sangha->name,
            $monastery->name,
            t('notif_source_monastery_portal', 'Monastery portal'),
            route('admin.sanghas.edit', $sangha),
        ));

        return redirect()
            ->route('monastery.dashboard', ['tab' => 'main', 'screen' => 'pending'])
            ->with('success', t('sangha_application_submitted', 'Sangha application submitted successfully. An administrator will assign a Student Id before the candidate can log in.'));
    }

    public function updateRejectedSangha(Request $request, Sangha $sangha): RedirectResponse
    {
        $monastery = Auth::guard('monastery')->user();
        abort_unless((int) $sangha->monastery_id === (int) $monastery->id, 403);
        abort_unless(! $sangha->approved && filled($sangha->rejection_reason), 403);

        $customFields = CustomField::forEntity('sangha')
            ->where('is_built_in', false)
            ->get();
        $bySlug = CustomField::sanghaDefinitionsBySlug();

        $validated = $request->validate(array_merge(
            CustomField::sanghaCoreValidationRules($bySlug, ['exam_id', 'name', 'father_name', 'nrc_number', 'description']),
            $this->customFieldRules($customFields, $request, $sangha)
        ));

        $name = trim((string) ($validated['name'] ?? ''));
        if ($name === '') {
            $name = 'Candidate '.Str::upper(Str::random(8));
        }

        $sangha->update([
            'exam_id' => $validated['exam_id'] ?? null,
            'name' => $name,
            'father_name' => $validated['father_name'] ?? null,
            'nrc_number' => $validated['nrc_number'] ?? null,
            'description' => $validated['description'] ?? null,
            'rejection_reason' => null,
            'approved' => false,
        ]);

        $sangha->setCustomFieldValues($request->input('custom_fields', []), $request);

        AdminNotifications::notifyAll(new NewPendingSanghaNotification(
            $sangha->name,
            $monastery->name,
            t('notif_source_monastery_portal', 'Monastery portal'),
            route('admin.sanghas.edit', $sangha),
        ));

        return redirect()
            ->route('monastery.dashboard', ['tab' => 'main', 'screen' => 'pending'])
            ->with('success', t('sangha_application_resubmitted', 'Application updated and resubmitted. It is pending review again.'));
    }

    public function storeFormRequest(Request $request): RedirectResponse
    {
        $monastery = Auth::guard('monastery')->user();
        CustomField::syncBuiltInFieldDefinitions();
        $requestCustomFields = CustomField::forEntity('request')->get();

        if ($requestCustomFields->isEmpty()) {
            return redirect()
                ->route('monastery.dashboard', ['tab' => 'main', 'screen' => 'request'])
                ->with('error', t('no_request_fields_configured', 'No request form fields are configured yet. Please contact the administrator.'));
        }

        if ($requestCustomFields->firstWhere('slug', 'transfer_from')) {
            $cfIn = $request->input('custom_fields', []);
            if (! isset($cfIn['transfer_from']) || $cfIn['transfer_from'] === '' || $cfIn['transfer_from'] === null) {
                $cfIn['transfer_from'] = $monastery->name;
                $request->merge(['custom_fields' => $cfIn]);
            }
        }

        $request->validate($this->customFieldRules($requestCustomFields, $request));

        if (! $this->portalRequestHasAnyInput($request, $requestCustomFields)) {
            return redirect()
                ->route('monastery.dashboard', ['tab' => 'main', 'screen' => 'request'])
                ->with('error', t('request_form_fill_one', 'Please complete the request form before submitting.'));
        }

        $submission = MonasteryFormRequest::create([
            'monastery_id' => $monastery->id,
            'status' => MonasteryFormRequest::STATUS_PENDING,
        ]);

        $submission->syncRequestFieldValues($request);

        $preview = $submission->fresh()->summaryPreview();
        if ($preview === '—') {
            $preview = t('notif_new_request_submitted', 'New request submitted');
        }

        AdminNotifications::notifyAll(new NewMonasteryRequestNotification(
            $monastery->name,
            Str::limit($preview, 140),
            route('admin.monastery-requests.show', $submission),
        ));

        return redirect()
            ->route('monastery.dashboard', ['tab' => 'main', 'screen' => 'request'])
            ->with('success', t('request_submitted_success', 'Your request was submitted and is pending review.'));
    }

    public function storeExamFormSubmission(Request $request): RedirectResponse
    {
        $monastery = Auth::guard('monastery')->user();
        $allowedExamTypeIds = ExamType::query()
            ->whereIn('name', ExamType::CANONICAL_NAME_ORDER)
            ->pluck('id')
            ->all();

        CustomField::syncBuiltInFieldDefinitions();
        $monasteryExamCustomFields = CustomField::forEntity('monastery_exam')->get();

        if ($monasteryExamCustomFields->isEmpty()) {
            return redirect()
                ->route('monastery.dashboard', ['tab' => 'exam', 'exam_type_id' => $request->input('exam_type_id')])
                ->with('error', t('no_monastery_exam_fields_configured', 'No monastery exam form fields are configured yet. Please contact the administrator.'));
        }

        $request->validate(array_merge(
            ['exam_type_id' => ['required', 'integer', Rule::in($allowedExamTypeIds)]],
            $this->customFieldRules($monasteryExamCustomFields, $request)
        ));

        if (! $this->portalRequestHasAnyInput($request, $monasteryExamCustomFields)) {
            return redirect()
                ->route('monastery.dashboard', ['tab' => 'exam', 'exam_type_id' => $request->integer('exam_type_id')])
                ->with('error', t('exam_form_fill_one', 'Please complete the exam form before submitting.'));
        }

        $resolvedExamTypeId = (int) $request->input('exam_type_id');
        $sessionExamId = $request->input('custom_fields.exam_session');
        if (filled($sessionExamId) && ctype_digit((string) $sessionExamId)) {
            $picked = Exam::query()->find((int) $sessionExamId);
            if ($picked && $picked->exam_type_id !== null && in_array((int) $picked->exam_type_id, $allowedExamTypeIds, true)) {
                $resolvedExamTypeId = (int) $picked->exam_type_id;
            }
        }

        $submission = MonasteryFormRequest::create([
            'monastery_id' => $monastery->id,
            'exam_type_id' => $resolvedExamTypeId,
            'status' => MonasteryFormRequest::STATUS_PENDING,
        ]);

        $submission->syncRequestFieldValues($request);

        $submission = $submission->fresh(['examType']);
        $preview = $submission->summaryPreview();
        if ($preview === '—') {
            $preview = t('notif_new_request_submitted', 'New request submitted');
        }
        if ($submission->examType) {
            $preview = '['.$submission->examType->name.'] '.$preview;
        }

        AdminNotifications::notifyAll(new NewMonasteryRequestNotification(
            $monastery->name,
            Str::limit($preview, 140),
            route('admin.monastery-requests.show', $submission),
        ));

        return redirect()
            ->route('monastery.dashboard', ['tab' => 'exam', 'exam_type_id' => $submission->exam_type_id])
            ->with('success', t('exam_form_submitted_success', 'Exam form submitted and is pending review.'));
    }

    private function portalRequestHasAnyInput(Request $request, Collection $fields): bool
    {
        $values = $request->input('custom_fields', []);

        foreach ($fields as $field) {
            if (in_array($field->type, ['media', 'document', 'video'], true)) {
                if ($request->hasFile('custom_fields.'.$field->slug)) {
                    return true;
                }
            } elseif ($field->type === 'checkbox') {
                if ($request->boolean('custom_fields.'.$field->slug)) {
                    return true;
                }
            } elseif (isset($values[$field->slug]) && filled($values[$field->slug])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function customFieldRules(Collection $fields, Request $request, ?Sangha $resubmitTarget = null): array
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
                    if ($field->entity_type === 'monastery_exam' && $field->slug === 'exam_year') {
                        $years = Exam::monasteryExamFormCatalog()['years'];
                        if ($years !== []) {
                            $fieldRules[] = Rule::in($years);
                        } elseif (! empty($field->options) && is_array($field->options)) {
                            $fieldRules[] = Rule::in(array_values($field->options));
                        }
                    } elseif (! empty($field->options) && is_array($field->options)) {
                        $fieldRules[] = Rule::in(array_values($field->options));
                    }
                    break;
                case 'dependent_select':
                    $parentSlug = CustomField::dependentSelectParentSlug($field);
                    if ($field->entity_type === 'monastery_exam' && $field->slug === 'exam_session') {
                        $fieldRules[] = 'integer';
                        if ($parentSlug) {
                            $fieldRules[] = function (string $attribute, mixed $value, \Closure $fail) use ($request, $parentSlug) {
                                if ($value === null || $value === '') {
                                    return;
                                }
                                $year = $request->input('custom_fields.'.$parentSlug);
                                if (! filled($year)) {
                                    $fail(t('validation_exam_year_before_session', 'Choose a year before selecting an exam.'));

                                    return;
                                }
                                $ok = Exam::query()
                                    ->whereKey((int) $value)
                                    ->where('is_active', true)
                                    ->whereYear('exam_date', (int) $year)
                                    ->exists();
                                if (! $ok) {
                                    $fail(t('validation_exam_session_must_match_year', 'The selected exam is not valid for the chosen year.'));
                                }
                            };
                        }
                    } else {
                        $fieldRules[] = 'string';
                        if ($parentSlug) {
                            $fieldRules[] = function (string $attribute, mixed $value, \Closure $fail) use ($request, $field, $parentSlug) {
                                if ($value === null || $value === '') {
                                    return;
                                }
                                $year = $request->input('custom_fields.'.$parentSlug);
                                if (! filled($year)) {
                                    $fail(t('validation_exam_year_before_session', 'Choose a year before selecting an exam.'));

                                    return;
                                }
                                $map = is_array($field->options) ? $field->options : [];
                                $allowed = $map[(string) $year] ?? [];
                                if (! is_array($allowed)) {
                                    $allowed = [];
                                }
                                $allowedStr = array_map('strval', $allowed);
                                if (! in_array((string) $value, $allowedStr, true)) {
                                    $fail(t('validation_exam_session_must_match_year', 'The selected exam is not valid for the chosen year.'));
                                }
                            };
                        }
                    }
                    break;
                case 'approved_sangha':
                    $fieldRules[] = 'integer';
                    $fieldRules[] = Rule::exists('sanghas', 'id')->where(
                        fn ($q) => $q->where('monastery_id', Auth::guard('monastery')->id())
                            ->where('approved', true)
                    );
                    break;
                case 'media':
                    $hasExistingFile = $resubmitTarget && filled($resubmitTarget->getCustomFieldValue($field->slug));
                    if ($field->required && ! $hasExistingFile) {
                        $fieldRules = ['required'];
                    } else {
                        $fieldRules = ['nullable'];
                    }
                    $fieldRules[] = 'image';
                    $fieldRules[] = 'max:5120';
                    break;
                case 'document':
                    $hasExistingFile = $resubmitTarget && filled($resubmitTarget->getCustomFieldValue($field->slug));
                    if ($field->required && ! $hasExistingFile) {
                        $fieldRules = ['required'];
                    } else {
                        $fieldRules = ['nullable'];
                    }
                    $fieldRules[] = 'file';
                    $fieldRules[] = 'max:51200';
                    break;
                case 'video':
                    $hasExistingFile = $resubmitTarget && filled($resubmitTarget->getCustomFieldValue($field->slug));
                    if ($field->required && ! $hasExistingFile) {
                        $fieldRules = ['required'];
                    } else {
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
}
