<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamType;
use App\Models\Role;
use App\Models\TeacherSubjectAssignment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->with('role')->orderBy('name');
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%");
            });
        }
        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }
        $users = $query->paginate(admin_per_page(15))->withQueryString();
        $roles = Role::orderBy('name')->get();
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create(): View
    {
        $roles = Role::orderBy('name')->get();
        $examTypes = ExamType::query()->orderByCanonical()->get(['id', 'name']);
        $examTypeSubjects = $this->examTypeSubjectOptions();

        return view('admin.users.create', compact('roles', 'examTypes', 'examTypeSubjects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id',
            'teacher_scope' => 'nullable|array',
            'teacher_scope.*' => 'nullable|array',
            'teacher_scope.*.*' => 'nullable|integer|exists:subjects,id',
        ]);
        $validated['password'] = Hash::make($validated['password']);
        $scopeInput = $validated['teacher_scope'] ?? [];
        unset($validated['teacher_scope']);

        $user = User::create($validated);
        $this->syncTeacherScope($user, $scopeInput);

        return redirect()->route('admin.users.index')->with('success', t('user_created'));
    }

    public function edit(User $user): View
    {
        $roles = Role::orderBy('name')->get();
        $examTypes = ExamType::query()->orderByCanonical()->get(['id', 'name']);
        $examTypeSubjects = $this->examTypeSubjectOptions();
        $teacherScope = TeacherSubjectAssignment::query()
            ->where('user_id', $user->id)
            ->get(['exam_type_id', 'subject_id'])
            ->groupBy('exam_type_id')
            ->map(fn ($rows) => $rows->pluck('subject_id')->map(fn ($id) => (int) $id)->all())
            ->all();

        return view('admin.users.edit', compact('user', 'roles', 'examTypes', 'examTypeSubjects', 'teacherScope'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id',
            'teacher_scope' => 'nullable|array',
            'teacher_scope.*' => 'nullable|array',
            'teacher_scope.*.*' => 'nullable|integer|exists:subjects,id',
        ]);
        $scopeInput = $validated['teacher_scope'] ?? [];
        unset($validated['teacher_scope']);
        unset($validated['password']);
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }
        $user->update($validated);
        $this->syncTeacherScope($user, $scopeInput);

        return redirect()->route('admin.users.index')->with('success', t('user_updated'));
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', t('user_deleted'));
    }

    /**
     * @param  array<string|int, mixed>  $scopeInput
     */
    private function syncTeacherScope(User $user, array $scopeInput): void
    {
        $user->loadMissing('role');
        if (! $user->isTeacher()) {
            TeacherSubjectAssignment::query()->where('user_id', $user->id)->delete();

            return;
        }

        $examTypeIds = ExamType::query()->pluck('id')->map(fn ($id) => (int) $id)->all();
        $allowedByExamType = [];
        foreach ($this->examTypeSubjectOptions() as $examTypeId => $subjects) {
            $allowedByExamType[(int) $examTypeId] = array_map(fn ($row) => (int) $row['id'], $subjects);
        }
        $examTypeIdSet = array_fill_keys($examTypeIds, true);

        $rows = [];
        foreach ($scopeInput as $examTypeIdRaw => $subjectIdList) {
            $examTypeId = (int) $examTypeIdRaw;
            if (! isset($examTypeIdSet[$examTypeId]) || ! is_array($subjectIdList)) {
                continue;
            }
            $allowedSubjectIdSet = array_fill_keys($allowedByExamType[$examTypeId] ?? [], true);
            foreach ($subjectIdList as $subjectIdRaw) {
                $subjectId = (int) $subjectIdRaw;
                if (! isset($allowedSubjectIdSet[$subjectId])) {
                    continue;
                }
                $rows[$examTypeId.'_'.$subjectId] = [
                    'user_id' => $user->id,
                    'exam_type_id' => $examTypeId,
                    'subject_id' => $subjectId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        TeacherSubjectAssignment::query()->where('user_id', $user->id)->delete();
        if ($rows !== []) {
            TeacherSubjectAssignment::query()->insert(array_values($rows));
        }
    }

    /**
     * @return array<int, list<array{id:int,name:string}>>
     */
    private function examTypeSubjectOptions(): array
    {
        $examTypes = ExamType::query()
            ->with([
                'exams:id,exam_type_id',
                'exams.subjects' => fn ($q) => $q
                    ->where('subjects.is_active', true)
                    ->orderBy('subjects.name')
                    ->select('subjects.id', 'subjects.name'),
            ])
            ->orderByCanonical()
            ->get(['id', 'name']);

        $out = [];
        foreach ($examTypes as $examType) {
            $subjectMap = [];
            foreach ($examType->exams as $exam) {
                foreach ($exam->subjects as $subject) {
                    $sid = (int) $subject->id;
                    $subjectMap[$sid] = [
                        'id' => $sid,
                        'name' => (string) $subject->name,
                    ];
                }
            }
            uasort($subjectMap, fn ($a, $b) => strcmp($a['name'], $b['name']));
            $out[(int) $examType->id] = array_values($subjectMap);
        }

        return $out;
    }
}
