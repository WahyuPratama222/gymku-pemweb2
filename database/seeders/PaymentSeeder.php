<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        // Cari data registrasi yang ID User-nya merujuk ke email budi@active.com
        $regBudi = DB::table('registrations')
            ->join('users', 'registrations.id_user', '=', 'users.id_user')
            ->where('users.email', 'budi@active.com')
            ->select('registrations.*')
            ->first();

        // Jika data registrasi Budi ditemukan, buatkan pembayarannya
        if ($regBudi) {
            // Ambil harga dari paket yang dipilih Budi
            $package = DB::table('packages')
                ->where('id_package', $regBudi->id_package)
                ->first();

            DB::table('payments')->insert([
                'id_registration' => $regBudi->id_registration,
                'payment_method' => 'QRIS',
                'payment_status' => 'Lunas',
                'amount' => $package->price, // Mengambil harga otomatis dari tabel packages
                'payment_date' => now(),
            ]);
        }
    }
}
