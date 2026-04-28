<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegistrationSeeder extends Seeder
{
public function run(): void
    {
        $memberAktif = DB::table('users')->where('email', 'budi@active.com')->first();
        $package = DB::table('packages')->first();

        if ($memberAktif) {
            DB::table('registrations')->insert([
                'id_user' => $memberAktif->id_user,
                'id_package' => $package->id_package,
                'start_date' => now(),
                'expiry_date' => now()->addDays(30),
                'status' => 'Active',
            ]);
        }
    }
}
