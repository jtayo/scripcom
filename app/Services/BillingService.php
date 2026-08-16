<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\ContractCampaign;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Organization;
use App\Models\User;
use App\Models\WifiSession;
use App\Notifications\SystemNotice;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BillingService
{
    public function overview(?Organization $organization): array
    {
        $contracts = Contract::query()->with('organization:id,name');
        $invoices = Invoice::query()->with(['organization:id,name', 'contract:id,title']);

        if ($organization) {
            $contracts->where('organization_id', $organization->id);
            $invoices->where('organization_id', $organization->id);
        }

        $allInvoices = (clone $invoices)->where('status', '!=', 'cancelled')->get();
        $openInvoices = $allInvoices->whereIn('status', ['draft', 'sent', 'overdue']);

        $currentMonth = now()->format('Y-m');

        return [
            'total_contracts' => (clone $contracts)->count(),
            'active_contracts' => (clone $contracts)->where('status', 'active')->count(),
            'total_invoices' => $allInvoices->count(),
            'open_invoices' => $openInvoices->count(),
            'invoiced_amount' => round($allInvoices->sum('total'), 2),
            'collected_amount' => round($allInvoices->sum('amount_paid'), 2),
            'outstanding_amount' => round($openInvoices->sum(fn (Invoice $i) => $i->balanceDue()), 2),
            'monthly_revenue' => round($allInvoices
                ->where(fn (Invoice $i) => $i->issue_date?->format('Y-m') === $currentMonth)
                ->sum('total'), 2),
            'recent_contracts' => (clone $contracts)->latest()->limit(5)->get(),
            'recent_invoices' => $allInvoices->sortByDesc('issue_date')->take(5)->values(),
        ];
    }

    public function contractStats(Contract $contract): array
    {
        $sessionsUsed = $contract->sessionsUsed(
            $contract->start_date->toDateString().' 00:00:00',
            $contract->end_date->toDateString().' 23:59:59',
        );

        return [
            'sessions_allocated' => (int) $contract->sessions_allocated,
            'sessions_used' => $sessionsUsed,
            'utilization' => $contract->sessions_allocated > 0
                ? round(min(($sessionsUsed / $contract->sessions_allocated) * 100, 100.0), 1)
                : 0.0,
            'contract_value' => $contract->contractValue(),
        ];
    }

    /**
     * Generate an invoice for a contract covering the given period.
     */
    public function generateInvoice(Contract $contract, string $from, string $to): Invoice
    {
        $fromDate = Carbon::parse($from)->toDateString();
        $toDate = Carbon::parse($to)->toDateString();

        $exists = $contract->invoices()
            ->where('period_start', '<=', $toDate)
            ->where('period_end', '>=', $fromDate)
            ->exists();

        if ($exists) {
            throw new \RuntimeException('An invoice already covers this period.');
        }

        $campaigns = $contract->campaigns()->get();

        $items = [];

        foreach ($campaigns as $contractCampaign) {
            $sessions = $this->sessionsForCampaign($contractCampaign, $fromDate, $toDate);

            if ($sessions <= 0) {
                continue;
            }

            $unitPrice = (float) ($contractCampaign->unit_price ?? $contract->unit_price);

            $items[] = [
                'description' => sprintf(
                    'Sponsored sessions — %s (%s – %s)',
                    $contractCampaign->campaign->title,
                    Carbon::parse($fromDate)->format('d M Y'),
                    Carbon::parse($toDate)->format('d M Y'),
                ),
                'quantity' => $sessions,
                'unit_price' => $unitPrice,
                'amount' => round($sessions * $unitPrice, 2),
            ];
        }

        if ($items === []) {
            $items[] = [
                'description' => sprintf(
                    'Monthly service fee — %s (%s – %s)',
                    $contract->title,
                    Carbon::parse($fromDate)->format('d M Y'),
                    Carbon::parse($toDate)->format('d M Y'),
                ),
                'quantity' => max(1, (int) $contract->sessions_allocated),
                'unit_price' => (float) $contract->unit_price,
                'amount' => round(max(1, (int) $contract->sessions_allocated) * (float) $contract->unit_price, 2),
            ];
        }

        $subtotal = round(array_sum(array_column($items, 'amount')), 2);
        $taxRate = (float) $contract->tax_rate;
        $taxAmount = round($subtotal * ($taxRate / 100), 2);
        $total = round($subtotal + $taxAmount, 2);

        $invoice = DB::transaction(function () use ($contract, $items, $subtotal, $taxRate, $taxAmount, $total, $fromDate, $toDate) {
            $invoice = Invoice::create([
                'organization_id' => $contract->organization_id,
                'contract_id' => $contract->id,
                'invoice_number' => $this->nextInvoiceNumber(),
                'status' => 'draft',
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(14)->toDateString(),
                'period_start' => $fromDate,
                'period_end' => $toDate,
                'subtotal' => $subtotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'amount_paid' => 0,
                'notes' => "Invoice generated for contract {$contract->contract_number}.",
            ]);

            foreach ($items as $item) {
                $invoice->items()->save(new InvoiceItem($item));
            }

            return $invoice;
        });

        $this->notifyBillingUsers($contract, $invoice);

        return $invoice;
    }

    private function sessionsForCampaign(ContractCampaign $contractCampaign, string $fromDate, string $toDate): int
    {
        return WifiSession::query()
            ->where('campaign_id', $contractCampaign->campaign_id)
            ->whereBetween('session_started_at', [$fromDate.' 00:00:00', $toDate.' 23:59:59'])
            ->count();
    }

    public function nextInvoiceNumber(): string
    {
        $year = now()->format('Y');
        $prefix = 'INV-'.$year.'-';

        $count = Invoice::where('invoice_number', 'like', $prefix.'%')
            ->withTrashed()
            ->count();

        do {
            $count++;
            $number = $prefix.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
        } while (Invoice::where('invoice_number', $number)->withTrashed()->exists());

        return $number;
    }

    public function markPaid(Invoice $invoice, ?float $amount = null): Invoice
    {
        $paid = round($amount ?? (float) $invoice->balanceDue(), 2);

        $invoice->update([
            'status' => 'paid',
            'amount_paid' => round((float) $invoice->amount_paid + $paid, 2),
            'paid_at' => now(),
        ]);

        return $invoice;
    }

    public function cancel(Invoice $invoice): Invoice
    {
        $invoice->update(['status' => 'cancelled']);

        return $invoice;
    }

    public function markOverdueInvoices(): int
    {
        $count = Invoice::query()
            ->whereIn('status', ['sent'])
            ->where('due_date', '<', now()->toDateString())
            ->update(['status' => 'overdue']);

        return $count;
    }

    private function notifyBillingUsers(Contract $contract, Invoice $invoice): void
    {
        $recipients = User::query()
            ->where('status', 'active')
            ->when($contract->organization_id, fn ($q) => $q->where('organization_id', $contract->organization_id))
            ->get()
            ->merge(User::role('Super Admin')->where('status', 'active')->get())
            ->unique('id')
            ->values();

        $notification = new SystemNotice(
            'New invoice generated',
            "Invoice {$invoice->invoice_number} of KSh ".number_format((float) $invoice->total, 2)." generated for {$contract->title}.",
            'info',
            route('admin.invoices.show', $invoice),
        );

        foreach ($recipients as $recipient) {
            $recipient->notify($notification);
        }
    }
}
