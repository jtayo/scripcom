<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request): View
    {
        $roles = Role::query()
            ->withCount(['users', 'permissions'])
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('roles.create', [
            'permissionGroups' => $this->permissionGroups(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $role = Role::create(Arr::except($data, ['permissions']));

        if (! empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return redirect()
            ->route('admin.roles.index')
            ->with('success', "Role {$role->name} created.");
    }

    public function show(Role $role): View
    {
        $role->load('permissions');
        $role->loadCount(['users', 'permissions']);

        return view('roles.show', compact('role'));
    }

    public function edit(Role $role): View
    {
        return view('roles.edit', [
            'role' => $role,
            'permissionGroups' => $this->permissionGroups(),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $this->validated($request, $role);

        $role->update(Arr::except($data, ['permissions']));
        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', "Role {$role->name} updated.");
    }

    public function destroy(Request $request, Role $role): RedirectResponse
    {
        if ($role->name === 'Super Admin') {
            return back()->with('error', 'The Super Admin role cannot be deleted.');
        }

        if ($role->users()->exists()) {
            return back()->with('error', "Role {$role->name} is assigned to users and cannot be deleted.");
        }

        $name = $role->name;
        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', "Role {$name} deleted.");
    }

    private function validated(Request $request, ?Role $role = null): array
    {
        $unique = $role ? "unique:roles,name,{$role->id}" : 'unique:roles,name';

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', $unique],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $data['guard_name'] = 'web';

        return $data;
    }

    private function permissionGroups(): array
    {
        $groups = [];

        foreach (Permission::orderBy('name')->get() as $permission) {
            $group = Str::afterLast($permission->name, '-');
            $group = ucfirst($group === $permission->name ? 'general' : $group);

            $groups[$group][] = $permission;
        }

        ksort($groups);

        return $groups;
    }
}
