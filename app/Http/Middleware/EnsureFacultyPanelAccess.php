<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureFacultyPanelAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('filament.faculty.auth.login');
        }

        // Only teachers, principals, and school admins can access the faculty panel
        $allowedRoles = ['teacher', 'principal', 'school_admin'];

        if (!$user->hasAnyRole($allowedRoles)) {
            abort(403, 'You do not have permission to access this panel.');
        }

        // Must be assigned to a school (except super admins)
        if (!$user->isSuperAdmin() && !$user->school_id) {
            abort(403, 'You must be assigned to a school to access this panel.');
        }

        return $next($request);
    }
}
