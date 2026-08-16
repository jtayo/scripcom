<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Console\Command;

class PruneAuditLogs extends Command
{
    protected $signature = 'audit-logs:prune {--days= : Override the retention period in days}';

    protected $description = 'Delete audit log entries older than the configured retention period';

    public function handle(): int
    {
        $days = (int) ($this->option('days') ?? Setting::getValue('audit.retention_days', 365));

        $deleted = AuditLog::query()
            ->where('created_at', '<', now()->subDays($days))
            ->delete();

        $this->info("Deleted {$deleted} audit log entries older than {$days} days.");

        return self::SUCCESS;
    }
}
