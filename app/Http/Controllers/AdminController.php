<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Todo;
use App\Models\UserRole;
use App\Models\RolePermission;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:Administrator');
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $users = User::whereHas('role', function ($query) {
            $query->where('role_name', 'User');
        })->get();

        return view('admin.dashboard', compact('users'));
    }

    /**
     * Show a specific user's todos.
     *
     * @param  int  $userId
     * @return \Illuminate\View\View
     */
    public function userTodos($userId)
    {
        $user = User::findOrFail($userId);
        $todos = Todo::where('user_id', $userId)->get();

        return view('admin.user-todos', compact('user', 'todos'));
    }

    /**
     * Toggle user activation status.
     *
     * @param  int  $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleActivation($userId)
    {
        $user = User::findOrFail($userId);

        // Here we can implement the actual activation toggle
        // For now, let's add a simple 'is_active' flag to our response
        $isActive = !$user->is_active;
        $user->is_active = $isActive;
        $user->save();

        $status = $isActive ? 'activated' : 'deactivated';
        return redirect()->route('admin.dashboard')
            ->with('success', "User {$user->name} has been {$status}.");
    }

    /**
     * Delete a user.
     *
     * @param  int  $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);
        $name = $user->name;

        $user->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', "User {$name} has been deleted.");
    }

    /**
     * Show the permissions management page for a user.
     *
     * @param  int  $userId
     * @return \Illuminate\View\View
     */
    public function managePermissions($userId)
    {
        $user = User::findOrFail($userId);
        $userRole = $user->role;

        $availablePermissions = ['Create', 'Retrieve', 'Update', 'Delete'];
        $userPermissions = $userRole->permissions->pluck('description')->toArray();

        return view('admin.manage-permissions', compact('user', 'availablePermissions', 'userPermissions'));
    }

    /**
     * Update permissions for a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePermissions(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $userRole = $user->role;

        // Delete existing permissions
        RolePermission::where('role_id', $userRole->role_id)->delete();

        // Add new permissions
        $permissions = $request->input('permissions', []);
        foreach ($permissions as $permission) {
            RolePermission::create([
                'role_id' => $userRole->role_id,
                'description' => $permission
            ]);
        }

        return redirect()->route('admin.permissions', $userId)
            ->with('success', "Permissions for {$user->name} have been updated.");
    }
}
