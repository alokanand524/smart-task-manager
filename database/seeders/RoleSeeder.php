<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'Admin'],
            ['name' => 'Manager'],
            ['name' => 'Employee']
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate($role);
        }
    }
}