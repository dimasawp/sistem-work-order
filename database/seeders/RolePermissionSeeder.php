<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder {
    public function run(): void {
        // Roles
        Role::firstOrCreate(['name' => 'job-giver']);
        Role::firstOrCreate(['name' => 'job-receiver']);
    }
}
