<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin Utama',
                'email' => 'admin@gym.com',
                'password' => Hash::make('password'),
                'role' => 'Admin',
                'gender' => 'Laki-Laki',
            ],
            [
                'name' => 'Budi Member Aktif', // Member yg akan kita beri registrasi & progress
                'email' => 'budi@active.com',
                'password' => Hash::make('password'),
                'role' => 'Member',
                'gender' => 'Laki-Laki',
            ],
            [
                'name' => 'Siti Member Baru', // Member yg kosongan
                'email' => 'siti@new.com',
                'password' => Hash::make('password'),
                'role' => 'Member',
                'gender' => 'Wanita',
            ],
            [
                'name' => 'Yunus Mang', // Member yg kosongan
                'email' => 'yunus@new.com',
                'password' => Hash::make('password'),
                'role' => 'Member',
                'gender' => 'Laki-Laki',
            ],
        ]);
    }
}
