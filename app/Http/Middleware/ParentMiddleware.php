<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ParentMiddleware
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

        // Check if user has parent role
        if (!$user->isParent()) {
            abort(403, 'You do not have permission to access the parent panel.');
        }

        // Verify user has children (students) associated with their email
        $hasChildren = \App\Models\Student::where('parent_email', $user->email)
            ->orWhereHas('user', function ($query) use ($user) {
                $query->where('email', $user->email);
            })
            ->exists();

        if (!$hasChildren) {
            abort(403, 'No student records found associated with your account.');
        }

        return $next($request);
    }
}
