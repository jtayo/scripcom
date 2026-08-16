<?php

namespace App\Http\Controllers;

use App\Enums\RevenueSource;
use App\Http\Controllers\Traits\HasOrganizationScoping;
use App\Models\RevenueRecord;
use App\Services\RevenueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RevenueController extends Controller
{
    use HasOrganizationScoping;

    public function index(Request $request, RevenueService $revenue): View
    {
        $organization = $this->organization();

        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        $overview = $revenue->overview($organization, $from, $to);
        $series = $revenue->monthlySeries($organization, 12);
        $bySource = $revenue->bySource($organization, $from, $to);
        $byOrganization = $organization ? collect() : $revenue->byOrganization($from, $to);
        $byHotspot = $revenue->byHotspot($organization, $from, $to);
        $byCampaign = $revenue->byCampaign($organization, $from, $to);
        $recent = $revenue->recent($organization, 10);

        return view('revenue.index', compact(
            'organization',
            'overview',
            'series',
            'bySource',
            'byOrganization',
            'byHotspot',
            'byCampaign',
            'recent',
            'from',
            'to',
        ));
    }

    public function store(Request $request, RevenueService $revenue): RedirectResponse
    {
        $data = $request->validate([
            'source' => ['required', 'in:'.implode(',', RevenueSource::values())],
            'description' => ['nullable', 'string', 'max:500'],
            'gross_amount' => ['required', 'numeric', 'min:0'],
            'payment_fee' => ['nullable', 'numeric', 'min:0'],
            'revenue_date' => ['required', 'date'],
            'currency' => ['nullable', 'string', 'size:3'],
        ]);

        $data['organization_id'] = $this->organizationId();

        $revenue->record($data);

        return back()->with('success', 'Revenue entry recorded.');
    }

    public function rebuild(RevenueService $revenue): RedirectResponse
    {
        $stats = $revenue->rebuild($this->organization());

        return back()->with('success', sprintf(
            'Revenue ledger rebuilt: %d payments, %d invoices, %d stale rows removed.',
            $stats['payments'],
            $stats['invoices'],
            $stats['removed'],
        ));
    }

    public function destroy(RevenueRecord $revenueRecord): RedirectResponse
    {
        $revenueRecord->delete();

        return back()->with('success', 'Revenue entry deleted.');
    }
}
