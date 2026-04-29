<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ===== ASSETS =====
        Permission::firstOrCreate(['name' => 'view assets'], ['guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create assets'], ['guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit assets'], ['guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete assets'], ['guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'assign assets'], ['guard_name' => 'web']);

        // ===== AGENTS =====
        Permission::firstOrCreate(['name' => 'manage agents'], ['guard_name' => 'web']);

        // ===== ROLES =====
        Permission::firstOrCreate(['name' => 'manage roles'], ['guard_name' => 'web']);

        // ===== SETTINGS =====
        Permission::firstOrCreate(['name' => 'manage asset statuses'], ['guard_name' => 'web']);

        // 🔥 ===== AUDITS (NEW) =====
        Permission::firstOrCreate(['name' => 'view audits'], ['guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create audits'], ['guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'verify audits'], ['guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage audits'], ['guard_name' => 'web']);
    }
}