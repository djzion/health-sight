<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'Read Operating Schedules']);
        Permission::create(['name' => 'Write Operating Schedules']);
        Permission::create(['name' => 'Read Consulting Rooms']);
        Permission::create(['name' => 'Write Consulting Rooms']);
        Permission::create(['name' => 'Read Labour Ward']);
        Permission::create(['name' => 'Write Labour Ward']);
    }
}
