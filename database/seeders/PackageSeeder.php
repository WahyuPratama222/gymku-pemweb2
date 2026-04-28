<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('packages')->insert([
            [
                'name' => 'Monthly Basic',
                'price' => 200000,
                'day_duration' => 30,
                'is_premium' => false,
                'status' => 'Active',
                'created_at' => now(),
            ],
            [
                'name' => 'VIP Platinum',
                'price' => 500000,
                'day_duration' => 90,
                'is_premium' => true,
                'status' => 'Active',
                'created_at' => now(),
            ],
        ]);
    }
}
