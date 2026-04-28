<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Registration;
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
                    'member_name' => $payment->registration->user->name,
                    'member_email' => $payment->registration->user->email,
                    'package_name' => $payment->registration->package->name,
                    'amount' => $payment->amount,
                    'payment_date' => $payment->payment_date,
                    'payment_method' => $payment->payment_method,
                    'payment_status' => $payment->payment_status,
                    'id_registration' => $payment->id_registration,
                ];
            });

        return view('admin.payments', compact('payments'));
    }

    /**
     * Confirm payment and activate membership.
     */
    public function confirm($id)
    {
        try {
            DB::beginTransaction();

            $payment = Payment::with('registration')->findOrFail($id);

            // Update payment status to Lunas
            $payment->update([
                'payment_status' => 'Lunas',
            ]);

            // Update registration status to Active
            $payment->registration->update([
                'status' => 'Active',
            ]);

            DB::commit();

            return redirect()->route('admin.payments')
                ->with('success', 'Pembayaran berhasil dikonfirmasi dan membership diaktifkan.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('admin.payments')
                ->with('error', 'Terjadi kesalahan saat konfirmasi pembayaran.');
        }
    }
}
