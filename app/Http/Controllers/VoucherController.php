<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasOrganizationScoping;
use App\Models\Hotspot;
use App\Models\Sponsor;
use App\Models\Sponsorship;
use App\Models\Voucher;
use App\Models\WifiPackage;
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
            ->with(['sponsor:id,name', 'sponsorship:id,reference', 'hotspot:id,name', 'package:id,name,price,duration_minutes'])
            ->tap(fn ($q) => $this->scopeOrganizationVouchers($q))
            ->when($request->search, fn ($q, $search) => $q->where(fn ($q) => $q
                ->where('code', 'like', "%{$search}%")
                ->orWhere('batch_id', 'like', "%{$search}%")))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('vouchers.index', compact('vouchers'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->can('create-voucher'), 403);

        $organizationId = $this->organizationId();

        $sponsors = Sponsor::query()
            ->active()
            ->when($organizationId, fn ($q) => $q->whereHas('sponsorships', fn ($q) => $q->where('organization_id', $organizationId)))
            ->orderBy('name')
            ->get();

        $sponsorships = Sponsorship::query()
            ->when($organizationId, fn ($q) => $q->where('organization_id', $organizationId))
            ->latest()
            ->get(['id', 'reference']);

        $hotspots = Hotspot::query()
            ->tap(fn ($q) => $this->scopeOrganization($q))
            ->active()
            ->orderBy('name')
            ->get();

        $packages = WifiPackage::query()
            ->active()
            ->when($organizationId, fn ($q) => $q->where(fn ($q) => $q
                ->whereNull('organization_id')
                ->orWhere('organization_id', $organizationId)))
            ->orderBy('name')
            ->get();

        return view('vouchers.create', compact('sponsors', 'sponsorships', 'hotspots', 'packages'));
    }

    public function store(Request $request, VoucherService $service): RedirectResponse
    {
        abort_unless(auth()->user()->can('create-voucher'), 403);

        $data = $request->validate([
            'type' => ['required', 'in:sessions,hours,days,bandwidth'],
            'value' => ['required', 'integer', 'min:1'],
            'count' => ['required', 'integer', 'min:1', 'max:1000'],
            'package_id' => ['nullable', 'exists:wifi_packages,id'],
            'hotspot_id' => ['nullable', 'exists:hotspots,id'],
            'sponsor_id' => ['nullable', 'exists:sponsors,id'],
            'sponsorship_id' => ['nullable', 'exists:sponsorships,id'],
            'max_uses' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $result = $service->generateBatch(
            $data['type'],
            $data['value'],
            $data['count'],
            $data['sponsor_id'] ?? null,
            $data['sponsorship_id'] ?? null,
            $data['expires_at'] ? strtotime($data['expires_at']) : null,
            $data['package_id'] ?? null,
            $data['hotspot_id'] ?? null,
            $data['max_uses'] ?? null,
        );

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', "Batch {$result['batch_id']} created with {$result['count']} voucher(s).");
    }

    public function show(Voucher $voucher): View
    {
        $this->authorizeAccess($voucher);

        $voucher->load(['sponsor:id,name', 'sponsorship:id,reference', 'hotspot:id,name', 'package:id,name,price,duration_minutes', 'session:id,session_id,expires_at,status', 'creator:id,name']);

        return view('vouchers.show', compact('voucher'));
    }

    public function destroy(Voucher $voucher): RedirectResponse
    {
        abort_unless(auth()->user()->can('delete-voucher'), 403);

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

        $query->where(function ($q) use ($organizationId) {
            $q->whereHas('hotspot', fn ($q) => $q->where('organization_id', $organizationId))
                ->orWhereHas('sponsorship', fn ($q) => $q->where('organization_id', $organizationId))
                ->orWhereHas('package', fn ($q) => $q->where('organization_id', $organizationId));
        });
    }

    private function authorizeAccess(Voucher $voucher): void
    {
        $organizationId = $this->organizationId();

        if (! $organizationId) {
            return;
        }

        $owned = ($voucher->hotspot_id && $voucher->hotspot->organization_id === $organizationId)
            || ($voucher->sponsorship_id && $voucher->sponsorship->organization_id === $organizationId)
            || ($voucher->package_id && $voucher->package->organization_id === $organizationId);

        if (! $owned) {
            abort(403, 'You do not have access to this voucher.');
        }
    }
}
