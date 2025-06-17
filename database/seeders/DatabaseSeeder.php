<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */    public function run(): void
    {
        // Run the user seeder first
        $this->call(UsersTableSeeder::class);

        // Seed roles and permissions after users are created
        $this->call(RolesAndPermissionsSeeder::class);
    }
}
