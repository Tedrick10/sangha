<?php

namespace App\Http\Controllers\Monastery;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\Exam;
use App\Models\MonasteryMessage;
use App\Models\Score;
use App\Models\Sangha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $tab = in_array($request->get('tab'), ['main', 'results'], true)
            ? $request->get('tab')
            : 'main';

        $allowedScreens = ['main-home', 'results-home', 'total', 'register', 'pending', 'approved', 'rejected', 'request', 'pass', 'fail'];
        $screen = in_array($request->get('screen'), $allowedScreens, true)
            ? $request->get('screen')
            : ($tab === 'results' ? 'results-home' : 'main-home');

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
        $sanghaCustomFields = CustomField::forEntity('sangha')
            ->where('is_built_in', false)
            ->get();
        $requestCustomFields = CustomField::forEntity('request')
            ->where('is_built_in', false)
            ->get();

        MonasteryMessage::where('monastery_id', $monastery->id)
            ->where('sender_type', 'admin')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = $monastery->messages()
            ->with('user:id,name')
            ->latest()
            ->limit(30)
            ->get()
            ->reverse()
            ->values();

        $passSanghas = Sangha::query()
            ->with('exam')
            ->where('monastery_id', $monastery->id)
            ->where(function ($query) {
                $query->whereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('scores')
                        ->join('subjects', 'subjects.id', '=', 'scores.subject_id')
                        ->whereColumn('scores.sangha_id', 'sanghas.id')
                        ->whereNotNull('subjects.pass_mark')
                        ->whereRaw('scores.value >= subjects.pass_mark');
                })->orWhereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('scores')
                        ->whereColumn('scores.sangha_id', 'sanghas.id')
                        ->where('scores.moderation_decision', 'pass');
                });
            })
            ->orderBy('name')
            ->get();

        $failSanghas = Sangha::query()
            ->with('exam')
            ->where('monastery_id', $monastery->id)
            ->where(function ($query) {
                $query->whereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('scores')
                        ->join('subjects', 'subjects.id', '=', 'scores.subject_id')
                        ->whereColumn('scores.sangha_id', 'sanghas.id')
                        ->whereNotNull('subjects.moderation_mark')
                        ->whereRaw('scores.value < subjects.moderation_mark');
                })->orWhereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('scores')
                        ->whereColumn('scores.sangha_id', 'sanghas.id')
                        ->where('scores.moderation_decision', 'fail');
                });
            })
            ->orderBy('name')
            ->get();

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
            'sanghaCustomFields',
            'requestCustomFields',
            'messages',
            'screen',
            'passSanghas',
            'failSanghas'
        ));
    }

    public function storeSangha(Request $request): RedirectResponse
    {
        $monastery = Auth::guard('monastery')->user();
        $customFields = CustomField::forEntity('sangha')
            ->where('is_built_in', false)
            ->get();

        $validated = $request->validate(array_merge([
            'exam_id' => ['nullable', 'exists:exams,id'],
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:sanghas,username'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'description' => ['nullable', 'string'],
        ], $this->customFieldRules($customFields)));

        $sangha = Sangha::create([
            'monastery_id' => $monastery->id,
            'exam_id' => $validated['exam_id'] ?? null,
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => $validated['password'],
            'description' => $validated['description'] ?? null,
            'is_active' => true,
            'approved' => false,
        ]);

        $sangha->setCustomFieldValues($request->input('custom_fields', []), $request);

        return redirect()
            ->route('monastery.dashboard', ['tab' => 'main', 'screen' => 'pending'])
            ->with('success', 'Sangha application submitted successfully.');
    }

    public function storeMessage(Request $request): RedirectResponse
    {
        $monastery = Auth::guard('monastery')->user();
        $requestCustomFields = CustomField::forEntity('request')
            ->where('is_built_in', false)
            ->get();

        $validated = $request->validate(array_merge([
            'message' => ['nullable', 'string', 'max:3000'],
        ], $this->customFieldRules($requestCustomFields)));

        $customPayload = [];
        foreach ($requestCustomFields as $field) {
            $value = $request->input('custom_fields.' . $field->slug);

            if (in_array($field->type, ['media', 'document', 'video'], true)) {
                $file = $request->file('custom_fields.' . $field->slug);
                if ($file?->isValid()) {
                    $value = $file->store('monastery-requests/' . $monastery->id, 'public');
                }
            }

            if ($field->type === 'checkbox') {
                if (! in_array((string) $value, ['1', 'true'], true)) {
                    continue;
                }
            }

            if (is_array($value)) {
                $value = array_values(array_filter($value, fn ($item) => filled($item)));
                if (empty($value)) {
                    continue;
                }
            } elseif (! filled($value)) {
                continue;
            }

            $customPayload[] = [
                'slug' => $field->slug,
                'label' => $field->name,
                'type' => $field->type,
                'value' => $value,
            ];
        }

        $messageText = trim((string) ($validated['message'] ?? ''));
        if ($messageText === '' && empty($customPayload)) {
            return redirect()
                ->route('monastery.dashboard', ['tab' => 'main', 'screen' => 'request'])
                ->with('error', 'Please fill at least one request field or message.');
        }

        MonasteryMessage::create([
            'monastery_id' => $monastery->id,
            'sender_type' => 'monastery',
            'message' => $messageText === '' ? 'Request form submitted.' : $messageText,
            'payload_json' => empty($customPayload) ? null : $customPayload,
        ]);

        return redirect()
            ->route('monastery.dashboard', ['tab' => 'main', 'screen' => 'request'])
            ->with('success', 'Message sent to Super Admin.');
    }

    /**
     * @return array<string, array<int, string|\Illuminate\Validation\Rules\In>>
     */
    private function customFieldRules(Collection $fields): array
    {
        $rules = [];

        foreach ($fields as $field) {
            $key = 'custom_fields.' . $field->slug;
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
}
