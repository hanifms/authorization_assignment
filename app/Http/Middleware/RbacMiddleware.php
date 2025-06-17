<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RbacMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        if ($role === 'admin' && !$user->isAdmin()) {
            return redirect('home')->with('error', 'Unauthorized access.');
        }

        if ($role === 'user' && $user->isAdmin()) {
            return redirect('admin/dashboard');
        }

        return $next($request);
    }
}
