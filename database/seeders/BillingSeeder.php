<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Contract;
use App\Models\Organization;
use App\Models\Sponsor;
use App\Services\BillingService;
use Illuminate\Database\Seeder;

class BillingSeeder extends Seeder
{
    public function run(): void
    {
        $mombasa = Organization::where('slug', 'mombasa-county')->first();

        if (! $mombasa) {
            return;
        }

        $campaignIds = Campaign::where('organization_id', $mombasa->id)->orderBy('id')->pluck('id');

        $definitions = [
            [
                'contract_number' => 'CTR-2026-0001',
                'title' => 'Mombasa County Community WiFi Program',
                'type' => 'county',
                'sponsor_slug' => null,
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
                'sessions_allocated' => 5000,
                'unit_price' => 20,
                'campaign_ids' => $campaignIds->take(2),
            ],
            [
                'contract_number' => 'CTR-2026-0002',
                'title' => 'Safaricom Sponsored Connectivity',
                'type' => 'advertising',
                'sponsor_slug' => 'safaricom-plc',
                'start_date' => '2026-03-01',
                'end_date' => '2026-12-31',
                'sessions_allocated' => 2000,
                'unit_price' => 25,
                'campaign_ids' => collect([2]),
            ],
            [
                'contract_number' => 'CTR-2026-0003',
                'title' => 'Magical Kenya Tourism Drive',
                'type' => 'advertising',
                'sponsor_slug' => 'kenya-tourism-board',
                'start_date' => '2026-06-01',
                'end_date' => '2026-11-30',
                'sessions_allocated' => 1500,
                'unit_price' => 30,
                'campaign_ids' => collect([1]),
            ],
        ];

        foreach ($definitions as $definition) {
            $sponsor = $definition['sponsor_slug']
                ? Sponsor::where('name', str_replace('-', ' ', ucwords($definition['sponsor_slug'], '-')))->first()
                : null;

            $campaignIds = collect($definition['campaign_ids'] ?? []);
            unset($definition['campaign_ids'], $definition['sponsor_slug']);

            $contract = Contract::firstOrCreate(
                ['contract_number' => $definition['contract_number']],
                array_merge($definition, [
                    'organization_id' => $mombasa->id,
                    'sponsor_id' => $sponsor?->id,
                    'status' => 'active',
                    'tax_rate' => 16,
                ])
            );

            if (! $contract->campaigns()->exists()) {
                foreach ($campaignIds as $campaignId) {
                    $contract->campaigns()->create([
                        'campaign_id' => $campaignId,
                        'sessions_allocated' => $contract->sessions_allocated,
                        'unit_price' => null,
                    ]);
                }
            }
        }

        $this->seedInvoices($mombasa);
    }

    private function seedInvoices(Organization $organization): void
    {
        $contract = Contract::where('contract_number', 'CTR-2026-0002')->first();

        if (! $contract) {
            return;
        }

        $from = now()->startOfMonth()->toDateString();
        $to = now()->toDateString();

        if ($contract->invoices()->where('period_start', '<=', $to)->where('period_end', '>=', $from)->exists()) {
            return;
        }

        try {
            app(BillingService::class)->generateInvoice($contract, $from, $to);
        } catch (\RuntimeException) {
            // Already billed for the period.
        }
    }
}
