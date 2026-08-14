<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Event;
use App\Models\Hotspot;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Sponsor;
use App\Models\Sponsorship;
use App\Models\Voucher;
use App\Models\WifiSession;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $org = Organization::where('slug', 'mombasa-county')->first();

        if (! $org) {
            $org = Organization::first();
        }

        $hotspots = [
            ['name' => 'Fort Jesus Garden', 'router_id' => 1, 'latitude' => -4.0629, 'longitude' => 39.6796, 'ward' => 'Mji wa Kale', 'sub_county' => 'Mvita'],
            ['name' => 'Nyali Beach', 'router_id' => 3, 'latitude' => -4.0135, 'longitude' => 39.7152, 'ward' => 'Nyali', 'sub_county' => 'Nyali'],
            ['name' => 'Moi International Airport', 'router_id' => 4, 'latitude' => -4.0348, 'longitude' => 39.5942, 'ward' => 'Port Reitz', 'sub_county' => 'Changamwe'],
            ['name' => 'Likoni Ferry Terminal', 'router_id' => 5, 'latitude' => -4.0868, 'longitude' => 39.6729, 'ward' => 'Likoni', 'sub_county' => 'Likoni'],
            ['name' => 'City Market', 'router_id' => 8, 'latitude' => -4.0560, 'longitude' => 39.6641, 'ward' => 'Mji wa Kale', 'sub_county' => 'Mvita'],
            ['name' => 'Bamburi Beach', 'router_id' => 10, 'latitude' => -3.9826, 'longitude' => 39.7398, 'ward' => 'Bamburi', 'sub_county' => 'Kisauni'],
        ];

        foreach ($hotspots as $data) {
            Hotspot::updateOrCreate(
                ['slug' => str($data['name'])->slug()],
                array_merge($data, [
                    'organization_id' => $org->id,
                    'slug' => str($data['name'])->slug(),
                    'ssid' => 'Mombasa-Free-WiFi',
                    'device_model' => 'MikroTik hAP ac3',
                    'firmware_version' => '7.14.2',
                    'ip_address' => '192.168.' . ($data['router_id'] % 254) . '.1',
                    'mac_address' => fake()->unique()->macAddress(),
                    'isp' => 'Safaricom',
                    'bandwidth_up' => 10,
                    'bandwidth_down' => 20,
                    'status' => $data['router_id'] % 3 === 0 ? 'degraded' : 'online',
                    'last_seen_at' => now()->subMinutes($data['router_id'] * 2),
                    'last_online_at' => now()->subMinutes($data['router_id'] * 2),
                    'max_clients' => 50,
                    'is_active' => true,
                ])
            );
        }

        $sponsors = [
            ['name' => 'Safaricom PLC', 'slug' => 'safaricom', 'email' => 'partners@safaricom.co.ke', 'phone' => '0722000001', 'contact_person' => 'Betty Njoroge', 'website' => 'https://www.safaricom.co.ke'],
            ['name' => 'Kenya Tourism Board', 'slug' => 'kenya-tourism-board', 'email' => 'info@ktb.go.ke', 'phone' => '0722000002', 'contact_person' => 'John Chirchir', 'website' => 'https://www.magicalkenya.com'],
            ['name' => 'Equity Bank', 'slug' => 'equity-bank', 'email' => 'corporate@equitybank.co.ke', 'phone' => '0722000003', 'contact_person' => 'Mary Wambui', 'website' => 'https://www.equitybank.co.ke'],
            ['name' => 'Mombasa Port Authority', 'slug' => 'mombasa-port-authority', 'email' => 'marketing@kpa.co.ke', 'phone' => '0722000004', 'contact_person' => 'David Mwangi', 'website' => 'https://www.kpa.co.ke'],
        ];

        foreach ($sponsors as $data) {
            Sponsor::updateOrCreate(['slug' => $data['slug']], $data);
        }

        $sponsor = Sponsor::where('slug', 'safaricom')->first();
        $ktb = Sponsor::where('slug', 'kenya-tourism-board')->first();
        $equity = Sponsor::where('slug', 'equity-bank')->first();

        $campaigns = [
            [
                'organization_id' => $org->id,
                'sponsor_id' => $ktb?->id,
                'title' => 'Visit Magical Kenya',
                'slug' => 'visit-magical-kenya',
                'description' => 'Discover the beautiful beaches of the Kenyan coast and beyond.',
                'type' => 'tourism',
                'content_type' => 'image',
                'content_url' => 'https://www.magicalkenya.com',
                'duration_seconds' => 15,
                'skip_allowed' => false,
                'is_mandatory' => true,
                'priority' => 3,
                'status' => 'active',
                'is_active' => true,
            ],
            [
                'organization_id' => $org->id,
                'sponsor_id' => $sponsor?->id,
                'title' => 'Stay Connected with Safaricom',
                'slug' => 'stay-connected-safaricom',
                'description' => 'Enjoy high-speed internet powered by Safaricom.',
                'type' => 'commercial',
                'content_type' => 'image',
                'content_url' => 'https://www.safaricom.co.ke',
                'duration_seconds' => 20,
                'skip_allowed' => false,
                'is_mandatory' => true,
                'priority' => 5,
                'status' => 'active',
                'is_active' => true,
            ],
            [
                'organization_id' => $org->id,
                'sponsor_id' => $equity?->id,
                'title' => 'Bank with Equity',
                'slug' => 'bank-with-equity',
                'description' => 'Open an account and enjoy free digital banking.',
                'type' => 'commercial',
                'content_type' => 'image',
                'content_url' => 'https://www.equitybank.co.ke',
                'duration_seconds' => 15,
                'skip_allowed' => true,
                'is_mandatory' => false,
                'priority' => 1,
                'status' => 'active',
                'is_active' => true,
            ],
            [
                'organization_id' => $org->id,
                'sponsor_id' => null,
                'title' => 'County Health Notice: Cholera Prevention',
                'slug' => 'county-health-cholera',
                'description' => 'Wash your hands with soap and clean water regularly.',
                'type' => 'health',
                'content_type' => 'html',
                'duration_seconds' => 12,
                'skip_allowed' => false,
                'is_mandatory' => true,
                'priority' => 10,
                'status' => 'active',
                'is_active' => true,
            ],
            [
                'organization_id' => $org->id,
                'sponsor_id' => null,
                'title' => 'Emergency Services Hotline',
                'slug' => 'emergency-hotline',
                'description' => 'Call 999 for emergencies or 112 from your mobile.',
                'type' => 'emergency',
                'content_type' => 'html',
                'duration_seconds' => 10,
                'skip_allowed' => false,
                'is_mandatory' => true,
                'priority' => 9,
                'status' => 'active',
                'is_active' => true,
            ],
        ];

        $hotspots = Hotspot::all();

        foreach ($campaigns as $data) {
            $campaign = Campaign::updateOrCreate(['slug' => $data['slug']], $data);
            $campaign->hotspots()->syncWithoutDetaching($hotspots->pluck('id'));
        }

        $sponsorships = [
            ['type' => 'sessions', 'quantity_purchased' => 5000, 'quantity_used' => 2140, 'unit_price' => 2, 'total_amount' => 10000, 'status' => 'active'],
            ['type' => 'hours', 'quantity_purchased' => 2000, 'quantity_used' => 760, 'unit_price' => 5, 'total_amount' => 10000, 'status' => 'active'],
            ['type' => 'campaign', 'quantity_purchased' => 100000, 'quantity_used' => 48300, 'unit_price' => 0.5, 'total_amount' => 50000, 'status' => 'active'],
            ['type' => 'bandwidth', 'quantity_purchased' => 5000, 'quantity_used' => 1280, 'unit_price' => 10, 'total_amount' => 50000, 'status' => 'active'],
            ['type' => 'sessions', 'quantity_purchased' => 2000, 'quantity_used' => 2000, 'unit_price' => 2, 'total_amount' => 4000, 'status' => 'expired'],
        ];

        foreach ($sponsorships as $index => $data) {
            $sponsorshipSponsor = $index % 2 === 0 ? $sponsor : $equity;

            Sponsorship::updateOrCreate(
                ['reference' => 'SP-' . str_pad((string) ($index + 1), 6, '0', STR_PAD_LEFT)],
                array_merge($data, [
                    'organization_id' => $org->id,
                    'sponsor_id' => $sponsorshipSponsor->id,
                    'reference' => 'SP-' . str_pad((string) ($index + 1), 6, '0', STR_PAD_LEFT),
                    'currency' => 'KES',
                    'starts_at' => now()->subDays(30),
                    'expires_at' => $data['status'] === 'expired' ? now()->subDay() : now()->addDays(30),
                ])
            );
        }

        $activeCampaign = Campaign::where('slug', 'stay-connected-safaricom')->first();
        $countyCampaign = Campaign::where('slug', 'county-health-cholera')->first();

        $sessions = [];
        $eventTypes = [
            'portal.opened', 'phone.submitted', 'otp.requested', 'otp.verified',
            'video.started', 'video.progress', 'video.completed',
            'internet.granted', 'session.started', 'session.ended', 'bandwidth.updated',
        ];

        foreach (range(1, 60) as $i) {
            $hotspot = $hotspots->random();
            $startedAt = now()->subMinutes(rand(10, 60 * 24 * 14));
            $duration = rand(300, 7200);
            $status = rand(1, 4) === 1 ? 'active' : 'completed';
            $campaign = rand(1, 2) === 1 ? $activeCampaign : $countyCampaign;

            $sessions[] = [
                'session_id' => (string) str()->uuid(),
                'organization_id' => $org->id,
                'hotspot_id' => $hotspot->id,
                'campaign_id' => $campaign->id,
                'phone' => '07' . rand(10000000, 99999999),
                'mac_address' => fake()->macAddress(),
                'device_type' => ['android', 'ios', 'windows', 'linux'][rand(0, 3)],
                'browser' => ['Chrome', 'Safari', 'Firefox', 'Edge'][rand(0, 3)],
                'ip_address' => fake()->ipv4(),
                'auth_method' => ['otp', 'voucher', 'social'][rand(0, 2)],
                'video_completed' => true,
                'video_watch_duration' => rand(10, 30),
                'total_duration' => $duration,
                'bandwidth_used' => rand(1000000, 250000000),
                'bandwidth_up' => rand(500000, 120000000),
                'bandwidth_down' => rand(500000, 130000000),
                'session_started_at' => $startedAt,
                'ended_at' => $status === 'completed' ? $startedAt->copy()->addSeconds($duration) : null,
                'last_heartbeat_at' => $status === 'active' ? now()->subMinutes(rand(1, 30)) : $startedAt->copy()->addSeconds($duration),
                'status' => $status,
                'end_reason' => $status === 'completed' ? 'ended' : null,
                'created_at' => $startedAt,
                'updated_at' => now(),
            ];
        }

        WifiSession::insert($sessions);

        $events = [];
        $allSessions = WifiSession::all();

        foreach ($allSessions as $session) {
            $count = rand(3, 6);
            for ($j = 0; $j < $count; $j++) {
                $type = $eventTypes[array_rand($eventTypes)];
                $events[] = [
                    'organization_id' => $org->id,
                    'session_id' => $session->id,
                    'hotspot_id' => $session->hotspot_id,
                    'campaign_id' => $session->campaign_id,
                    'event_type' => $type,
                    'payload' => json_encode(['source' => 'seeder']),
                    'ip_address' => $session->ip_address,
                    'user_agent' => 'Seeder/1.0',
                    'occurred_at' => $session->session_started_at->copy()->addMinutes(rand(0, 90)),
                ];
            }
        }

        foreach (array_chunk($events, 500) as $chunk) {
            Event::insert($chunk);
        }

        $activeSponsorship = Sponsorship::where('status', 'active')->first();

        foreach (range(1, 10) as $i) {
            Payment::create([
                'organization_id' => $org->id,
                'sponsorship_id' => $activeSponsorship->id,
                'phone' => '07' . rand(10000000, 99999999),
                'amount' => [1000, 5000, 10000, 25000][rand(0, 3)],
                'currency' => 'KES',
                'status' => ['completed', 'completed', 'pending', 'failed'][rand(0, 3)],
                'checkout_request_id' => 'WS_CO_' . now()->timestamp . $i,
                'mpesa_receipt_number' => 'SGF' . rand(100000, 999999),
                'transacted_at' => now()->subDays(rand(1, 30)),
            ]);
        }

        Voucher::create([
            'sponsor_id' => $sponsor->id,
            'sponsorship_id' => $activeSponsorship->id,
            'code' => 'DEMO-VOUCHER-1',
            'batch_id' => 'V-DEMO1',
            'type' => 'sessions',
            'value' => 1,
            'status' => 'unused',
            'created_by' => 1,
            'expires_at' => now()->addDays(30),
        ]);

        Setting::setValue('sponsorship.unit_price', 2, $org->id);
        Setting::setValue('portal.default_session_minutes', 120, $org->id);
        Setting::setValue('portal.default_bandwidth_mbps', 10, $org->id);
    }
}
