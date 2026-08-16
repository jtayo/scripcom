<?php

namespace Database\Seeders;

use App\Models\Hotspot;
use App\Models\Organization;
use App\Models\Router;
use App\Models\RouterHealthLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class RouterSeeder extends Seeder
{
    public function run(): void
    {
        $mombasa = Organization::where('slug', 'mombasa-county')->first();

        if (! $mombasa) {
            return;
        }

        $definitions = [
            ['Fort Jesus Router', 1, '10.10.1.1', 'online', 'RB951Ui-2HnD', '6.49.10'],
            ['Nyali Beach Router', 2, '10.10.1.2', 'degraded', 'hAP ac2', '7.13.4'],
            ['Airport Router', 3, '10.10.1.3', 'online', 'CCR1009-7G', '7.13.5'],
            ['Likoni Ferry Router', 4, '10.10.1.4', 'online', 'hAP lite', '6.48.6'],
            ['City Market Router', 5, '10.10.1.5', 'online', 'RB4011iGS+', '7.13.4'],
            ['Bamburi Beach Router', 6, '10.10.1.6', 'offline', 'hAP ac3', '7.12.1'],
        ];

        foreach ($definitions as [$name, $hotspotIndex, $ip, $status, $model, $firmware]) {
            $hotspot = Hotspot::query()->where('organization_id', $mombasa->id)->orderBy('id')->get()[$hotspotIndex - 1] ?? null;

            $router = Router::firstOrCreate(
                ['name' => $name, 'organization_id' => $mombasa->id],
                [
                    'hotspot_id' => $hotspot?->id,
                    'model' => $model,
                    'firmware_version' => $firmware,
                    'ip_address' => $ip,
                    'port' => 8728,
                    'username' => 'admin',
                    'password' => 'mikrotik-demo-password',
                    'status' => $status,
                    'last_seen_at' => $status === 'offline' ? now()->subMinutes(40) : now()->subMinutes(2),
                    'last_online_at' => $status === 'offline' ? now()->subHours(3) : now()->subMinutes(2),
                    'is_active' => true,
                ]
            );

            if ($router->healthLogs()->exists()) {
                continue;
            }

            $health = $status === 'offline'
                ? [
                    'status' => 'offline',
                    'recorded_at' => now()->subMinutes(40),
                ]
                : [
                    'status' => $status,
                    'cpu_usage' => $status === 'degraded' ? 93.4 : rand(22, 65) + (rand(0, 9) / 10),
                    'memory_usage' => $status === 'degraded' ? 76.2 : rand(30, 70) + (rand(0, 9) / 10),
                    'uptime_seconds' => rand(3, 30) * 86400,
                    'rx_bytes' => rand(2, 20) * 1_000_000_000,
                    'tx_bytes' => rand(1, 10) * 1_000_000_000,
                    'latency_ms' => $status === 'degraded' ? 210.5 : rand(10, 80) + (rand(0, 99) / 100),
                    'active_users' => rand(8, 45),
                    'recorded_at' => now()->subMinutes(2),
                ];

            RouterHealthLog::create(array_merge(['router_id' => $router->id], $health));

            $earlier = array_merge($health, [
                'status' => $status === 'offline' ? 'online' : $status,
                'recorded_at' => Carbon::now()->subMinutes(30),
            ]);

            RouterHealthLog::create(array_merge(['router_id' => $router->id], $earlier));
        }

        $kilifi = Organization::where('slug', 'kilifi-county')->first();

        if ($kilifi) {
            Router::firstOrCreate(
                ['name' => 'Kilifi Gateway Router', 'organization_id' => $kilifi->id],
                [
                    'model' => 'hAP ac2',
                    'firmware_version' => '7.13.4',
                    'ip_address' => '10.20.1.1',
                    'port' => 8728,
                    'username' => 'admin',
                    'password' => 'mikrotik-demo-password',
                    'status' => 'online',
                    'last_seen_at' => now()->subMinutes(1),
                    'last_online_at' => now()->subMinutes(1),
                    'is_active' => true,
                ]
            );
        }
    }
}
