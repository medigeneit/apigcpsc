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

        $suprims = User::role(['Super Admin', 'Owner'])->get()->pluck('phone')->toArray();

        if (!in_array('00000000000', $suprims)) {
            $data = [
                'name' => 'Super Admin (Developers)',
                'phone' => '00000000000',
                'password'  => '123456',
                'hash_password'  => bcrypt('123456'),
            ];
            $user = User::create($data);
            $user ->assignRole('Super Admin');
        }
        if (!in_array('99999999999', $suprims)) {
            $data = [
                'name' => 'Owner (Developers)',
                'phone' => '99999999999',
                'password'  => '123456',
                'hash_password'  => bcrypt('123456'),
            ];
            $user = User::create($data);
            $user ->assignRole('Owner');
        }
    }
}
