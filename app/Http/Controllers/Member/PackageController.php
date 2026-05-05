<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Registration;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PackageController extends Controller
{
    /**
     * Display list of active packages.
     */
    public function index()
    {
        // Menggunakan scope active() yang sudah diperbaiki di model
        $packages = Package::active()
            ->withCount(['registrations' => function ($query) {
                $query->where('status', '!=', 'Cancelled');
            }])
            ->orderBy('price', 'asc')
            ->get();

        // Tentukan paket paling populer (non-premium dengan registrations terbanyak)
        $mostPopularNonPremiumId = Package::active()
            ->where('is_premium', false)
            ->withCount(['registrations' => function ($query) {
                $query->where('status', '!=', 'Cancelled');
            }])
            ->orderBy('registrations_count', 'desc')
            ->first()?->id_package;

        return view('member.packages', compact('packages', 'mostPopularNonPremiumId'));
    }

    /**
     * Show checkout page.
     */
    public function checkout(Request $request)
    {
        $user = Auth::user();
        $packageId = $request->get('id');
        $quantity = (int) $request->get('quantity', 1);
        $extraDays = (int) $request->get('extra_days', 0);

        // Get package
        $package = Package::where('id_package', $packageId)
            ->where('status', 'Active')
            ->firstOrFail();

        // PROTEKSI VIP: Jika paket premium, minimal harus beli 1 bulan
        if ($package->is_premium && $quantity < 1) {
            $quantity = 1;
        }

        // Pastikan tidak negatif
        if ($quantity < 0) $quantity = 0;
        if ($extraDays < 0) $extraDays = 0;

        // Jika keduanya 0, paksa quantity minimal 1 agar tidak error durasi kosong
        if ($quantity === 0 && $extraDays === 0) {
            $quantity = 1;
        }

        // Calculate totals
        $pricePerDay = ceil($package->price / $package->day_duration);
        $totalDays = ($package->day_duration * $quantity) + $extraDays;
        $totalPrice = ($package->price * $quantity) + ($extraDays * $pricePerDay);

        // Check if user already has active membership
        $hasActiveMembership = Registration::where('id_user', $user->id_user)
            ->where('status', 'Active')
            ->exists();

        if ($hasActiveMembership) {
            return redirect()->route('member.packages')
                ->with('error', 'Kamu masih memiliki membership aktif. Tunggu hingga masa aktif habis.');
        }

        return view('member.checkout', compact(
            'package',
            'quantity',
            'extraDays',
            'totalDays',
            'totalPrice',
            'pricePerDay'
        ));
    }

    /**
     * Process checkout and create registration + payment.
     */
    public function processCheckout(Request $request)
    {
        $user = Auth::user();

        // Validate input (quantity sekarang boleh 0)
        $validator = Validator::make($request->all(), [
            'id_package' => 'required|exists:packages,id_package',
            'quantity' => 'required|integer|min:0',
            'extra_days' => 'required|integer|min:0',
            'start_date' => 'required|date|after_or_equal:today',
            'payment_method' => 'required|in:Transfer Bank,Tunai,QRIS,E-Wallet',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Get package
        $package = Package::findOrFail($request->id_package);

        // Calculate totals
        $quantity = (int) $request->quantity;
        $extraDays = (int) $request->extra_days;

        // PROTEKSI VIP: Jika paket premium, minimal harus beli 1 bulan
        if ($package->is_premium && $quantity < 1) {
            return back()->with('error', 'Paket Premium minimal harus dibeli untuk durasi 1 bulan.')->withInput();
        }

        if ($quantity === 0 && $extraDays === 0) {
            return back()->with('error', 'Durasi pembelian tidak boleh kosong. Silakan pilih bulan atau tambah hari.')->withInput();
        }

        $pricePerDay = ceil($package->price / $package->day_duration);
        
        $totalDays = ($package->day_duration * $quantity) + $extraDays;
        $totalPrice = ($package->price * $quantity) + ($extraDays * $pricePerDay);

        // Calculate expiry date
        $startDate = Carbon::parse($request->start_date);
        $expiryDate = $startDate->copy()->addDays($totalDays);

        try {
            DB::beginTransaction();

            // Create registration (sesuai enum di migration: 'Pending')
            $registration = Registration::create([
                'id_user' => $user->id_user,
                'id_package' => $package->id_package,
                'start_date' => $startDate,
                'expiry_date' => $expiryDate,
                'status' => 'Pending',
            ]);

            // Create payment
            Payment::create([
                'id_registration' => $registration->id_registration,
                'payment_method' => $request->payment_method,
                'payment_status' => 'Belum Lunas',
                'amount' => $totalPrice,
            ]);

            DB::commit();

            return redirect()->route('member.payment.success', $registration->id_registration)
                ->with('success', 'Pendaftaran berhasil! Silakan selesaikan pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    /**
     * Show payment success page with instructions.
     */
    public function paymentSuccess($registrationId)
    {
        $user = Auth::user();

        // Get transaction details
        $transaction = Registration::with(['package', 'payment'])
            ->where('id_registration', $registrationId)
            ->where('id_user', $user->id_user)
            ->firstOrFail();

        // Create object for view
        $transaction = (object) [
            'id_registration' => $transaction->id_registration,
            'package_name' => $transaction->package->name,
            'payment_method' => $transaction->payment->payment_method,
            'payment_status' => $transaction->payment->payment_status,
            'amount' => $transaction->payment->amount,
        ];

        // Generate payment instructions based on method
        $instructions = $this->getPaymentInstructions($transaction->payment_method, $transaction->amount, $registrationId);

        return view('member.payment-success', compact('transaction', 'instructions'));
    }

    /**
     * Get payment instructions based on method.
     */
    protected function getPaymentInstructions($method, $amount, $regId)
    {
        $formattedAmount = 'Rp ' . number_format($amount, 0, ',', '.');
        $orderId = '#REG-' . str_pad($regId, 5, '0', STR_PAD_LEFT);

        switch ($method) {
            case 'E-Wallet':
                return [
                    'Buka aplikasi e-wallet pilihan Anda (OVO, LinkAja, ShopeePay).',
                    'Pilih menu <strong>Transfer</strong> atau <strong>Kirim Uang</strong>.',
                    'Masukkan nomor Gymku: <strong>081234567890</strong>.',
                    'Masukkan nominal sebesar ' . $formattedAmount . '.',
                    'Konfirmasi dan simpan bukti pembayaran.',
                ];

            case 'QRIS':
                return [
                    'Buka m-BCA, GoPay, OVO, atau aplikasi yang mendukung QRIS.',
                    'Pilih opsi <strong>Pindai / Scan QR</strong>.',
                    'Tunjukkan atau pindai QR Gymku di meja resepsionis.',
                    'Masukkan jumlah ' . $formattedAmount . ' dan klik konfirmasi.',
                    'Simpan bukti transaksi sukses.',
                ];

            case 'Transfer Bank':
                return [
                    'Login M-Banking, Internet Banking, atau ATM.',
                    'Pilih <strong>Transfer Antar Bank</strong>.',
                    'Tujuan transfer: <strong>BCA 1234567890 (Gymku Official)</strong>.',
                    'Masukkan nominal: ' . $formattedAmount . '.',
                    'Simpan struk atau bukti transfer digital Anda.',
                ];

            case 'Tunai':
                return [
                    'Kunjungi resepsionis Gymku secara langsung.',
                    'Sebutkan nama atau email Anda untuk verifikasi (Order ID: <strong>' . $orderId . '</strong>).',
                    'Lakukan pembayaran tunai sebesar ' . $formattedAmount . '.',
                    'Status membership akan segera diaktifkan setelah kasir mengonfirmasi.',
                ];

            default:
                return [];
        }
    }
}