<?php

namespace App\Services;

use App\Enums\RevenueSource;
use App\Models\Invoice;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\RevenueRecord;
use App\Models\Setting;
use App\Models\WifiSession;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class RevenueService
{
    /**
     * Successful payment statuses recognised as real money received.
     */
    private const SUCCESSFUL_PAYMENT_STATUSES = ['completed', 'successful'];

    /**
     * Recognised invoice statuses that represent collected revenue.
     */
    private const PAID_INVOICE_STATUSES = ['paid'];

    public function overview(?Organization $organization, $from = null, $to = null): array
    {
        $records = RevenueRecord::query()->forPeriod($from, $to);

        if ($organization) {
            $records->where('organization_id', $organization->id);
        }

        $all = $records->get();

        $gross = (float) $all->sum('gross_amount');
        $fees = (float) $all->sum('payment_fee');
        $net = (float) $all->sum('net_amount');

        $sessions = WifiSession::query();

        if ($organization) {
            $sessions->where('organization_id', $organization->id);
        }

        if ($from && $to) {
            $sessions->whereBetween('session_started_at', [$from.' 00:00:00', $to.' 23:59:59']);
        }

        $sponsoredSessions = (clone $sessions)->whereNotNull('campaign_id')->count();
        $paidSessions = (clone $sessions)->where('auth_method', 'paid')->count();

        $bandwidthBytes = (clone $sessions)->sum('bandwidth_used');
        $bandwidthGb = round($bandwidthBytes / (1024 * 1024 * 1024), 2);

        $costPerGb = (float) Setting::getValue('finance.bandwidth_cost_per_gb', 50, $organization?->id);
        $bandwidthCost = round($bandwidthGb * $costPerGb, 2);

        $paymentFeeRate = (float) Setting::getValue('finance.payment_fee_rate', 1.0, $organization?->id);

        $operatingExpenses = $this->proratedOperatingExpenses($organization, $from, $to);

        $grossMargin = round($gross - $bandwidthCost, 2);
        $ebitda = round($grossMargin - $operatingExpenses, 2);

        return [
            'total_gross' => round($gross, 2),
            'total_fees' => round($fees, 2),
            'total_net' => round($net, 2),
            'effective_fee_rate' => $gross > 0 ? round(($fees / $gross) * 100, 2) : $paymentFeeRate,
            'sponsored_sessions' => $sponsoredSessions,
            'paid_sessions' => $paidSessions,
            'bandwidth_gb' => $bandwidthGb,
            'bandwidth_cost' => $bandwidthCost,
            'bandwidth_cost_per_gb' => $costPerGb,
            'operating_expenses' => $operatingExpenses,
            'gross_margin' => $grossMargin,
            'gross_margin_pct' => $gross > 0 ? round(($grossMargin / $gross) * 100, 1) : 0.0,
            'ebitda' => $ebitda,
            'ebitda_pct' => $gross > 0 ? round(($ebitda / $gross) * 100, 1) : 0.0,
            'recent_records' => $all->sortByDesc('revenue_date')->take(10)->values(),
        ];
    }

    public function monthlySeries(?Organization $organization, int $months = 12): array
    {
        $from = now()->startOfMonth()->subMonths($months - 1)->toDateString();
        $to = now()->endOfMonth()->toDateString();

        $query = RevenueRecord::query()
            ->selectRaw('DATE_FORMAT(revenue_date, "%Y-%m") as month, SUM(gross_amount) as gross, SUM(payment_fee) as fees, SUM(net_amount) as net')
            ->forPeriod($from, $to)
            ->groupBy('month');

        if ($organization) {
            $query->where('organization_id', $organization->id);
        }

        $rows = $query->pluck('gross', 'month');

        $labels = [];
        $gross = [];
        $net = [];

        $cursor = now()->startOfMonth()->subMonths($months - 1);

        for ($i = 0; $i < $months; $i++) {
            $key = $cursor->format('Y-m');
            $labels[] = $cursor->format('M y');
            $gross[] = round((float) ($rows[$key] ?? 0), 2);
            $net[] = round((float) ($rows[$key] ?? 0), 2);
            $cursor->addMonth();
        }

        return ['labels' => $labels, 'gross' => $gross, 'net' => $net];
    }

    public function bySource(?Organization $organization, $from = null, $to = null): Collection
    {
        $query = RevenueRecord::query()->forPeriod($from, $to);

        if ($organization) {
            $query->where('organization_id', $organization->id);
        }

        return $query
            ->selectRaw('source, COUNT(*) as records, SUM(gross_amount) as gross, SUM(payment_fee) as fees, SUM(net_amount) as net')
            ->groupBy('source')
            ->get()
            ->map(function ($row) {
                $source = RevenueSource::tryFrom($row->source) ?? RevenueSource::Advertising;

                return [
                    'source' => $source,
                    'records' => (int) $row->records,
                    'gross' => round((float) $row->gross, 2),
                    'fees' => round((float) $row->fees, 2),
                    'net' => round((float) $row->net, 2),
                ];
            })
            ->sortByDesc('gross')
            ->values();
    }

    public function byOrganization($from = null, $to = null): Collection
    {
        return RevenueRecord::query()
            ->with('organization:id,name')
            ->forPeriod($from, $to)
            ->selectRaw('organization_id, COUNT(*) as records, SUM(gross_amount) as gross, SUM(net_amount) as net')
            ->whereNotNull('organization_id')
            ->groupBy('organization_id')
            ->get()
            ->map(function ($row) {
                return [
                    'organization' => $row->organization,
                    'records' => (int) $row->records,
                    'gross' => round((float) $row->gross, 2),
                    'net' => round((float) $row->net, 2),
                ];
            })
            ->sortByDesc('gross')
            ->values();
    }

    public function byHotspot(?Organization $organization, $from = null, $to = null): Collection
    {
        $query = RevenueRecord::query()
            ->with('hotspot:id,name')
            ->forPeriod($from, $to)
            ->selectRaw('hotspot_id, COUNT(*) as records, SUM(gross_amount) as gross, SUM(net_amount) as net')
            ->whereNotNull('hotspot_id')
            ->groupBy('hotspot_id');

        if ($organization) {
            $query->where('organization_id', $organization->id);
        }

        return $query->get()
            ->map(function ($row) {
                return [
                    'hotspot' => $row->hotspot,
                    'records' => (int) $row->records,
                    'gross' => round((float) $row->gross, 2),
                    'net' => round((float) $row->net, 2),
                ];
            })
            ->sortByDesc('gross')
            ->values();
    }

    public function byCampaign(?Organization $organization, $from = null, $to = null): Collection
    {
        $query = RevenueRecord::query()
            ->with('campaign:id,title')
            ->forPeriod($from, $to)
            ->selectRaw('campaign_id, COUNT(*) as records, SUM(gross_amount) as gross, SUM(net_amount) as net')
            ->whereNotNull('campaign_id')
            ->groupBy('campaign_id');

        if ($organization) {
            $query->where('organization_id', $organization->id);
        }

        return $query->get()
            ->map(function ($row) {
                return [
                    'campaign' => $row->campaign,
                    'records' => (int) $row->records,
                    'gross' => round((float) $row->gross, 2),
                    'net' => round((float) $row->net, 2),
                ];
            })
            ->sortByDesc('gross')
            ->values();
    }

    public function recent(?Organization $organization, int $limit = 10): Collection
    {
        $query = RevenueRecord::query()
            ->with(['organization:id,name', 'hotspot:id,name', 'campaign:id,title'])
            ->latest('revenue_date')
            ->latest('id')
            ->limit($limit);

        if ($organization) {
            $query->where('organization_id', $organization->id);
        }

        return $query->get();
    }

    /**
     * Create a single revenue record (manual adjustment or single-source entry).
     */
    public function record(array $data): RevenueRecord
    {
        $feeRate = (float) Setting::getValue('finance.payment_fee_rate', 1.0, $data['organization_id'] ?? null);
        $gross = round((float) ($data['gross_amount'] ?? 0), 2);
        $fee = round((float) ($data['payment_fee'] ?? ($gross * $feeRate / 100)), 2);

        return RevenueRecord::create([
            'organization_id' => $data['organization_id'] ?? null,
            'wifi_session_id' => $data['wifi_session_id'] ?? null,
            'hotspot_id' => $data['hotspot_id'] ?? null,
            'campaign_id' => $data['campaign_id'] ?? null,
            'sponsorship_id' => $data['sponsorship_id'] ?? null,
            'payment_id' => $data['payment_id'] ?? null,
            'invoice_id' => $data['invoice_id'] ?? null,
            'package_id' => $data['package_id'] ?? null,
            'source' => $data['source'] ?? RevenueSource::Advertising->value,
            'description' => $data['description'] ?? null,
            'gross_amount' => $gross,
            'payment_fee' => $fee,
            'net_amount' => round($gross - $fee, 2),
            'currency' => $data['currency'] ?? 'KES',
            'revenue_date' => $data['revenue_date'] ?? now()->toDateString(),
            'metadata' => $data['metadata'] ?? null,
        ]);
    }

    /**
     * Rebuild the ledger from recognised payments and paid invoices.
     * Manual entries are preserved. Auto-generated rows are replaced.
     */
    public function rebuild(?Organization $organization): array
    {
        $stats = ['payments' => 0, 'invoices' => 0, 'removed' => 0];

        $query = RevenueRecord::query()->where('metadata->auto', true);

        if ($organization) {
            $query->where('organization_id', $organization->id);
        }

        $stats['removed'] = $query->delete();

        $payments = Payment::query()->whereIn('status', self::SUCCESSFUL_PAYMENT_STATUSES);

        $invoices = Invoice::query()->whereIn('status', self::PAID_INVOICE_STATUSES)->with('contract');

        if ($organization) {
            $payments->where('organization_id', $organization->id);
            $invoices->where('organization_id', $organization->id);
        }

        foreach ($payments->get() as $payment) {
            $this->record([
                'organization_id' => $payment->organization_id,
                'payment_id' => $payment->id,
                'sponsorship_id' => $payment->sponsorship_id,
                'source' => RevenueSource::Advertising->value,
                'description' => 'Sponsorship credits payment'.($payment->mpesa_receipt_number ? " — {$payment->mpesa_receipt_number}" : ''),
                'gross_amount' => (float) $payment->amount,
                'revenue_date' => ($payment->transacted_at ?? $payment->created_at)?->toDateString(),
                'metadata' => ['auto' => true, 'kind' => 'payment'],
            ]);

            $stats['payments']++;
        }

        foreach ($invoices->get() as $invoice) {
            $this->record([
                'organization_id' => $invoice->organization_id,
                'invoice_id' => $invoice->id,
                'source' => $this->sourceForContractType($invoice->contract?->type),
                'description' => "Invoice {$invoice->invoice_number} — {$invoice->contract?->title}",
                'gross_amount' => (float) $invoice->total,
                'revenue_date' => ($invoice->paid_at ?? $invoice->issue_date)?->toDateString(),
                'metadata' => ['auto' => true, 'kind' => 'invoice'],
            ]);

            $stats['invoices']++;
        }

        return $stats;
    }

    private function sourceForContractType(?string $type): string
    {
        return match ($type) {
            'county' => RevenueSource::County->value,
            'corporate' => RevenueSource::Corporate->value,
            'ngo' => RevenueSource::Ngo->value,
            'institution', 'institutional' => RevenueSource::Institutional->value,
            'advertiser' => RevenueSource::Advertising->value,
            default => RevenueSource::Corporate->value,
        };
    }

    private function proratedOperatingExpenses(?Organization $organization, $from = null, $to = null): float
    {
        $monthly = (float) Setting::getValue('finance.operating_expenses_monthly', 0, $organization?->id);

        if ($monthly <= 0) {
            return 0.0;
        }

        if (! $from || ! $to) {
            return round($monthly, 2);
        }

        $start = Carbon::parse($from);
        $end = Carbon::parse($to);

        $months = max(1, round($start->diffInDays($end) / 30.4375, 2));

        return round($monthly * $months, 2);
    }
}
