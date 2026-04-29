<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'onesmugendi@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Mugendi@2025*'),
            ]
        );
    }
}