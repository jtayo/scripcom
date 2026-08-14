<?php

namespace App\Http\Controllers;

use App\Enums\SessionStatus;
use App\Http\Controllers\Traits\HasOrganizationScoping;
use App\Models\WifiSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WifiSessionController extends Controller
{
    use HasOrganizationScoping;

    public function index(Request $request): View
    {
        $sessions = WifiSession::query()
            ->with(['hotspot:id,name', 'campaign:id,title'])
            ->tap(fn ($q) => $this->scopeOrganization($q))
            ->when($request->search, fn ($q, $search) => $q->where('phone', 'like', "%{$search}%"))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->date, fn ($q, $date) => $q->whereDate('session_started_at', $date))
            ->latest('session_started_at')
            ->paginate(15)
            ->withQueryString();

        $statuses = SessionStatus::cases();

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
