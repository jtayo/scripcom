<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $entities = [
            'organization',
            'user',
            'hotspot',
            'router',
            'campaign',
            'sponsor',
            'sponsorship',
            'voucher',
            'package',
            'session',
            'event',
            'payment',
            'contract',
            'invoice',
            'revenue',
            'role',
            'permission',
            'audit-log',
        ];

        $actions = ['view-any', 'view', 'create', 'update', 'delete'];

        $permissions = [
            'buy-credits',
            'view-analytics',
            'view-reports',
            'export-reports',
            'view-settings',
            'update-settings',
            'view-notifications',
        ];

        foreach ($entities as $entity) {
            foreach ($actions as $action) {
                $permissions[] = "{$action}-{$entity}";
            }
        }

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        $orgAdmin = Role::firstOrCreate(['name' => 'Organization Admin', 'guard_name' => 'web']);
        $orgAdmin->syncPermissions([
            'view-any-user', 'view-user', 'create-user', 'update-user', 'delete-user',
            'view-any-hotspot', 'view-hotspot', 'create-hotspot', 'update-hotspot', 'delete-hotspot',
            'view-any-router', 'view-router', 'create-router', 'update-router',
            'view-any-campaign', 'view-campaign', 'create-campaign', 'update-campaign', 'delete-campaign',
            'view-any-sponsor', 'view-sponsor', 'create-sponsor', 'update-sponsor', 'delete-sponsor',
            'view-any-sponsorship', 'view-sponsorship', 'create-sponsorship', 'update-sponsorship',
            'view-any-voucher', 'view-voucher', 'create-voucher', 'update-voucher', 'delete-voucher',
            'view-any-package', 'view-package', 'create-package', 'update-package',
            'view-any-session', 'view-session',
            'view-any-event', 'view-event',
            'view-any-payment', 'view-payment',
            'view-any-contract', 'view-contract', 'create-contract', 'update-contract',
            'view-any-invoice', 'view-invoice', 'create-invoice', 'update-invoice',
            'view-any-revenue', 'view-revenue', 'create-revenue',
            'view-any-audit-log', 'view-audit-log',
            'buy-credits',
            'view-analytics',
            'view-reports',
            'view-settings', 'update-settings',
            'view-notifications',
        ]);

        $departmentAdmin = Role::firstOrCreate(['name' => 'Department Admin', 'guard_name' => 'web']);
        $departmentAdmin->syncPermissions([
            'view-any-hotspot', 'view-hotspot', 'update-hotspot',
            'view-any-router', 'view-router', 'update-router',
            'view-any-campaign', 'view-campaign', 'create-campaign', 'update-campaign',
            'view-any-voucher', 'view-voucher', 'create-voucher', 'update-voucher',
            'view-any-package', 'view-package', 'update-package',
            'view-any-session', 'view-session',
            'view-any-event', 'view-event',
            'view-any-contract', 'view-contract',
            'view-any-invoice', 'view-invoice',
            'view-any-revenue', 'view-revenue',
            'view-any-audit-log', 'view-audit-log',
            'view-analytics',
            'view-notifications',
        ]);

        $viewer = Role::firstOrCreate(['name' => 'Viewer', 'guard_name' => 'web']);
        $viewer->syncPermissions([
            'view-any-hotspot', 'view-hotspot',
            'view-any-router', 'view-router',
            'view-any-campaign', 'view-campaign',
            'view-any-package', 'view-package',
            'view-any-session', 'view-session',
            'view-any-event', 'view-event',
            'view-any-sponsor', 'view-sponsor',
            'view-any-sponsorship', 'view-sponsorship',
            'view-any-voucher', 'view-voucher',
            'view-any-contract', 'view-contract',
            'view-any-invoice', 'view-invoice',
            'view-any-revenue', 'view-revenue',
            'view-any-audit-log', 'view-audit-log',
            'view-analytics',
            'view-any-payment', 'view-payment',
            'view-notifications',
        ]);

        $sponsor = Role::firstOrCreate(['name' => 'Sponsor', 'guard_name' => 'web']);
        $sponsor->syncPermissions([
            'view-any-campaign', 'view-campaign', 'create-campaign', 'update-campaign',
            'view-any-sponsorship', 'view-sponsorship',
            'view-any-voucher', 'view-voucher',
            'view-any-package', 'view-package',
            'view-any-payment', 'view-payment',
            'view-any-contract', 'view-contract',
            'view-any-invoice', 'view-invoice',
            'buy-credits',
            'view-analytics',
            'view-notifications',
        ]);

        $countyAdmin = Role::firstOrCreate(['name' => 'County Administrator', 'guard_name' => 'web']);
        $countyAdmin->syncPermissions([
            'view-any-user', 'view-user', 'create-user', 'update-user',
            'view-any-hotspot', 'view-hotspot', 'create-hotspot', 'update-hotspot',
            'view-any-router', 'view-router', 'update-router',
            'view-any-campaign', 'view-campaign', 'create-campaign', 'update-campaign',
            'view-any-sponsor', 'view-sponsor',
            'view-any-sponsorship', 'view-sponsorship', 'create-sponsorship', 'update-sponsorship',
            'view-any-voucher', 'view-voucher', 'create-voucher', 'update-voucher',
            'view-any-package', 'view-package', 'create-package', 'update-package',
            'view-any-session', 'view-session',
            'view-any-event', 'view-event',
            'view-any-payment', 'view-payment',
            'view-any-contract', 'view-contract', 'create-contract', 'update-contract',
            'view-any-invoice', 'view-invoice', 'create-invoice', 'update-invoice',
            'view-any-revenue', 'view-revenue', 'create-revenue',
            'view-any-audit-log', 'view-audit-log',
            'view-analytics',
            'view-reports',
            'view-notifications',
        ]);

        $corporateAdmin = Role::firstOrCreate(['name' => 'Corporate Administrator', 'guard_name' => 'web']);
        $corporateAdmin->syncPermissions([
            'view-any-campaign', 'view-campaign', 'create-campaign', 'update-campaign',
            'view-any-sponsor', 'view-sponsor', 'create-sponsor', 'update-sponsor',
            'view-any-sponsorship', 'view-sponsorship', 'create-sponsorship', 'update-sponsorship',
            'view-any-voucher', 'view-voucher', 'create-voucher', 'update-voucher',
            'view-any-package', 'view-package', 'create-package', 'update-package',
            'view-any-session', 'view-session',
            'view-any-event', 'view-event',
            'view-any-payment', 'view-payment',
            'view-any-contract', 'view-contract', 'create-contract', 'update-contract',
            'view-any-invoice', 'view-invoice', 'create-invoice', 'update-invoice',
            'view-any-revenue', 'view-revenue', 'create-revenue',
            'view-any-audit-log', 'view-audit-log',
            'buy-credits',
            'view-analytics',
            'view-reports',
            'view-notifications',
        ]);
    }
}
