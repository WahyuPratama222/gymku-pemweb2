<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Registration;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the member dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // Get active membership (sesuai enum di migration: 'Active')
        $activeMembership = Registration::with('package')
            ->where('id_user', $user->id_user)
            ->where('status', 'Active')
            ->latest('registration_date')
            ->first();

        // Calculate days remaining
        $daysRemaining = 0;
        if ($activeMembership) {
            $daysRemaining = $activeMembership->days_remaining;
        }

        // Get recent payments (last 5)
        $recentPayments = Payment::with(['registration.package'])
            ->whereHas('registration', function ($query) use ($user) {
                $query->where('id_user', $user->id_user);
            })
            ->latest('payment_date')
            ->limit(5)
            ->get();

        return view('member.dashboard', compact(
            'activeMembership',
            'daysRemaining',
            'recentPayments'
        ));
    }
}