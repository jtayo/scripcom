<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasOrganizationScoping;
use App\Models\Contract;
use App\Models\Invoice;
use App\Services\BillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    use HasOrganizationScoping;

    public function index(Request $request): View
    {
        $invoices = Invoice::query()
            ->with(['organization:id,name', 'contract:id,title'])
            ->tap(fn ($q) => $this->scopeOrganization($q))
            ->when($request->search, fn ($q, $search) => $q->where('invoice_number', 'like', "%{$search}%")
                ->orWhereHas('contract', fn ($q) => $q->where('title', 'like', "%{$search}%")))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest('issue_date')
            ->paginate(15)
            ->withQueryString();

        return view('invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice): View
    {
        $this->authorizeAccess($invoice);

        $invoice->load([
            'organization:id,name,address,phone,email',
            'contract:id,title,contract_number',
            'items',
        ]);

        return view('invoices.show', compact('invoice'));
    }

    public function generate(Request $request, Contract $contract, BillingService $billing): RedirectResponse
    {
        $this->authorizeContract($contract);

        $data = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        try {
            $invoice = $billing->generateInvoice($contract, $data['from'], $data['to']);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.invoices.show', $invoice)
            ->with('success', "Invoice {$invoice->invoice_number} generated.");
    }

    public function markPaid(Invoice $invoice, BillingService $billing): RedirectResponse
    {
        $this->authorizeAccess($invoice);

        $billing->markPaid($invoice);

        return back()->with('success', "Invoice {$invoice->invoice_number} marked as paid.");
    }

    public function cancel(Invoice $invoice, BillingService $billing): RedirectResponse
    {
        $this->authorizeAccess($invoice);

        $billing->cancel($invoice);

        return back()->with('success', "Invoice {$invoice->invoice_number} cancelled.");
    }

    private function authorizeAccess(Invoice $invoice): void
    {
        $organizationId = $this->organizationId();

        if ($organizationId && $invoice->organization_id !== $organizationId) {
            abort(403, 'You do not have access to this invoice.');
        }
    }

    private function authorizeContract(Contract $contract): void
    {
        $organizationId = $this->organizationId();

        if ($organizationId && $contract->organization_id !== $organizationId) {
            abort(403, 'You do not have access to this contract.');
        }
    }
}
