<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,        // 1. Buat Usernya dulu
            PackageSeeder::class,     // 2. Buat Paketnya
            RegistrationSeeder::class, // 3. Daftarkan User ke Paket
            PaymentSeeder::class,      // 4. Bayar pendaftarannya
            ProgressSeeder::class,     // 5. Catat progresnya
        ]);
    }
}
