<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasOrganizationScoping;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    use HasOrganizationScoping;

    public function index(Request $request, AnalyticsService $analytics): View
    {
        $organization = $this->organization();

        $from = $this->date($request->input('from'), now()->subDays(30)->toDateString());
        $to = $this->date($request->input('to'), now()->toDateString());

        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        $fromDateTime = $from.' 00:00:00';
        $toDateTime = $to.' 23:59:59';

        $summary = $analytics->summaryStats($organization, $fromDateTime, $toDateTime);
        $trend = $analytics->usageTrend($organization, $from, $to);
        $peakHours = $analytics->sessionsByHour($organization, $fromDateTime, $toDateTime);
        $geo = $analytics->geoBreakdown($organization, $from, $to, 10);
        $devices = $analytics->deviceBreakdown($organization, $from, $to);
        $campaigns = $analytics->campaignAnalytics($organization, $fromDateTime, $toDateTime, 10);

        return view('analytics.index', compact(
            'organization',
            'from',
            'to',
            'summary',
            'trend',
            'peakHours',
            'geo',
            'devices',
            'campaigns'
        ));
    }

    private function date(mixed $value, string $default): string
    {
        if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        return $default;
    }
}
