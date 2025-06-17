<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserRole;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find admin user by email
        $adminUser = User::where('email', 'admin@example.com')->first();

        if ($adminUser) {
            // Delete existing role if exists
            UserRole::where('user_id', $adminUser->id)->delete();

            // Create admin role
            $adminRole = UserRole::create([
                'user_id' => $adminUser->id,
                'role_name' => 'Administrator',
                'description' => 'Full access to all features'
            ]);

            // Add all permissions to admin role
            $adminPermissions = ['Create', 'Retrieve', 'Update', 'Delete'];
            foreach ($adminPermissions as $permission) {
                RolePermission::create([
                    'role_id' => $adminRole->role_id,
                    'description' => $permission
                ]);
            }
        }

        // Create regular user role for all other existing users
        $regularUsers = User::where('email', '!=', 'admin@example.com')->get();
        foreach ($regularUsers as $user) {
            // Delete existing role if exists
            UserRole::where('user_id', $user->id)->delete();

            // Create user role
            $userRole = UserRole::create([
                'user_id' => $user->id,
                'role_name' => 'User',
                'description' => 'Regular user with limited access'
            ]);

            // Give regular users Create and Retrieve permissions only
            $userPermissions = ['Create', 'Retrieve'];
            foreach ($userPermissions as $permission) {
                RolePermission::create([
                    'role_id' => $userRole->role_id,
                    'description' => $permission
                ]);
            }
        }
    }
}
