<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use RuntimeException;

/**
 * Reverse-geocode a lat/lng point into an IEBC ward using the Kenya ward
 * boundary dataset (kenya_county_assemblies.geojson, EPSG:4326).
 */
class KenyaWardLookup
{
    private const CACHE_KEY = 'kenya_wards_geojson_v1';

    private ?array $features = null;

    /**
     * Look up the ward containing the given point.
     *
     * @return array{ward: string, sub_county: string, county: string}|null
     */
    public function wardFor(float $lat, float $lng): ?array
    {
        foreach ($this->features() as $feature) {
            if ($this->pointInMultiPolygon($lat, $lng, $feature['coordinates'])) {
                return [
                    'ward' => $feature['ward'],
                    'sub_county' => $feature['const'],
                    'county' => $feature['county'],
                ];
            }
        }

        return null;
    }

    /**
     * @return array<int, array{ward: string, const: string, county: string, coordinates: array}>
     */
    private function features(): array
    {
        if ($this->features !== null) {
            return $this->features;
        }

        $cached = Cache::get(self::CACHE_KEY);
        if (is_array($cached)) {
            return $this->features = $cached;
        }

        $path = storage_path('app/geodata/kenya_wards.geojson');

        if (! is_file($path)) {
            throw new RuntimeException('Kenya ward geodata not found at ' . $path);
        }

        $geojson = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        $features = [];
        foreach (($geojson['features'] ?? []) as $feature) {
            $properties = $feature['properties'] ?? [];
            $geometry = $feature['geometry'] ?? null;

            if ($geometry === null) {
                continue;
            }

            $features[] = [
                'ward' => (string) ($properties['ward'] ?? ''),
                'const' => (string) ($properties['const'] ?? ''),
                'county' => (string) ($properties['county'] ?? ''),
                'coordinates' => $geometry['coordinates'] ?? [],
            ];
        }

        Cache::put(self::CACHE_KEY, $features, now()->addDays(30));

        return $this->features = $features;
    }

    private function pointInMultiPolygon(float $lat, float $lng, array $multiPolygon): bool
    {
        foreach ($multiPolygon as $polygon) {
            if (empty($polygon)) {
                continue;
            }

            $outer = array_shift($polygon);
            if (! $this->pointInRing($lat, $lng, $outer)) {
                continue;
            }

            $inHole = false;
            foreach ($polygon as $hole) {
                if ($this->pointInRing($lat, $lng, $hole)) {
                    $inHole = true;
                    break;
                }
            }

            if (! $inHole) {
                return true;
            }
        }

        return false;
    }

    /**
     * Ray-casting point-in-polygon test. Rings are [lng, lat] pairs.
     */
    private function pointInRing(float $lat, float $lng, array $ring): bool
    {
        $inside = false;
        $n = count($ring);

        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $xi = $ring[$i][0];
            $yi = $ring[$i][1];
            $xj = $ring[$j][0];
            $yj = $ring[$j][1];

            $intersect = (($yi > $lat) !== ($yj > $lat))
                && ($lng < ($xj - $xi) * ($lat - $yi) / ($yj - $yi) + $xi);

            if ($intersect) {
                $inside = ! $inside;
            }
        }

        return $inside;
    }
}
