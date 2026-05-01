<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Registration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

        // Get chart data for payments
        $chartData = $this->getPaymentChartData();

        return view('admin.payments', compact('payments', 'chartData'));
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

    /**
     * Get chart data for payments
     */
    protected function getPaymentChartData()
    {
        // Monthly revenue for last 12 months
        $monthlyRevenue = $this->getMonthlyRevenueData();
        
        // Payment methods breakdown
        $paymentMethods = $this->getPaymentMethodsData();

        return [
            'monthlyRevenue' => $monthlyRevenue,
            'paymentMethods' => $paymentMethods,
        ];
    }

    /**
     * Get monthly revenue data for last 12 months
     */
    protected function getMonthlyRevenueData()
    {
        $labels = [];
        $data = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');
            
            $revenue = Payment::paid()
                ->whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount');
                
            $data[] = $revenue;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get payment methods breakdown
     */
    protected function getPaymentMethodsData()
    {
        $methods = Payment::paid()
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get();

        $labels = [];
        $data = [];
        
        foreach ($methods as $method) {
            $labels[] = $method->payment_method;
            $data[] = $method->total;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}