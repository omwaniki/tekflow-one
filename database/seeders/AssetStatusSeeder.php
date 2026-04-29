<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssetStatus;
use Illuminate\Support\Str;

class AssetStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'Working', 'color' => 'green'],
            ['name' => 'Stolen', 'color' => 'red'],
            ['name' => 'Under Maintenance', 'color' => 'blue'],
            ['name' => 'Damaged', 'color' => 'purple'],
            ['name' => 'Unissued (Tech Room)', 'color' => 'gray'],
            ['name' => 'Beyond Repair', 'color' => 'yellow'],
            ['name' => 'Other', 'color' => 'black'],
        ];

        foreach ($statuses as $status) {
            AssetStatus::updateOrCreate(
                ['slug' => Str::slug($status['name'])],
                [
                    'name' => $status['name'],
                    'color' => $status['color'],
                    'is_active' => true
                ]
            );
        }
    }
}