<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::where('name', 'Admin')->first();
        $managerRole = Role::where('name', 'Manager')->first();
        $employeeRole = Role::where('name', 'Employee')->first();

        // Create Admin user
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
            ]
        );

        // Create Manager user
        User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager User',
                'password' => Hash::make('password'),
                'role_id' => $managerRole->id,
            ]
        );

        // Create Employee users
        User::firstOrCreate(
            ['email' => 'employee1@example.com'],
            [
                'name' => 'Employee One',
                'password' => Hash::make('password'),
                'role_id' => $employeeRole->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'employee2@example.com'],
            [
                'name' => 'Employee Two',
                'password' => Hash::make('password'),
                'role_id' => $employeeRole->id,
            ]
        );
    }
}