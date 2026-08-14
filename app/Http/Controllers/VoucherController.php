<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasOrganizationScoping;
use App\Models\Hotspot;
use App\Models\Sponsor;
use App\Models\Voucher;
use App\Services\VoucherService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VoucherController extends Controller
{
    use HasOrganizationScoping;

    public function index(Request $request): View
    {
        $vouchers = Voucher::query()
            ->with(['sponsor:id,name', 'sponsorship:id,reference'])
            ->tap(fn ($q) => $this->scopeOrganizationVouchers($q))
            ->when($request->search, fn ($q, $search) => $q->where('code', 'like', "%{$search}%")->orWhere('batch_id', 'like', "%{$search}%"))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('vouchers.index', compact('vouchers'));
    }

    public function create(): View
    {
        $sponsors = Sponsor::active()->get();
        $hotspots = Hotspot::query()->tap(fn ($q) => $this->scopeOrganization($q))->active()->get();

        return view('vouchers.create', compact('sponsors', 'hotspots'));
    }

    public function store(Request $request, VoucherService $service): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:sessions,hours,bandwidth'],
            'value' => ['required', 'integer', 'min:1'],
            'count' => ['required', 'integer', 'min:1', 'max:1000'],
            'sponsor_id' => ['nullable', 'exists:sponsors,id'],
            'sponsorship_id' => ['nullable', 'exists:sponsorships,id'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $result = $service->generateBatch(
            $data['type'],
            $data['value'],
            $data['count'],
            $data['sponsor_id'] ?? null,
            $data['sponsorship_id'] ?? null,
            $data['expires_at'] ?? null
        );

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', "Batch {$result['batch_id']} created with {$result['count']} voucher(s).");
    }

    public function show(Voucher $voucher): View
    {
        $this->authorizeAccess($voucher);
        $voucher->load(['sponsor:id,name', 'sponsorship:id,reference', 'hotspot:id,name', 'session:id,session_id']);

        return view('vouchers.show', compact('voucher'));
    }

    public function destroy(Voucher $voucher): RedirectResponse
    {
        $this->authorizeAccess($voucher);

        $code = $voucher->code;
        $voucher->delete();

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', "Voucher {$code} deleted.");
    }

    private function scopeOrganizationVouchers($query): void
    {
        $organizationId = $this->organizationId();

        if (! $organizationId) {
            return;
        }

        $query->whereHas('sponsorship', fn ($q) => $q->where('organization_id', $organizationId));
    }

    private function authorizeAccess(Voucher $voucher): void
    {
        $organizationId = $this->organizationId();

        if (! $organizationId) {
            return;
        }

        if ($voucher->sponsorship_id && $voucher->sponsorship->organization_id !== $organizationId) {
            abort(403, 'You do not have access to this voucher.');
        }
    }
}
