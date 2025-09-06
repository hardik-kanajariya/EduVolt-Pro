<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentPanelAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('filament.student.auth.login');
        }

        // Only students can access the student panel
        if (!$user->isStudent()) {
            abort(403, 'You do not have permission to access this panel.');
        }

        // Students must be assigned to a school
        if (!$user->school_id) {
            abort(403, 'You must be assigned to a school to access this panel.');
        }

        return $next($request);
    }
}
