<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    /**
     * Display list of all members with their latest membership.
     */
    public function index()
    {
        // Get all members with their latest registration and package info
        $members = User::where('role', 'Member')
            ->with(['registrations' => function ($query) {
                $query->latest('registration_date');
            }, 'registrations.package'])
            ->get()
            ->map(function ($user) {
                // Get latest registration
                $latestRegistration = $user->registrations->first();

                return (object) [
                    'id_user' => $user->id_user,
                    'member_name' => $user->name,
                    'email' => $user->email,
                    'gender' => $user->gender,
                    'joined_at' => $user->created_at,
                    
                    // Latest membership data
                    'id_registration' => $latestRegistration?->id_registration,
                    'registration_date' => $latestRegistration?->registration_date,
                    'start_date' => $latestRegistration?->start_date,
                    'expiry_date' => $latestRegistration?->expiry_date,
                    'status' => $latestRegistration?->status,
                    
                    // Package data
                    'id_package' => $latestRegistration?->package?->id_package,
                    'package_name' => $latestRegistration?->package?->name,
                    'price' => $latestRegistration?->package?->price,
                    'day_duration' => $latestRegistration?->package?->day_duration,
                ];
            });

        return view('admin.members', compact('members'));
    }
}