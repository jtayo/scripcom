<?php

namespace App\Http\Controllers;

use App\Enums\CampaignType;
use App\Http\Controllers\Traits\HasOrganizationScoping;
use App\Models\Campaign;
use App\Models\Hotspot;
use App\Models\Organization;
use App\Models\Sponsor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CampaignController extends Controller
{
    use HasOrganizationScoping;

    public function index(Request $request): View
    {
        $campaigns = Campaign::query()
            ->with(['organization:id,name', 'sponsor:id,name'])
            ->withCount('sessions')
            ->tap(fn ($q) => $this->scopeOrganization($q))
            ->when($request->search, fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('campaigns.index', compact('campaigns'));
    }

    public function create(): View
    {
        $organizations = $this->organizationId() ? null : Organization::active()->get();
        $sponsors = Sponsor::active()->get();
        $hotspots = Hotspot::query()->tap(fn ($q) => $this->scopeOrganization($q))->active()->get();
        $types = CampaignType::cases();

        return view('campaigns.create', compact('organizations', 'sponsors', 'hotspots', 'types'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $campaign = Campaign::create(array_merge(
            Arr::except($data, ['hotspot_ids']),
            [
                'slug' => Str::slug($data['title']) . '-' . Str::lower(Str::random(4)),
                'created_by' => auth()->id(),
                'organization_id' => $data['organization_id'] ?? $this->organizationId(),
            ]
        ));

        if (! empty($data['hotspot_ids'])) {
            $campaign->hotspots()->sync($data['hotspot_ids']);
        }

        return redirect()
            ->route('admin.campaigns.index')
            ->with('success', "Campaign {$campaign->title} created.");
    }

    public function show(Campaign $campaign): View
    {
        $this->authorizeAccess($campaign);
        $campaign->load(['organization:id,name', 'sponsor:id,name', 'creator:id,name', 'hotspots:id,name,router_id,status']);

        $stats = [
            'total_plays' => $campaign->current_plays,
            'total_sessions' => $campaign->sessions()->count(),
            'completions' => $campaign->sessions()->where('video_completed', true)->count(),
            'avg_watch' => round($campaign->sessions()->avg('video_watch_duration') ?? 0, 1),
        ];

        $daily = $campaign->sessions()
            ->selectRaw('DATE(session_started_at) as day, COUNT(*) as total')
            ->where('session_started_at', '>=', now()->subDays(14))
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        return view('campaigns.show', compact('campaign', 'stats', 'daily'));
    }

    public function edit(Campaign $campaign): View
    {
        $this->authorizeAccess($campaign);

        $organizations = $this->organizationId() ? null : Organization::active()->get();
        $sponsors = Sponsor::active()->get();
        $hotspots = Hotspot::query()->tap(fn ($q) => $this->scopeOrganization($q))->active()->get();
        $types = CampaignType::cases();

        return view('campaigns.edit', compact('campaign', 'organizations', 'sponsors', 'hotspots', 'types'));
    }

    public function update(Request $request, Campaign $campaign): RedirectResponse
    {
        $this->authorizeAccess($campaign);

        $data = $this->validated($request, $campaign);

        $campaign->update(Arr::except($data, ['hotspot_ids']));

        if (isset($data['hotspot_ids'])) {
            $campaign->hotspots()->sync($data['hotspot_ids']);
        }

        return redirect()
            ->route('admin.campaigns.index')
            ->with('success', "Campaign {$campaign->title} updated.");
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        $this->authorizeAccess($campaign);

        $title = $campaign->title;
        $campaign->delete();

        return redirect()
            ->route('admin.campaigns.index')
            ->with('success', "Campaign {$title} deleted.");
    }

    private function validated(Request $request, ?Campaign $campaign = null): array
    {
        $data = $request->validate([
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'sponsor_id' => ['nullable', 'exists:sponsors,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:' . implode(',', array_column(CampaignType::cases(), 'value'))],
            'content_type' => ['required', 'in:image,video,html'],
            'content_url' => ['nullable', 'url'],
            'video_caption' => ['nullable', 'string', 'max:255'],
            'thumbnail' => ['nullable', 'string', 'max:255'],
            'redirect_url' => ['nullable', 'url'],
            'duration_seconds' => ['required', 'integer', 'min:1', 'max:600'],
            'skip_allowed' => ['nullable', 'boolean'],
            'is_mandatory' => ['nullable', 'boolean'],
            'priority' => ['nullable', 'integer'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'max_plays' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'in:active,paused,ended,draft'],
            'is_active' => ['nullable', 'boolean'],
            'hotspot_ids' => ['nullable', 'array'],
            'hotspot_ids.*' => ['exists:hotspots,id'],
        ]);

        $data['skip_allowed'] = $request->boolean('skip_allowed');
        $data['is_mandatory'] = $request->boolean('is_mandatory');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function authorizeAccess(Campaign $campaign): void
    {
        $organizationId = $this->organizationId();

        if ($organizationId && $campaign->organization_id !== $organizationId) {
            abort(403, 'You do not have access to this campaign.');
        }
    }
}
