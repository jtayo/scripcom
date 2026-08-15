<?php

namespace App\Console\Commands;

use App\Models\Hotspot;
use App\Models\Organization;
use App\Services\KenyaWardLookup;
use App\Services\TolclinApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SyncTolclinRouters extends Command
{
    protected $signature = 'tolclin:sync-routers {--ids= : Comma-separated router IDs (defaults to TOLCLIN_ROUTER_IDS)}';

    protected $description = 'Fetch live router data from the Tolclin API and update hotspot records';

    public function handle(TolclinApiService $tolclin, KenyaWardLookup $wardLookup): int
    {
        $ids = $this->option('ids')
            ? array_map('intval', explode(',', (string) $this->option('ids')))
            : $tolclin->configuredRouterIds();

        if (empty($ids)) {
            $this->error('No router IDs configured. Set TOLCLIN_ROUTER_IDS or pass --ids=1,2,3');

            return self::FAILURE;
        }

        $this->info('Fetching routers: ' . implode(', ', $ids));

        $routers = collect($tolclin->normalizedRouters());
        if ($routers->isEmpty()) {
            $this->error('No router data returned by the Tolclin API.');

            return self::FAILURE;
        }

        $organization = Organization::find(config('services.tolclin.organization_id'));
        $ssid = config('services.tolclin.ssid');

        $updated = 0;
        $created = 0;
        $withCoords = 0;

        foreach ($routers as $router) {
            $location = $wardLookup->wardFor($router['latitude'], $router['longitude']);

            $data = [
                'name' => $router['name'] ?: 'Hotspot ' . $router['router_id'],
                'latitude' => $router['latitude'],
                'longitude' => $router['longitude'],
                'ward' => $location['ward'] ?? null,
                'sub_county' => $location['sub_county'] ?? null,
                'ssid' => $ssid ?: 'Tolclin-Free-WiFi',
                'status' => $router['status'] ?: 'online',
                'is_active' => true,
                'last_seen_at' => now(),
            ];

            if ($organization) {
                $data['organization_id'] = $organization->id;
            }

            $hotspot = Hotspot::withTrashed()->where('router_id', $router['router_id'])->first();

            if ($hotspot) {
                $hotspot->update($data);
                $hotspot->restore();
                $updated++;
            } else {
                Hotspot::create(array_merge($data, [
                    'router_id' => $router['router_id'],
                    'slug' => Str::slug($data['name']) . '-' . Str::lower(Str::random(4)),
                ]));
                $created++;
            }

            if ($router['latitude'] !== 0.0 && $router['longitude'] !== 0.0) {
                $withCoords++;
            }

            $this->line(sprintf(
                '  - #%s %s (%.5f, %.5f)',
                $router['router_id'],
                $router['name'],
                $router['latitude'],
                $router['longitude']
            ));
        }

        $this->newLine();
        $this->info("Done. {$created} created, {$updated} updated, {$withCoords} with coordinates.");

        return self::SUCCESS;
    }
}
