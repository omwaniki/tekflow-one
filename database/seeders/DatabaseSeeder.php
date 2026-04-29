<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ✅ Seed roles first
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);

        // ✅ Seed asset statuses
        $this->call(AssetStatusSeeder::class);

        // ✅ Create root admin (persistent)
        $admin = User::updateOrCreate(
            ['email' => 'onesmugendi@gmail.com'], // unique identifier
            [
                'name' => 'System Admin',
                'password' => Hash::make('Mugendi@12345*'),
            ]
        );

        // ✅ Assign admin role (Spatie)
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // ✅ Optional: Test user
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );
    }
}