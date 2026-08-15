<?php

namespace Database\Seeders;

use App\Models\WifiPackage;
use Illuminate\Database\Seeder;

class WifiPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Free Wi-Fi',
                'code' => 'FREE-2H',
                'description' => 'Complimentary 2-hour internet access for all citizens.',
                'duration_minutes' => 120,
                'price' => 0,
                'access_type' => 'free',
                'bandwidth_down_kbps' => 10240,
                'bandwidth_up_kbps' => 5120,
                'data_limit_mb' => null,
                'simultaneous_devices' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Direct Wi-Fi',
                'code' => 'DIRECT-2H',
                'description' => 'Affordable pay-as-you-go 2-hour session.',
                'duration_minutes' => 120,
                'price' => 10,
                'access_type' => 'paid',
                'bandwidth_down_kbps' => 20480,
                'bandwidth_up_kbps' => 10240,
                'data_limit_mb' => null,
                'simultaneous_devices' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'High-Speed Direct',
                'code' => 'DIRECT-24H',
                'description' => '24-hour high-speed session for heavy users.',
                'duration_minutes' => 1440,
                'price' => 50,
                'access_type' => 'paid',
                'bandwidth_down_kbps' => 51200,
                'bandwidth_up_kbps' => 25600,
                'data_limit_mb' => 51200,
                'simultaneous_devices' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Sponsored Access',
                'code' => 'SPONSORED-2H',
                'description' => '2-hour access sponsored by a partner, with promotional content.',
                'duration_minutes' => 120,
                'price' => 0,
                'access_type' => 'sponsored',
                'bandwidth_down_kbps' => 10240,
                'bandwidth_up_kbps' => 5120,
                'data_limit_mb' => null,
                'simultaneous_devices' => 1,
                'is_active' => true,
            ],
        ];

        foreach ($packages as $package) {
            WifiPackage::updateOrCreate(
                ['code' => $package['code']],
                array_merge($package, ['organization_id' => null])
            );
        }
    }
}
