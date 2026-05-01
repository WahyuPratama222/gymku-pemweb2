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

        // Get data for charts
        $chartData = $this->getChartData();

        return view('admin.dashboard', compact('summary', 'pendingPayments', 'chartData'));
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

    /**
     * Get chart data for dashboard
     */
    protected function getChartData()
    {
        // Revenue chart - last 7 days
        $revenueData = $this->getRevenueChartData();
        
        // Member growth chart - last 6 months
        $memberGrowthData = $this->getMemberGrowthData();

        return [
            'revenue' => $revenueData,
            'memberGrowth' => $memberGrowthData,
        ];
    }

    /**
     * Get revenue data for last 7 days
     */
    protected function getRevenueChartData()
    {
        $labels = [];
        $data = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d M');
            
            $revenue = Payment::paid()
                ->whereDate('payment_date', $date->toDateString())
                ->sum('amount');
                
            $data[] = $revenue;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get member growth data for last 6 months
     */
    protected function getMemberGrowthData()
    {
        $labels = [];
        $data = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');
            
            $count = User::where('role', 'Member')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
                
            $data[] = $count;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}