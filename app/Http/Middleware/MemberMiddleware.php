<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MemberMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has member role
        if (auth()->check() && auth()->user()->role === 'Member') {
            return $next($request);
        }

        // Redirect to home if not authorized
        return redirect()->route('home')->with('error', 'Unauthorized access. Member only.');
    }
}