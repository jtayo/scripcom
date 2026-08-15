<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $mombasa = Organization::where('slug', 'mombasa-county')->first();
        $kilifi = Organization::where('slug', 'kilifi-county')->first();
        $uon = Organization::where('slug', 'university-of-nairobi')->first();
        $safaricom = Organization::where('slug', 'safaricom')->first();
        $equity = Organization::where('slug', 'equity-bank')->first();

        $users = [
            [
                'organization_id' => null,
                'name' => 'Super Admin',
                'email' => 'admin@wificivicplatform.com',
                'password' => 'password',
                'phone' => '0700000000',
                'status' => 'active',
                'role' => 'Super Admin',
            ],
            [
                'organization_id' => $mombasa?->id,
                'name' => 'Grace Mwangi',
                'email' => 'grace@mombasa.go.ke',
                'password' => 'password',
                'phone' => '0711000001',
                'status' => 'active',
                'role' => 'Department Admin',
            ],
            [
                'organization_id' => $mombasa?->id,
                'name' => 'James Ochieng',
                'email' => 'james@mombasa.go.ke',
                'password' => 'password',
                'phone' => '0711000002',
                'status' => 'active',
                'role' => 'Viewer',
            ],
            [
                'organization_id' => $kilifi?->id,
                'name' => 'Amina Hassan',
                'email' => 'amina@kilifi.go.ke',
                'password' => 'password',
                'phone' => '0711000003',
                'status' => 'active',
                'role' => 'Organization Admin',
            ],
            [
                'organization_id' => $uon?->id,
                'name' => 'Peter Kariuki',
                'email' => 'peter@uonbi.ac.ke',
                'password' => 'password',
                'phone' => '0711000004',
                'status' => 'active',
                'role' => 'Organization Admin',
            ],
            [
                'organization_id' => $safaricom?->id,
                'name' => 'Betty Njoroge',
                'email' => 'betty@safaricom.co.ke',
                'password' => 'password',
                'phone' => '0711000005',
                'status' => 'active',
                'role' => 'Corporate Administrator',
            ],
            [
                'organization_id' => $equity?->id,
                'name' => 'David Otieno',
                'email' => 'david@equitybank.co.ke',
                'password' => 'password',
                'phone' => '0711000006',
                'status' => 'active',
                'role' => 'Corporate Administrator',
            ],
        ];

        foreach ($users as $data) {
            $role = $data['role'];
            unset($data['role']);

            $user = User::where('email', $data['email'])->first();

            if ($user) {
                $user->update($data);
            } else {
                $user = User::create($data);
            }

            $user->assignRole($role);
        }
    }
}
