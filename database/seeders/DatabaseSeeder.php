<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $roles = ['MOH', 'PHC Manager', 'Health Information Officer', 'Nurse/Midwife'];
        foreach ($roles as $roleName) {
            Role::create(['name' => $roleName]);
        }

        // Create Permissions
        $permissions = [
            'read_operating_schedules',
            'write_operating_schedules',
            'read_consulting_rooms',
            'write_consulting_rooms',
            // Add more permissions as needed
        ];

        foreach ($permissions as $permissionName) {
            Permission::create(['name' => $permissionName]);
        }

        // Assign Permissions to Roles (example)
        $moh = Role::where('name', 'MOH')->first();
        $permissions = Permission::all();
        $moh->permissions()->attach($permissions); // MOH gets all permissions
    }

}
