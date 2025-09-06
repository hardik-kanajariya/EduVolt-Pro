<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('filament.student.auth.login');
        }

        // Check if user has student role
        if (!$user->hasRole('student')) {
            Auth::logout();
            return redirect()->route('filament.student.auth.login')
                ->withErrors(['error' => 'Access denied. Student access required.']);
        }

        // Check if user has a student profile
        if (!$user->student) {
            Auth::logout();
            return redirect()->route('filament.student.auth.login')
                ->withErrors(['error' => 'Student profile not found.']);
        }

        // Check if student is active
        if ($user->student->status !== 'active') {
            Auth::logout();
            return redirect()->route('filament.student.auth.login')
                ->withErrors(['error' => 'Student account is not active.']);
        }

        return $next($request);
    }
}
