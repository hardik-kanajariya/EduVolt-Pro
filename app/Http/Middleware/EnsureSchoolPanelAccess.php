<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSchoolPanelAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('filament.school.auth.login');
        }

        // Only school admins, principals, teachers, accountants, and librarians can access the school panel
        $allowedRoles = ['school_admin', 'principal', 'teacher', 'accountant', 'librarian'];

        if (!$user->hasAnyRole($allowedRoles)) {
            abort(403, 'You do not have permission to access this panel.');
        }

        // School admins and staff must be assigned to a school (except super admins)
        if (!$user->isSuperAdmin() && !$user->school_id) {
            abort(403, 'You must be assigned to a school to access this panel.');
        }

        return $next($request);
    }
}
