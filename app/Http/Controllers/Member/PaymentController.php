<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Display payment history for the member.
     */
    public function index()
    {
        $user = Auth::user();

        // Get payment history with related data
        $payments = Payment::with(['registration.package'])
            ->whereHas('registration', function ($query) use ($user) {
                $query->where('id_user', $user->id_user);
            })
            ->latest('payment_date')
            ->get()
            ->map(function ($payment) {
                return (object) [
                    'id_payment' => $payment->id_payment,
                    'payment_date' => $payment->payment_date,
                    'payment_method' => $payment->payment_method,
                    'payment_status' => $payment->payment_status,
                    'amount' => $payment->amount,
                    'package_name' => $payment->registration->package->name,
                    'expiry_date' => $payment->registration->expiry_date,
                ];
            });

        return view('member.payments', compact('payments'));
    }
}