<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display list of all payments.
     */
    public function index()
    {
        // Get all payments with related data
        $payments = Payment::with(['registration.user', 'registration.package'])
            ->latest('payment_date')
            ->get()
            ->map(function ($payment) {
                return (object) [
                    'id_payment' => $payment->id_payment,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'payment_status' => $payment->payment_status,
                    'payment_date' => $payment->payment_date,
                    
                    'id_registration' => $payment->registration->id_registration,
                    'registration_status' => $payment->registration->status,
                    'start_date' => $payment->registration->start_date,
                    'expiry_date' => $payment->registration->expiry_date,
                    
                    'member_name' => $payment->registration->user->name,
                    'member_email' => $payment->registration->user->email,
                    
                    'package_name' => $payment->registration->package->name,
                ];
            });

        return view('admin.payments', compact('payments'));
    }

    /**
     * Confirm payment and activate membership.
     */
    public function confirm(Request $request, $id)
    {
        $payment = Payment::with('registration')->findOrFail($id);

        // Check if already confirmed
        if ($payment->payment_status === 'Lunas') {
            return redirect()->route('admin.payments')
                ->with('error', 'Pembayaran ini sudah dikonfirmasi sebelumnya.');
        }

        try {
            DB::beginTransaction();

            // Update payment status to Lunas (Paid)
            $payment->update([
                'payment_status' => 'Lunas',
            ]);

            // Update registration status to active
            $payment->registration->update([
                'status' => 'active',
            ]);

            DB::commit();

            return redirect()->route('admin.payments')
                ->with('success', 'Pembayaran berhasil dikonfirmasi. Membership member sekarang aktif.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('admin.payments')
                ->with('error', 'Terjadi kesalahan saat mengonfirmasi pembayaran. Silakan coba lagi.');
        }
    }
}