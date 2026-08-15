<?php

namespace App\Http\Controllers;

use App\Enums\SessionStatus;
use App\Http\Controllers\Traits\HasOrganizationScoping;
use App\Models\Campaign;
use App\Models\Hotspot;
use App\Models\WifiSession;
use App\Services\TolclinApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class WifiSessionController extends Controller
{
    use HasOrganizationScoping;

    public function index(Request $request): View
    {
        $from = $request->date ?: now()->subDays(1)->toDateString();
        $to = $request->date ?: now()->toDateString();

        $apiSessions = Cache::remember(
            "tolclin.sessions.{$from}.{$to}",
            now()->addMinutes(5),
            fn () => app(TolclinApiService::class)->sessions($from, $to)
        );

        $filtered = collect($apiSessions)
            ->when($request->search, fn ($c, $search) => $c->filter(
                fn ($s) => str_contains(strtolower($s['mac_address']), strtolower($search))
            ))
            ->when($request->status, fn ($c, $status) => $c->where('status', strtoupper($status)))
            ->values();

        $page = Paginator::resolveCurrentPage();
        $perPage = 15;
        $items = $filtered->slice(($page - 1) * $perPage, $perPage)->values();

        $hotspotByRouter = Hotspot::query()
            ->tap(fn ($q) => $this->scopeOrganization($q))
            ->get(['id', 'router_id', 'name'])
            ->keyBy('router_id');

        $campaign = Campaign::query()
            ->tap(fn ($q) => $this->scopeOrganization($q))
            ->where('status', 'active')
            ->orderBy('id')
            ->first();

        $dbSessions = WifiSession::query()
            ->with(['campaign:id,title', 'hotspot:id,name'])
            ->whereIn('mac_address', $items->pluck('mac_address')->filter()->values())
            ->get()
            ->keyBy(fn ($s) => strtoupper((string) $s->mac_address));

        $rows = $items->map(function (array $session) use ($hotspotByRouter, $campaign, $dbSessions) {
            $mac = (string) $session['mac_address'];
            $db = $dbSessions->get(strtoupper($mac));
            $hotspot = $db?->hotspot ?? $hotspotByRouter->get($session['router_id']);

            return [
                'mac_address' => $mac,
                'status' => $session['status'],
                'router_id' => $session['router_id'],
                'router_name' => $session['router_name'],
                'hotspot' => $hotspot,
                'campaign' => $db?->campaign ?? $campaign,
                'started_at' => $db?->session_started_at,
                'total_duration' => $db?->total_duration,
                'session' => $db,
            ];
        });

        $sessions = new LengthAwarePaginator($rows, $filtered->count(), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]);

        $statuses = array_values(array_filter(
            SessionStatus::cases(),
            fn (SessionStatus $status) => in_array($status->value, ['active', 'expired', 'failed'], true)
        ));

        return view('sessions.index', compact('sessions', 'statuses'));
    }

    public function show(WifiSession $session): View
    {
        $this->authorizeAccess($session);
        $session->load([
            'organization:id,name',
            'hotspot:id,name,router_id,latitude,longitude',
            'campaign:id,title,type',
            'sponsorship:id,reference',
        ]);

        return view('sessions.show', compact('session'));
    }

    public function destroy(WifiSession $session): RedirectResponse
    {
        $this->authorizeAccess($session);

        if ($session->status === 'active') {
            $session->update([
                'status' => 'completed',
                'ended_at' => now(),
                'end_reason' => 'revoked',
            ]);
            $message = 'Session terminated.';
        } else {
            $session->delete();
            $message = 'Session deleted.';
        }

        return redirect()
            ->route('admin.sessions.index')
            ->with('success', $message);
    }

    private function authorizeAccess(WifiSession $session): void
    {
        $organizationId = $this->organizationId();

        if ($organizationId && $session->organization_id !== $organizationId) {
            abort(403, 'You do not have access to this session.');
        }
    }
}
