<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasOrganizationScoping;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    use HasOrganizationScoping;

    public function index(Request $request): View
    {
        $events = Event::query()
            ->with(['hotspot:id,name', 'campaign:id,title'])
            ->tap(fn ($q) => $this->scopeOrganization($q))
            ->when($request->event_type, fn ($q, $type) => $q->where('event_type', $type))
            ->when($request->date, fn ($q, $date) => $q->whereDate('occurred_at', $date))
            ->latest('occurred_at')
            ->paginate(25)
            ->withQueryString();

        $eventTypes = Event::query()
            ->tap(fn ($q) => $this->scopeOrganization($q))
            ->select('event_type')
            ->distinct()
            ->orderBy('event_type')
            ->pluck('event_type');

        return view('events.index', compact('events', 'eventTypes'));
    }

    public function show(Event $event): View
    {
        $this->authorizeAccess($event);
        $event->load(['hotspot:id,name,router_id', 'campaign:id,title', 'session:id,session_id,phone']);

        return view('events.show', compact('event'));
    }

    private function authorizeAccess(Event $event): void
    {
        $organizationId = $this->organizationId();

        if ($organizationId && $event->organization_id !== $organizationId) {
            abort(403, 'You do not have access to this event.');
        }
    }
}
