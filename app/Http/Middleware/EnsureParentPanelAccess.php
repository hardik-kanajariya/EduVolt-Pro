<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureParentPanelAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('filament.parent.auth.login');
        }

        // Only parents can access the parent panel
        if (!$user->isParent()) {
            abort(403, 'You do not have permission to access this panel.');
        }

        return $next($request);
    }
}
