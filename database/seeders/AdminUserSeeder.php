<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!User::where('email', 'admin@admin.com')->exists()) {
            // Create the admin user
            $admin = User::create([
                'name' => 'Administrator',
                'email' => 'admin@motherson.com',
                'password' => Hash::make('eGj,jO{1Jc8nP0P?OC.x'), // You should hash the password for security
            ]);

            // Get the ID of the 'admin' role from the pivot table
            $adminRoleId = 1; // Assuming 'admin' role ID is 1, you should adjust this accordingly

            // Associate the admin user with the 'admin' role using the pivot table
            $admin->roles()->attach($adminRoleId);
        }
    }
}
