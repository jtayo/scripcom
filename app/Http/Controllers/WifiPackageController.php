<?php

namespace App\Http\Controllers;

use App\Enums\PackageAccessType;
use App\Http\Controllers\Traits\HasOrganizationScoping;
use App\Models\Organization;
use App\Models\WifiPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WifiPackageController extends Controller
{
    use HasOrganizationScoping;

    public function index(Request $request): View
    {
        $packages = WifiPackage::query()
            ->with('organization:id,name')
            ->tap(fn ($q) => $this->scopeOrganization($q))
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%"))
            ->when($request->access_type, fn ($q, $type) => $q->where('access_type', $type))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('packages.index', compact('packages'));
    }

    public function create(): View
    {
        $organizations = $this->organizationId() ? null : Organization::active()->get();

        return view('packages.create', compact('organizations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $package = WifiPackage::create(array_merge($data, [
            'organization_id' => $data['organization_id'] ?? $this->organizationId(),
        ]));

        return redirect()
            ->route('admin.packages.index')
            ->with('success', "Package {$package->name} created.");
    }

    public function show(WifiPackage $package): View
    {
        $this->authorizeAccess($package);
        $package->load([
            'organization:id,name',
            'sessions' => fn ($q) => $q->with('hotspot:id,name')->latest('session_started_at')->limit(10),
        ]);

        return view('packages.show', compact('package'));
    }

    public function edit(WifiPackage $package): View
    {
        $this->authorizeAccess($package);

        $organizations = $this->organizationId() ? null : Organization::active()->get();

        return view('packages.edit', compact('package', 'organizations'));
    }

    public function update(Request $request, WifiPackage $package): RedirectResponse
    {
        $this->authorizeAccess($package);

        $package->update($this->validated($request, $package));

        return redirect()
            ->route('admin.packages.index')
            ->with('success', "Package {$package->name} updated.");
    }

    public function destroy(WifiPackage $package): RedirectResponse
    {
        $this->authorizeAccess($package);

        $name = $package->name;
        $package->delete();

        return redirect()
            ->route('admin.packages.index')
            ->with('success', "Package {$name} deleted.");
    }

    private function validated(Request $request, ?WifiPackage $package = null): array
    {
        $data = $request->validate([
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50', 'unique:wifi_packages,code,'.($package?->id ?: 'NULL')],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'access_type' => ['required', 'in:'.implode(',', array_column(PackageAccessType::cases(), 'value'))],
            'bandwidth_down_kbps' => ['nullable', 'integer', 'min:0'],
            'bandwidth_up_kbps' => ['nullable', 'integer', 'min:0'],
            'data_limit_mb' => ['nullable', 'integer', 'min:0'],
            'simultaneous_devices' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ]);

        return $data;
    }

    private function authorizeAccess(WifiPackage $package): void
    {
        $organizationId = $this->organizationId();

        if ($organizationId && $package->organization_id !== $organizationId) {
            abort(403, 'You do not have access to this package.');
        }
    }
}
