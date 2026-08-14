<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasOrganizationScoping;
use App\Models\Organization;
use App\Models\Sponsor;
use App\Models\Sponsorship;
use App\Services\MpesaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SponsorshipController extends Controller
{
    use HasOrganizationScoping;

    public function index(Request $request): View
    {
        $sponsorships = Sponsorship::query()
            ->with(['organization:id,name', 'sponsor:id,name'])
            ->tap(fn ($q) => $this->scopeOrganization($q))
            ->when($request->search, fn ($q, $search) => $q->where('reference', 'like', "%{$search}%"))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('sponsorships.index', compact('sponsorships'));
    }

    public function create(): View
    {
        $organizations = $this->organizationId() ? null : Organization::active()->get();
        $sponsors = Sponsor::active()->get();

        return view('sponsorships.create', compact('organizations', 'sponsors'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $data['total_amount'] = $data['quantity_purchased'] * $data['unit_price'];
        $data['reference'] = 'SP-' . strtoupper(Str::random(6));

        $sponsorship = Sponsorship::create(array_merge($data, [
            'organization_id' => $data['organization_id'] ?? $this->organizationId(),
        ]));

        return redirect()
            ->route('admin.sponsorships.index')
            ->with('success', "Sponsorship {$sponsorship->reference} created.");
    }

    public function show(Sponsorship $sponsorship): View
    {
        $this->authorizeAccess($sponsorship);
        $sponsorship->load(['organization:id,name', 'sponsor:id,name', 'sessions' => fn ($q) => $q->latest('session_started_at')->limit(10), 'payments']);

        return view('sponsorships.show', compact('sponsorship'));
    }

    public function edit(Sponsorship $sponsorship): View
    {
        $this->authorizeAccess($sponsorship);

        $organizations = $this->organizationId() ? null : Organization::active()->get();
        $sponsors = Sponsor::active()->get();

        return view('sponsorships.edit', compact('sponsorship', 'organizations', 'sponsors'));
    }

    public function update(Request $request, Sponsorship $sponsorship): RedirectResponse
    {
        $this->authorizeAccess($sponsorship);

        $data = $this->validated($request, $sponsorship);

        $data['total_amount'] = $data['quantity_purchased'] * $data['unit_price'];

        $sponsorship->update($data);

        return redirect()
            ->route('admin.sponsorships.index')
            ->with('success', "Sponsorship {$sponsorship->reference} updated.");
    }

    public function destroy(Sponsorship $sponsorship): RedirectResponse
    {
        $this->authorizeAccess($sponsorship);

        $reference = $sponsorship->reference;
        $sponsorship->delete();

        return redirect()
            ->route('admin.sponsorships.index')
            ->with('success', "Sponsorship {$reference} deleted.");
    }

    private function validated(Request $request, ?Sponsorship $sponsorship = null): array
    {
        $data = $request->validate([
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'sponsor_id' => ['required', 'exists:sponsors,id'],
            'type' => ['required', 'in:sessions,hours,campaign,bandwidth'],
            'quantity_purchased' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],
            'status' => ['required', 'in:pending,active,expired,cancelled'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:starts_at'],
            'notes' => ['nullable', 'string'],
        ]);

        return $data;
    }

    private function authorizeAccess(Sponsorship $sponsorship): void
    {
        $organizationId = $this->organizationId();

        if ($organizationId && $sponsorship->organization_id !== $organizationId) {
            abort(403, 'You do not have access to this sponsorship.');
        }
    }
}
