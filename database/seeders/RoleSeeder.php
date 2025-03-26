<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'Medical Officer of Health']);
        Role::create(['name' => 'PHC Manager']);
        Role::create(['name' => 'Health Information Officer']);
        Role::create(['name' => 'Nurse/Midwife']);
        Role::create(['name' => 'Pharmacist']);
        Role::create(['name' => 'Laboratory Technician']);
        Role::create(['name' => 'HIV Service Provider']);
        Role::create(['name' => 'Emergency Service Provider']);
        Role::create(['name' => 'TB Service Provider']);
        Role::create(['name' => 'Oral Health Officer']);
        Role::create(['name' => 'Health Educator']);
        Role::create(['name' => 'Family Planning Counsellor']);

        $role = Role::where('name', 'Medical Officer of Health')->first();
        $permissions = Permission::whereIn('name', [
            'Read Operating Schedules',
            'Write Operating Schedules',
            'Read Consulting Rooms',
            'Write Consulting Rooms',
            // Add more permissions for this role
        ])->get();
        $role->permissions()->sync($permissions);
    }
}
