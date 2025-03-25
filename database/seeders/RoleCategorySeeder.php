<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleCategorySeeder extends Seeder
{
    public function run()
    {
        // Define role-category mappings with access levels
        $mappings = [
            // Medical Officer of Health (MOH)
            [
                'role_name' => 'Medical Officer of Health',
                'categories' => [
                    'Operating Schedules' => 'read_write',
                    'Consulting Rooms' => 'read_write',
                    'Labour Ward' => 'read_write',
                    'Treatment Rooms' => 'read_write',
                    'Observation Rooms' => 'read_write',
                    'In-Patient Wards' => 'read_write',
                    'Pharmacy' => 'read',
                    'Immunization Services' => 'read_write',
                    'Health Information Management' => 'read',
                    'Laboratory Services' => 'read',
                    'HIV Services' => 'read',
                    'Emergency Services' => 'read_write',
                    'TB Services' => 'read',
                    'Oral Health' => 'read',
                    'Health Education Services' => 'read',
                    'Family Planning' => 'read_write'
                ]
            ],

            // PHC Manager/Coordinator
            [
                'role_name' => 'PHC Manager',
                'categories' => [
                    'Operating Schedules' => 'read_write',
                    'Consulting Rooms' => 'read',
                    'Labour Ward' => 'read',
                    'Treatment Rooms' => 'read',
                    'Observation Rooms' => 'read',
                    'In-Patient Wards' => 'read',
                    'Pharmacy' => 'read',
                    'Immunization Services' => 'read',
                    'Health Information Management' => 'read',
                    'Laboratory Services' => 'read',
                    'HIV Services' => 'read',
                    'Emergency Services' => 'read',
                    'TB Services' => 'read',
                    'Oral Health' => 'read',
                    'Health Education Services' => 'read',
                    'Family Planning' => 'read'
                ]
            ],

            // Health Information Officer
            [
                'role_name' => 'Health Information Officer',
                'categories' => [
                    'Operating Schedules' => 'read_write',
                    'Consulting Rooms' => 'read',
                    'Labour Ward' => 'read_write',
                    'Treatment Rooms' => 'read_write',
                    'Observation Rooms' => 'read_write',
                    'In-Patient Wards' => 'read_write',
                    'Pharmacy' => 'read_write',
                    'Immunization Services' => 'read_write',
                    'Health Information Management' => 'read_write',
                    'Laboratory Services' => 'read_write',
                    'HIV Services' => 'read_write',
                    'Emergency Services' => 'read_write',
                    'TB Services' => 'read_write',
                    'Oral Health' => 'read_write',
                    'Health Education Services' => 'read_write',
                    'Family Planning' => 'read_write'
                ]
            ],

            // Nurse/Midwife
            [
                'role_name' => 'Nurse/Midwife',
                'categories' => [
                    'Operating Schedules' => 'read',
                    'Consulting Rooms' => 'read_write',
                    'Labour Ward' => 'read_write',
                    'Treatment Rooms' => 'read_write',
                    'Observation Rooms' => 'read_write',
                    'In-Patient Wards' => 'read_write',
                    'Pharmacy' => 'read',
                    'Immunization Services' => 'read_write',
                    'Health Information Management' => 'read',
                    'Laboratory Services' => 'read_limited',
                    'HIV Services' => 'read_limited',
                    'Emergency Services' => 'read_write',
                    'TB Services' => 'read_limited',
                    'Oral Health' => 'read_limited',
                    'Health Education Services' => 'read_limited',
                    'Family Planning' => 'read_write'
                ]
            ],

            // Pharmacist
            [
                'role_name' => 'Pharmacist',
                'categories' => [
                    'Operating Schedules' => 'read',
                    'Consulting Rooms' => 'read_limited',
                    'In-Patient Wards' => 'read_limited',
                    'Pharmacy' => 'read_write',
                    'Laboratory Services' => 'read_limited',
                    'Emergency Services' => 'read_limited'
                ]
            ],

            // Laboratory Technician
            [
                'role_name' => 'Laboratory Technician',
                'categories' => [
                    'Consulting Rooms' => 'read_limited',
                    'Labour Ward' => 'read_limited',
                    'Treatment Rooms' => 'read_limited',
                    'Observation Rooms' => 'read_limited',
                    'In-Patient Wards' => 'read_limited',
                    'Pharmacy' => 'read_limited',
                    'Laboratory Services' => 'read_write',
                    'HIV Services' => 'read_limited',
                    'Emergency Services' => 'read_limited',
                    'TB Services' => 'read_limited'
                ]
            ],

            // HIV Service Provider
            [
                'role_name' => 'HIV Service Provider',
                'categories' => [
                    'Consulting Rooms' => 'read_write_limited',
                    'Laboratory Services' => 'read_write_limited',
                    'HIV Services' => 'read_write',
                    'Health Education Services' => 'read_limited',
                    'Family Planning' => 'read_write_limited'
                ]
            ],

            // Emergency Service Provider
            [
                'role_name' => 'Emergency Service Provider',
                'categories' => [
                    'Operating Schedules' => 'read',
                    'Consulting Rooms' => 'read_write_emergency',
                    'Labour Ward' => 'read_write_emergency',
                    'Treatment Rooms' => 'read_write',
                    'Observation Rooms' => 'read_write',
                    'In-Patient Wards' => 'read_write',
                    'Pharmacy' => 'read_limited',
                    'Laboratory Services' => 'read_limited',
                    'Emergency Services' => 'read_write'
                ]
            ],

            // TB Service Provider
            [
                'role_name' => 'TB Service Provider',
                'categories' => [
                    'Consulting Rooms' => 'read_write_limited',
                    'Laboratory Services' => 'read_write_limited',
                    'TB Services' => 'read_write',
                    'Health Education Services' => 'read_limited'
                ]
            ],

            // Oral Health Officer
            [
                'role_name' => 'Oral Health Officer',
                'categories' => [
                    'Consulting Rooms' => 'read_write_dental',
                    'Oral Health' => 'read_write'
                ]
            ],

            // Health Educator
            [
                'role_name' => 'Health Educator',
                'categories' => [
                    'Consulting Rooms' => 'read_aggregated',
                    'Immunization Services' => 'read_aggregated',
                    'Health Education Services' => 'read_write'
                ]
            ],

            // Family Planning Counsellor
            [
                'role_name' => 'Family Planning Counsellor',
                'categories' => [
                    'Consulting Rooms' => 'read_write_family',
                    'Pharmacy' => 'read_limited',
                    'Laboratory Services' => 'read_limited',
                    'Health Education Services' => 'read_limited',
                    'Family Planning' => 'read_write'
                ]
            ]
        ];

        // Insert the mappings into role_category table
        foreach ($mappings as $mapping) {
            $roleId = DB::table('roles')
                       ->where('name', $mapping['role_name'])
                       ->value('id');

            if ($roleId) {
                foreach ($mapping['categories'] as $categoryName => $accessLevel) {
                    $categoryId = DB::table('categories')
                                  ->where('name', $categoryName)
                                  ->value('id');

                    if ($categoryId) {
                        DB::table('role_category')->insert([
                            'role_id' => $roleId,
                            'category_id' => $categoryId,
                            'access_level' => $accessLevel,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }
        }
    }
}
