<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard or redirect based on user role.
     *
     * @return \Illuminate\Http\Response
     */    public function index()
    {
        $user = auth()->user();

        // Create a default role for the user if they don't have one
        if (!$user->role) {
            // Create default User role
            $userRole = \App\Models\UserRole::create([
                'user_id' => $user->id,
                'role_name' => 'User',
                'description' => 'Regular user with limited access'
            ]);

            // Give user Create and Retrieve permissions
            $permissions = ['Create', 'Retrieve'];
            foreach ($permissions as $permission) {
                \App\Models\RolePermission::create([
                    'role_id' => $userRole->role_id,
                    'description' => $permission
                ]);
            }
        }

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('todo.index');
    }
}
