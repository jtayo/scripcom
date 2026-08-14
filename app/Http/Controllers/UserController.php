<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasOrganizationScoping;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use HasOrganizationScoping;

    public function index(Request $request): View
    {
        $users = User::query()
            ->with(['organization:id,name', 'sponsor:id,name'])
            ->with('roles')
            ->tap(fn ($q) => $this->scopeOrganization($q))
            ->when($request->search, fn ($q, $search) => $q->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $organizations = $this->organizationId() ? null : Organization::active()->get();
        $roles = Role::where('name', '!=', 'Super Admin')->get();

        return view('users.create', compact('organizations', 'roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $user = User::create(array_merge($data, [
            'password' => $data['password'] ?? 'password',
            'email_verified_at' => now(),
        ]));

        if (! empty($data['roles'])) {
            $user->syncRoles($data['roles']);
        } else {
            $user->assignRole('Viewer');
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User {$user->name} created.");
    }

    public function show(User $user): View
    {
        $this->authorizeAccess($user);
        $user->load(['organization:id,name', 'sponsor:id,name', 'roles']);

        return view('users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $this->authorizeAccess($user);

        $organizations = $this->organizationId() ? null : Organization::active()->get();
        $roles = Role::where('name', '!=', 'Super Admin')->get();

        return view('users.edit', compact('user', 'organizations', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAccess($user);

        $data = $this->validated($request, $user);

        if (! empty($data['password'])) {
            $user->update($data);
        } else {
            unset($data['password']);
            $user->update($data);
        }

        if (isset($data['roles']) && $request->user()->isSuperAdmin()) {
            $user->syncRoles($data['roles']);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User {$user->name} updated.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAccess($user);

        if ($request->user()->is($user)) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User {$name} deleted.");
    }

    private function validatePassword(bool $required = false): array
    {
        $rules = ['nullable', 'confirmed', Password::min(8)];

        if ($required) {
            $rules[0] = 'required';
        }

        return $rules;
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $unique = $user ? 'unique:users,email,' . $user->id : 'unique:users,email';

        $data = $request->validate([
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', $unique],
            'phone' => ['nullable', 'string', 'max:30'],
            'status' => ['required', 'in:active,inactive'],
            'password' => $this->validatePassword($user === null),
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,name'],
        ]);

        $data['password'] = $data['password'] ?? null;

        return $data;
    }

    private function authorizeAccess(User $user): void
    {
        $organizationId = $this->organizationId();

        if ($organizationId && $user->organization_id !== $organizationId) {
            abort(403, 'You do not have access to this user.');
        }
    }
}
