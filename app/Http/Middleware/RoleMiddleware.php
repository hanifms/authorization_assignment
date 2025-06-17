<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        // Check if user has role, if not redirect to home to generate a role
        if (!$user->role) {
            return redirect()->route('home');
        }

        if (!$user->hasRole($role)) {
            // If trying to access admin routes, redirect to user's todo page
            if ($role === 'Administrator') {
                return redirect()->route('todo.index')->with('error', 'You do not have administrator privileges.');
            }

            return redirect()->route('home')->with('error', 'Role ' . $role . ' is required to access this page.');
        }

        return $next($request);
    }
}
