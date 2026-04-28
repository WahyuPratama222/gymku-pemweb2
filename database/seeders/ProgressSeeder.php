<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $memberAktif = DB::table('users')->where('email', 'budi@active.com')->first();

        if ($memberAktif) {
            DB::table('progress')->insert([
                [
                    'id_user' => $memberAktif->id_user,
                    'record_date' => now(),
                    'weight' => 75.0,
                    'height' => 170.0,
                    'body_fat' => 20.0,
                    'muscle_mass' => 35.0,
                ],
            ]);
        }
    }
}
