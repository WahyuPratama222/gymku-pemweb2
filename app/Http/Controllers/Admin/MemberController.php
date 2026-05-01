<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    /**
     * Display list of all members with their latest membership.
     */
    public function index()
    {
        // Get all members with their latest registration and package info
        $members= User::where('role', 'Member')
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

        return view('admin.member', compact('members'));
    }

    /**
     * Store a newly created member.
     */
    public function store(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email|max:100',
            'gender' => 'required|in:Laki-Laki,Wanita',
            'password' => 'required|min:8|confirmed',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'gender.in' => 'Pilih jenis kelamin yang valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Create new member
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'gender' => $request->gender,
            'password' => Hash::make($request->password),
            'role' => 'Member',
        ]);

        return redirect()->route('admin.members')->with('success', 'Member berhasil didaftarkan.');
    }
}