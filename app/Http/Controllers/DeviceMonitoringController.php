<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasOrganizationScoping;
use App\Models\Router;
use App\Services\MonitoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeviceMonitoringController extends Controller
{
    use HasOrganizationScoping;

    public function index(Request $request, MonitoringService $monitoring): View
    {
        $overview = $monitoring->overview();

        $routers = Router::query()
            ->with(['organization:id,name', 'hotspot:id,name', 'healthLogs'])
            ->tap(fn ($q) => $this->scopeOrganization($q))
            ->latest()
            ->get();

        $recentNotifications = $request->user()
            ->notifications()
            ->latest()
            ->limit(8)
            ->get();

        return view('device-monitoring.index', compact('overview', 'routers', 'recentNotifications'));
    }

    public function check(Router $router, MonitoringService $monitoring): RedirectResponse
    {
        $organizationId = $this->organizationId();

        if ($organizationId && $router->organization_id !== $organizationId) {
            abort(403, 'You do not have access to this router.');
        }

        $log = $monitoring->runCheck($router);

        return back()->with('success', sprintf(
            'Health check completed for %s — status: %s.',
            $router->name,
            ucfirst($log->status),
        ));
    }
}
