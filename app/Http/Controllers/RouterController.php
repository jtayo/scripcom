<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasOrganizationScoping;
use App\Models\Hotspot;
use App\Models\Organization;
use App\Models\Router;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RouterController extends Controller
{
    use HasOrganizationScoping;

    public function index(Request $request): View
    {
        $routers = Router::query()
            ->with(['organization:id,name', 'hotspot:id,name'])
            ->tap(fn ($q) => $this->scopeOrganization($q))
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('ip_address', 'like', "%{$search}%"))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('routers.index', compact('routers'));
    }

    public function create(): View
    {
        return view('routers.create', [
            'organizations' => $this->organizationId() ? null : Organization::active()->get(['id', 'name']),
            'hotspots' => Hotspot::query()
                ->tap(fn ($q) => $this->scopeOrganization($q))
                ->orderBy('name')
                ->get(['id', 'name', 'organization_id']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $router = Router::create(array_merge($data, [
            'organization_id' => $data['organization_id'] ?? $this->organizationId(),
            'status' => 'online',
            'is_active' => true,
        ]));

        return redirect()
            ->route('admin.routers.index')
            ->with('success', "Router {$router->name} registered.");
    }

    public function show(Router $router): View
    {
        $this->authorizeAccess($router);

        $router->load([
            'organization:id,name',
            'hotspot:id,name,status',
            'healthLogs.router:id,name,status',
        ]);

        $healthLogs = $router->healthLogs()->paginate(20);

        return view('routers.show', compact('router', 'healthLogs'));
    }

    public function edit(Router $router): View
    {
        $this->authorizeAccess($router);

        return view('routers.edit', [
            'router' => $router,
            'organizations' => $this->organizationId() ? null : Organization::active()->get(['id', 'name']),
            'hotspots' => Hotspot::query()
                ->tap(fn ($q) => $this->scopeOrganization($q))
                ->orderBy('name')
                ->get(['id', 'name', 'organization_id']),
        ]);
    }

    public function update(Request $request, Router $router): RedirectResponse
    {
        $this->authorizeAccess($router);

        $data = $this->validated($request, $router);

        $router->update($data);

        return redirect()
            ->route('admin.routers.index')
            ->with('success', "Router {$router->name} updated.");
    }

    public function destroy(Router $router): RedirectResponse
    {
        $this->authorizeAccess($router);

        $name = $router->name;
        $router->delete();

        return redirect()
            ->route('admin.routers.index')
            ->with('success', "Router {$name} removed.");
    }

    private function validated(Request $request, ?Router $router = null): array
    {
        $data = $request->validate([
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'hotspot_id' => ['nullable', 'exists:hotspots,id'],
            'name' => ['required', 'string', 'max:150'],
            'model' => ['nullable', 'string', 'max:100'],
            'firmware_version' => ['nullable', 'string', 'max:50'],
            'ip_address' => ['nullable', 'ip'],
            'port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'username' => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:online,degraded,offline,maintenance'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['port'] = $data['port'] ?? 8728;

        if (empty($data['status'])) {
            unset($data['status']);
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }

    private function authorizeAccess(Router $router): void
    {
        $organizationId = $this->organizationId();

        if ($organizationId && $router->organization_id !== $organizationId) {
            abort(403, 'You do not have access to this router.');
        }
    }
}
