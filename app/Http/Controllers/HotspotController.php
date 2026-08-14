<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasOrganizationScoping;
use App\Models\Campaign;
use App\Models\Hotspot;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HotspotController extends Controller
{
    use HasOrganizationScoping;

    public function index(Request $request): View
    {
        $hotspots = Hotspot::query()
            ->with(['organization:id,name'])
            ->withCount('sessions')
            ->tap(fn ($q) => $this->scopeOrganization($q))
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('hotspots.index', compact('hotspots'));
    }

    public function create(): View
    {
        $organizations = $this->organizationId() ? null : Organization::active()->get();
        $campaigns = Campaign::query()->tap(fn ($q) => $this->scopeOrganization($q))->active()->get();

        return view('hotspots.create', compact('organizations', 'campaigns'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $hotspot = Hotspot::create(array_merge($data, [
            'slug' => Str::slug($data['name']) . '-' . Str::lower(Str::random(4)),
            'organization_id' => $data['organization_id'] ?? $this->organizationId(),
        ]));

        if (! empty($data['campaign_ids'])) {
            $hotspot->campaigns()->sync($data['campaign_ids']);
        }

        return redirect()
            ->route('admin.hotspots.index')
            ->with('success', "Hotspot {$hotspot->name} created.");
    }

    public function show(Hotspot $hotspot): View
    {
        $this->authorizeAccess($hotspot);
        $hotspot->load([
            'organization:id,name',
            'campaigns:id,title,type,status,current_plays',
            'sessions' => fn ($q) => $q->latest('session_started_at')->limit(10),
        ]);

        $stats = [
            'total_sessions' => $hotspot->sessions()->count(),
            'active_sessions' => $hotspot->sessions()->where('status', 'active')->count(),
            'bandwidth_mb' => round($hotspot->sessions()->sum('bandwidth_used') / (1024 * 1024), 1),
            'total_hours' => round($hotspot->sessions()->sum('total_duration') / 3600, 1),
        ];

        return view('hotspots.show', compact('hotspot', 'stats'));
    }

    public function edit(Hotspot $hotspot): View
    {
        $this->authorizeAccess($hotspot);

        $organizations = $this->organizationId() ? null : Organization::active()->get();
        $campaigns = Campaign::query()->tap(fn ($q) => $this->scopeOrganization($q))->active()->get();

        return view('hotspots.edit', compact('hotspot', 'organizations', 'campaigns'));
    }

    public function update(Request $request, Hotspot $hotspot): RedirectResponse
    {
        $this->authorizeAccess($hotspot);

        $data = $this->validated($request, $hotspot);

        $hotspot->update($data);

        if (isset($data['campaign_ids'])) {
            $hotspot->campaigns()->sync($data['campaign_ids']);
        }

        return redirect()
            ->route('admin.hotspots.index')
            ->with('success', "Hotspot {$hotspot->name} updated.");
    }

    public function destroy(Hotspot $hotspot): RedirectResponse
    {
        $this->authorizeAccess($hotspot);

        $name = $hotspot->name;
        $hotspot->delete();

        return redirect()
            ->route('admin.hotspots.index')
            ->with('success', "Hotspot {$name} deleted.");
    }

    private function validated(Request $request, ?Hotspot $hotspot = null): array
    {
        $uniqueRouter = $hotspot
            ? 'unique:hotspots,router_id,' . $hotspot->id
            : 'unique:hotspots,router_id';

        $data = $request->validate([
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'router_id' => ['nullable', 'integer', $uniqueRouter],
            'name' => ['required', 'string', 'max:255'],
            'ssid' => ['nullable', 'string', 'max:255'],
            'device_model' => ['nullable', 'string', 'max:255'],
            'firmware_version' => ['nullable', 'string', 'max:100'],
            'ip_address' => ['nullable', 'ip'],
            'mac_address' => ['nullable', 'string', 'max:17'],
            'isp' => ['nullable', 'string', 'max:255'],
            'bandwidth_up' => ['nullable', 'integer', 'min:0'],
            'bandwidth_down' => ['nullable', 'integer', 'min:0'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'ward' => ['nullable', 'string', 'max:255'],
            'sub_county' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:online,offline,degraded,maintenance'],
            'max_clients' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
            'campaign_ids' => ['nullable', 'array'],
            'campaign_ids.*' => ['exists:campaigns,id'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['last_seen_at'] = now();

        return $data;
    }

    private function authorizeAccess(Hotspot $hotspot): void
    {
        $organizationId = $this->organizationId();

        if ($organizationId && $hotspot->organization_id !== $organizationId) {
            abort(403, 'You do not have access to this hotspot.');
        }
    }
}
