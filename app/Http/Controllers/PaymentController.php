<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasOrganizationScoping;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    use HasOrganizationScoping;

    public function index(Request $request): View
    {
        $payments = Payment::query()
            ->with(['organization:id,name', 'sponsorship:id,reference'])
            ->tap(fn ($q) => $this->scopeOrganization($q))
            ->when($request->search, fn ($q, $search) => $q->where('phone', 'like', "%{$search}%")
                ->orWhere('mpesa_receipt_number', 'like', "%{$search}%")
                ->orWhere('checkout_request_id', 'like', "%{$search}%"))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->date, fn ($q, $date) => $q->whereDate('created_at', $date))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('payments.index', compact('payments'));
    }

    public function show(Payment $payment): View
    {
        $this->authorizeAccess($payment);
        $payment->load(['organization:id,name', 'sponsorship:id,reference']);

        return view('payments.show', compact('payment'));
    }

    private function authorizeAccess(Payment $payment): void
    {
        $organizationId = $this->organizationId();

        if ($organizationId && $payment->organization_id !== $organizationId) {
            abort(403, 'You do not have access to this payment.');
        }
    }
}
