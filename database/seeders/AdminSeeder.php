<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gymku.com'],
            [
                'name'     => 'Admin Gymku',
                'gender'   => 'Male',
                'password' => Hash::make('Admin@Gymku2026'),
                'role'     => 'Admin',
            ]
        );

        $this->command->info('✅ Admin user seeded: admin@gymku.com');
    }
}
