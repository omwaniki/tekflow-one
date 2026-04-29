<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $admin = Role::firstOrCreate(['name' => 'admin'], ['guard_name' => 'web']);
        $global = Role::firstOrCreate(['name' => 'global'], ['guard_name' => 'web']);
        $regional = Role::firstOrCreate(['name' => 'regional'], ['guard_name' => 'web']);
        $campus = Role::firstOrCreate(['name' => 'campus'], ['guard_name' => 'web']);

        // Get all permissions
        $allPermissions = Permission::all();

        // ✅ Admin gets everything
        $admin->syncPermissions($allPermissions);

        // ✅ Optional: assign selectively to others (safe defaults)

        // Global → can manage assets & statuses
        $global->syncPermissions([
            'view assets',
            'create assets',
            'edit assets',
            'assign assets',
            'manage asset statuses',
        ]);

        // Regional → limited
        $regional->syncPermissions([
            'view assets',
            'edit assets',
            'assign assets',
        ]);

        // Campus → very limited
        $campus->syncPermissions([
            'view assets',
        ]);
    }
}