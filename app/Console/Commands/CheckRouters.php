<?php

namespace App\Console\Commands;

use App\Models\Router;
use App\Services\MonitoringService;
use Illuminate\Console\Command;

class CheckRouters extends Command
{
    protected $signature = 'routers:check {--router= : Check a single router by ID}';

    protected $description = 'Run a health probe against routers and alert on status changes';

    public function handle(MonitoringService $monitoring): int
    {
        $query = Router::query()->where('is_active', true);

        if ($routerId = $this->option('router')) {
            $query->whereKey($routerId);
        }

        $routers = $query->get();

        if ($routers->isEmpty()) {
            $this->error('No active routers to check.');

            return self::FAILURE;
        }

        $checked = 0;

        foreach ($routers as $router) {
            $log = $monitoring->runCheck($router);
            $checked++;

            $this->line(sprintf(
                '  - %-24s %-9s cpu=%s mem=%s latency=%sms',
                $router->name,
                $log->status,
                $log->cpu_usage !== null ? round($log->cpu_usage, 1).'%' : '-',
                $log->memory_usage !== null ? round($log->memory_usage, 1).'%' : '-',
                $log->latency_ms !== null ? round($log->latency_ms, 1) : '-',
            ));
        }

        $this->newLine();
        $this->info("Done. {$checked} routers checked. Alerts (if any) have been dispatched.");

        return self::SUCCESS;
    }
}
