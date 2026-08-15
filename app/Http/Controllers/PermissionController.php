<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(Request $request): View
    {
        $permissions = Permission::query()
            ->withCount('roles')
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('permissions.index', compact('permissions'));
    }

    public function show(Permission $permission): View
    {
        $permission->loadCount('roles');
        $permission->load('roles');

        return view('permissions.show', compact('permission'));
    }

    public static function groupOf(Permission $permission): string
    {
        $group = Str::afterLast($permission->name, '-');

        return ucfirst($group === $permission->name ? 'general' : $group);
    }
}
