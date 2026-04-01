<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Role::query()->orderBy('name');
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('name', 'like', "%{$s}%");
        }
        $roles = $query->paginate(admin_per_page(15))->withQueryString();
        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        $permissions = Role::availablePermissions();
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $allowed = Role::allPermissionSlugs();
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => [Rule::in($allowed)],
        ]);
        $permissions = $request->input('permissions', []);
        $permissions = is_array($permissions) ? array_values(array_filter($permissions)) : [];
        Role::create(['name' => $request->input('name'), 'permissions' => $permissions]);
        return redirect()->route('admin.roles.index')->with('success', t('role_created'));
    }

    public function edit(Role $role): View
    {
        $permissions = Role::availablePermissions();
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $allowed = Role::allPermissionSlugs();
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => [Rule::in($allowed)],
        ]);
        $permissions = $request->input('permissions', []);
        $permissions = is_array($permissions) ? array_values(array_filter($permissions)) : [];
        $role->update(['name' => $request->input('name'), 'permissions' => $permissions]);
        return redirect()->route('admin.roles.index')->with('success', t('role_updated'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->users()->exists()) {
            return redirect()->route('admin.roles.index')->with('error', t('role_has_users'));
        }
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', t('role_deleted'));
    }
}
