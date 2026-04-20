<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes: middleware('role:admin') or middleware('role:member')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Normalize roles to lowercase for comparison
        $allowedRoles = array_map('strtolower', $roles);
        $userRole = strtolower($user->role);

        if (! in_array($userRole, $allowedRoles)) {
            // Redirect to appropriate dashboard instead of showing 403
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Kamu tidak memiliki akses ke halaman tersebut.');
            }

            return redirect()->route('member.dashboard')
                ->with('error', 'Kamu tidak memiliki akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
