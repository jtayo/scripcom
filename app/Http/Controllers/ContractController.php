<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasOrganizationScoping;
use App\Models\Campaign;
use App\Models\Contract;
use App\Models\Organization;
use App\Models\Sponsor;
use App\Services\BillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContractController extends Controller
{
    use HasOrganizationScoping;

    public function index(Request $request): View
    {
        $contracts = Contract::query()
            ->with(['organization:id,name', 'sponsor:id,name'])
            ->tap(fn ($q) => $this->scopeOrganization($q))
            ->when($request->search, fn ($q, $search) => $q->where('title', 'like', "%{$search}%")
                ->orWhere('contract_number', 'like', "%{$search}%"))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->type, fn ($q, $type) => $q->where('type', $type))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('contracts.index', compact('contracts'));
    }

    public function create(): View
    {
        return view('contracts.create', $this->formOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $contract = Contract::create(array_merge($data, [
            'organization_id' => $data['organization_id'] ?? $this->organizationId(),
            'contract_number' => $this->nextContractNumber(),
        ]));

        $this->syncCampaigns($contract, $request->input('campaign_ids', []));

        return redirect()
            ->route('admin.contracts.show', $contract)
            ->with('success', "Contract {$contract->contract_number} created.");
    }

    public function show(Contract $contract, BillingService $billing): View
    {
        $this->authorizeAccess($contract);

        $contract->load([
            'organization:id,name',
            'sponsor:id,name',
            'campaigns.campaign:id,title,status',
            'invoices.items',
        ]);

        return view('contracts.show', [
            'contract' => $contract,
            'stats' => $billing->contractStats($contract),
        ]);
    }

    public function edit(Contract $contract): View
    {
        $this->authorizeAccess($contract);

        return view('contracts.edit', array_merge(
            ['contract' => $contract],
            $this->formOptions($contract),
        ));
    }

    public function update(Request $request, Contract $contract): RedirectResponse
    {
        $this->authorizeAccess($contract);

        $contract->update($this->validated($request, $contract));

        $this->syncCampaigns($contract, $request->input('campaign_ids', []));

        return redirect()
            ->route('admin.contracts.index')
            ->with('success', "Contract {$contract->contract_number} updated.");
    }

    public function destroy(Contract $contract): RedirectResponse
    {
        $this->authorizeAccess($contract);

        $number = $contract->contract_number;
        $contract->delete();

        return redirect()
            ->route('admin.contracts.index')
            ->with('success', "Contract {$number} deleted.");
    }

    private function syncCampaigns(Contract $contract, array $campaignIds): void
    {
        $contract->campaigns()->delete();

        foreach (array_unique(array_filter($campaignIds)) as $campaignId) {
            $contract->campaigns()->create([
                'campaign_id' => $campaignId,
                'sessions_allocated' => (int) $contract->sessions_allocated,
                'unit_price' => null,
            ]);
        }
    }

    private function formOptions(?Contract $contract = null): array
    {
        $organizationId = $this->organizationId();

        $campaigns = Campaign::query()
            ->tap(fn ($q) => $this->scopeOrganization($q))
            ->orderBy('title')
            ->get(['id', 'title', 'status']);

        $selectedCampaignIds = $contract
            ? $contract->campaigns()->pluck('campaign_id')->all()
            : [];

        return [
            'organizations' => $organizationId ? null : Organization::active()->get(['id', 'name']),
            'sponsors' => Sponsor::query()
                ->when($organizationId, fn ($q) => $q->whereHas('sponsorships', fn ($q) => $q->where('organization_id', $organizationId)))
                ->orderBy('name')
                ->get(['id', 'name']),
            'campaigns' => $campaigns,
            'selectedCampaignIds' => $selectedCampaignIds,
        ];
    }

    private function validated(Request $request, ?Contract $contract = null): array
    {
        $data = $request->validate([
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'sponsor_id' => ['nullable', 'exists:sponsors,id'],
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:county,corporate,advertising'],
            'status' => ['required', 'in:draft,active,completed,cancelled'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'sessions_allocated' => ['required', 'integer', 'min:0'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string'],
            'campaign_ids' => ['nullable', 'array'],
            'campaign_ids.*' => ['integer', 'exists:campaigns,id'],
        ]);

        $data['tax_rate'] = $data['tax_rate'] ?? 16;

        return $data;
    }

    private function nextContractNumber(): string
    {
        $year = now()->format('Y');
        $prefix = 'CTR-'.$year.'-';

        $count = Contract::where('contract_number', 'like', $prefix.'%')
            ->withTrashed()
            ->count() + 1;

        return $prefix.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }

    private function authorizeAccess(Contract $contract): void
    {
        $organizationId = $this->organizationId();

        if ($organizationId && $contract->organization_id !== $organizationId) {
            abort(403, 'You do not have access to this contract.');
        }
    }
}
