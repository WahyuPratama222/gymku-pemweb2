<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Package;
use App\Models\Registration;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Get dashboard summary statistics
        $summary = $this->getDashboardSummary();

        // Get pending payments (5 latest)
        $pendingPayments = $this->getPendingPayments();

        return view('admin.dashboard', compact('summary', 'pendingPayments'));
    }

    /**
     * Get dashboard summary statistics
     */
    protected function getDashboardSummary()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        return [
            'total_members' => User::where('role', 'Member')->count(),
            'active_memberships' => Registration::where('status', 'Active')->count(),
            'expired_memberships' => Registration::where('status', 'Expired')->count(),
            'active_packages' => Package::where('status', 'Active')->count(),
            'income_today' => Payment::paid()
                ->whereDate('payment_date', $today)
                ->sum('amount'),
            'income_this_month' => Payment::paid()
                ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                ->sum('amount'),
        ];
    }

    /**
     * Get pending payments list
     */
    protected function getPendingPayments()
    {
        return Payment::with(['registration.user', 'registration.package'])
            ->pending()
            ->latest('payment_date')
            ->limit(5)
            ->get()
            ->map(function ($payment) {
                return [
                    'id_payment' => $payment->id_payment,
                    'member_name' => $payment->registration->user->name,
                    'package_name' => $payment->registration->package->name,
                    'amount' => $payment->amount,
                    'payment_date' => $payment->payment_date,
                ];
            });
    }
}