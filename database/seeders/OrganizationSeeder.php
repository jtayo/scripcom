<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = [
            [
                'name' => 'Mombasa County Government',
                'slug' => 'mombasa-county',
                'email' => 'ict@mombasa.go.ke',
                'phone' => '+254411231000',
                'address' => 'P.O. Box 90438-80100',
                'city' => 'Mombasa',
                'county' => 'Mombasa',
                'country' => 'Kenya',
                'postal_code' => '80100',
                'website' => 'https://www.mombasa.go.ke',
                'primary_color' => '#DB1F2A',
                'secondary_color' => '#262B40',
                'type' => 'county',
                'is_active' => true,
            ],
            [
                'name' => 'Kilifi County Government',
                'slug' => 'kilifi-county',
                'email' => 'info@kilifi.go.ke',
                'phone' => '+254416208400',
                'address' => 'P.O. Box 420-80108',
                'city' => 'Kilifi',
                'county' => 'Kilifi',
                'country' => 'Kenya',
                'postal_code' => '80108',
                'website' => 'https://www.kilifi.go.ke',
                'primary_color' => '#0E7A5C',
                'secondary_color' => '#262B40',
                'type' => 'county',
                'is_active' => true,
            ],
            [
                'name' => 'University of Nairobi',
                'slug' => 'university-of-nairobi',
                'email' => 'info@uonbi.ac.ke',
                'phone' => '+254207010117',
                'address' => 'P.O. Box 30197-00100',
                'city' => 'Nairobi',
                'county' => 'Nairobi',
                'country' => 'Kenya',
                'postal_code' => '00100',
                'website' => 'https://www.uonbi.ac.ke',
                'primary_color' => '#FFC20E',
                'secondary_color' => '#00437B',
                'type' => 'institution',
                'is_active' => true,
            ],
            [
                'name' => 'Safaricom PLC',
                'slug' => 'safaricom',
                'email' => 'partners@safaricom.co.ke',
                'phone' => '+254722000001',
                'address' => 'Safaricom House, Waiyaki Way',
                'city' => 'Nairobi',
                'county' => 'Nairobi',
                'country' => 'Kenya',
                'postal_code' => '00100',
                'website' => 'https://www.safaricom.co.ke',
                'primary_color' => '#E60000',
                'secondary_color' => '#262B40',
                'type' => 'corporate',
                'is_active' => true,
            ],
            [
                'name' => 'Equity Bank Kenya',
                'slug' => 'equity-bank',
                'email' => 'marketing@equitybank.co.ke',
                'phone' => '+254763000001',
                'address' => 'Equity Centre, Hospital Road',
                'city' => 'Nairobi',
                'county' => 'Nairobi',
                'country' => 'Kenya',
                'postal_code' => '00100',
                'website' => 'https://equitybank.co.ke',
                'primary_color' => '#00A859',
                'secondary_color' => '#262B40',
                'type' => 'corporate',
                'is_active' => true,
            ],
        ];

        foreach ($organizations as $organization) {
            Organization::updateOrCreate(['slug' => $organization['slug']], $organization);
        }
    }
}
