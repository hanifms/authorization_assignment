<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        // Check if user has role, if not redirect to home to generate a role
        if (!$user->role) {
            return redirect()->route('home');
        }

        if (!$user->hasPermission($permission)) {
            // Show error message but still allow the user to view the page
            if ($permission === 'Retrieve') {
                // For retrieve permission, we should not block access to view
                session()->flash('error', 'You do not have permission to view these items, but we are allowing you temporary access.');
                return $next($request);
            }

            // For other permissions, redirect to the todos index
            return redirect()->route('todo.index')->with('error', 'You do not have permission to perform this action.');
        }

        return $next($request);
    }
}
