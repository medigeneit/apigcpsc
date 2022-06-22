<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [];

        $permissionPrefix = [
            // 'User',
            // 'Mentors',
            'Schedule',
            'Appointment',
            'Feedback Questions',
            'Feedback',
            'Chamber',
            'Role',
            'Role Assign',
        ];

        $commonPart = [
            'List',
            'Create',
            'Edit',
            'Download'
        ];

        $permissions = DB::table('permissions')->pluck('name')->toArray() ?? [];

        foreach ($permissionPrefix as $prefix) {
            foreach ($commonPart as $part) {
                if (!in_array("{$prefix} {$part}", $permissions)) {
                    $data[] = [
                        'name' => "{$prefix} {$part}",
                        'guard_name' => 'web',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        $permissionPrefix2 = [
            // 'User',
            'Mentors assign',
        ];

        $commonPart2 = [
            // 'List',
            'Create',
            'Edit',
            // 'Download'
        ];
        foreach ($permissionPrefix2 as $prefix) {
            foreach ($commonPart2 as $part) {
                if (!in_array("{$prefix} {$part}", $permissions)) {
                    $data[] = [
                        'name' => "{$prefix} {$part}",
                        'guard_name' => 'web',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        DB::table('permissions')->insert($data);
    }
}
