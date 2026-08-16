<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Services\RevenueService;
use Illuminate\Console\Command;

class RebuildRevenueLedger extends Command
{
    protected $signature = 'revenue:rebuild {--organization= : Optional organization id to scope the rebuild}';

    protected $description = 'Rebuild the revenue ledger from recognised payments and paid invoices';

    public function handle(RevenueService $revenue): int
    {
        $organizationId = $this->option('organization');

        $organization = $organizationId ? Organization::findOrFail((int) $organizationId) : null;

        $stats = $revenue->rebuild($organization);

        $this->info(sprintf(
            'Revenue ledger rebuilt: %d payments, %d invoices, %d stale rows removed%s.',
            $stats['payments'],
            $stats['invoices'],
            $stats['removed'],
            $organization ? " for organization {$organization->name}" : ''
        ));

        return self::SUCCESS;
    }
}
