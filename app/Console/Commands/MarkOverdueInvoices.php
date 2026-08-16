<?php

namespace App\Console\Commands;

use App\Services\BillingService;
use Illuminate\Console\Command;

class MarkOverdueInvoices extends Command
{
    protected $signature = 'billing:mark-overdue';

    protected $description = 'Mark open invoices past their due date as overdue';

    public function handle(BillingService $billing): int
    {
        $count = $billing->markOverdueInvoices();

        $this->info("Done. {$count} invoice(s) marked as overdue.");

        return self::SUCCESS;
    }
}
