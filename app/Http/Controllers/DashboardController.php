<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasOrganizationScoping;
use App\Models\Campaign;
use App\Models\Event;
use App\Models\Hotspot;
use App\Models\Sponsorship;
use App\Models\WifiSession;
use App\Services\AnalyticsService;
use App\Services\EventService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    use HasOrganizationScoping;

    public function index(AnalyticsService $analytics, EventService $eventService): View
    {
        $organization = $this->organization();
        $stats = $analytics->dashboardStats($organization);
        $sessionsByHour = $analytics->sessionsByHour($organization);
        $sessionsPerDay = $analytics->sessionsPerDay($organization, 14);
        $campaigns = $analytics->campaignsPerformance($organization, 5);
        $topHotspots = $analytics->topHotspots($organization, 5);
        $recentEvents = $eventService->getEvents($organization, 8);
        $recentSessions = WifiSession::query()
            ->when($organization, fn ($q) => $q->where('organization_id', $organization->id))
            ->with(['hotspot:id,name', 'campaign:id,title'])
            ->latest('session_started_at')
            ->limit(8)
            ->get();

        $campaignCount = Campaign::query()->tap(fn ($q) => $this->scopeOrganization($q))->count();
        $activeSponsorships = Sponsorship::query()->tap(fn ($q) => $this->scopeOrganization($q))->where('status', 'active')->count();
        $eventCount = Event::query()->tap(fn ($q) => $this->scopeOrganization($q))->count();

        return view('dashboard', compact(
            'stats',
            'sessionsByHour',
            'sessionsPerDay',
            'campaigns',
            'topHotspots',
            'recentEvents',
            'recentSessions',
            'campaignCount',
            'activeSponsorships',
            'eventCount',
            'organization'
        ));
    }
}
