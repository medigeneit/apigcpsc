<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssignRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $roles = [];
        $existing_roles =  Role::query()->get()->pluck('name')->toArray();

        if (!in_array('Owner', $existing_roles))
            $roles[] = [
                'name' => 'Owner',
                'guard_name' => 'web',
                'type' => '1',
            ];
        if (!in_array('Super Admin', $existing_roles))
            $roles[] = [
                'name' => 'Super Admin',
                'guard_name' => 'web',
                'type' => '1',
            ];

        if (count($roles))
            foreach ($roles as $role) {
                # code...
                Role::create($role);
            }



        #~~~~~~~~~~~~~~Assign Role~~~~~~~~~~~~~~~~~~~~~~~~~
        $suprims = User::role(['Super Admin', 'Owner'])->get()->pluck('phone')->toArray();

        $create_new_roles = [
            '00000000000' => 'Super Admin',
            '99999999999' => 'Owner',
            '11111111111' => 'Super Admin',
            '11111111111' => 'Exicutive',
        ];

        foreach ($create_new_roles as $phone => $role) {

            if (!in_array($phone, $suprims)) {
                $data = [
                    'name' => $role . ' (Developers)',
                    'phone' => $phone,
                    'password'  => '123456',
                    'hash_password'  => bcrypt('123456'),
                ];
                $user = User::where('phone', $phone)->first();
                if (!$user)
                    $user = User::create($data);
                $user->assignRole($role);
            } else {

                $user = User::where('phone', $phone)->first();
                $assigned_roles = $user->getRoleNames()->toArray();
                if (!in_array($role, $assigned_roles)) {
                    $user->assignRole($role);
                }
            }
        }
    }
}
