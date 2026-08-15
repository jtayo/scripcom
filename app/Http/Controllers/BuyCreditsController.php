<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasOrganizationScoping;
use App\Models\Setting;
use App\Models\Sponsorship;
use App\Services\MpesaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BuyCreditsController extends Controller
{
    use HasOrganizationScoping;

    public function index(): View
    {
        $organizationId = $this->organizationId();
        $unitPrice = Setting::getValue('sponsorship.unit_price', 2, $organizationId);
        $minPurchase = Setting::getValue('sponsorship.min_purchase', 100, $organizationId);

        $sponsorships = Sponsorship::query()
            ->with('sponsor:id,name')
            ->tap(fn ($q) => $this->scopeOrganization($q))
            ->where('status', 'active')
            ->latest()
            ->get();

        $balance = Sponsorship::query()
            ->with('sponsor:id,name')
            ->tap(fn ($q) => $this->scopeOrganization($q))
            ->where('status', 'active')
            ->get();

        return view('buy-credits.index', compact('unitPrice', 'minPurchase', 'sponsorships', 'balance'));
    }

    public function store(Request $request, MpesaService $mpesa): RedirectResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'regex:/^(\+?254|0)?[71]\d{8}$/'],
            'amount' => ['required', 'numeric', 'min:1'],
            'sponsorship_id' => ['nullable', 'exists:sponsorships,id'],
        ]);

        $result = $mpesa->stkPush($data['phone'], (float) $data['amount'], $data['sponsorship_id'] ?? null);

        if (! $result['success']) {
            return back()->withInput()->with('error', $result['error'] ?? 'Payment request failed.');
        }

        return redirect()
            ->route('admin.buy-credits')
            ->with('success', $result['simulated'] ?? false
                ? 'Payment simulated successfully (M-Pesa is not configured).'
                : 'STK push sent to ' . $data['phone'] . '. Complete the payment on your phone.');
    }
}
