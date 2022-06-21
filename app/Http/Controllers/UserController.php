<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }






    
    public function find_user(Request $request)
    {

        // return 341654;

        $user = User::where('phone', $request->phone)->first();

        if($user){
            return [
                'user'=>$user,
                'success'=>true,
            ];

        }else{
            return [
                'user'=>$user,
                'success'=>false,
            ];

        }


    }

    public function assigned_users()
    {
        $users =  User::with('roles')->has('roles')->get()->makeHidden(['created_at','updated_at','email_verified_at'])->map(function($user){
            // $user['all_roles'] = [];
            $user['name'] =  $user['name'] ?? '';
            $user['phone'] =  $user['phone'] ?? '';
            $user['email'] =  $user['email'] ?? '';
            $user['gender'] =  $user['gender'] ;
            $user['bmdc'] =  $user['bmdc'] ?? '';
            $user['medical'] =  $user['medical'] ?? '';
            $user['session'] =  $user['session'] ?? '';
            $user['all_roles'] =  $user['roles']->pluck(['name']);
            return $user;
        });

        return $users->makeHidden(['roles']);
        // return $users->unsetRelation('roles');
    }

    public function role_assign( Request $request)
    {
        // return $request;
        $user = User::find($request->user_id);
        $user->assignRole($request->role);
        return $this->assigned_users();
    }
    public function role_assign_edit( User $user)
    {
        // return $user;
        // $user = User::find($request->user_id);
        // $user->assignRole($request->role);
        // return
        $user_role = $user->roles->pluck('id');

        $roles = Role::query()
            ->where('name', '!=', 'Owner')
            ->where('name', '!=', 'Super Admin')
            ->get();

        return[
            'user'  => $user,
            'user_role'  => $user_role,
            'roles'  => $roles,
        ];
    }

    public function role_assign_delete(User $user)
    {
        // $user_role = $user->roles->pluck('id');
        $user->roles()->detach();

        return $this->assigned_users();

    }
    public function user_role (User $user)
    {
        // $user_role = $user->roles->pluck('id');

        return $user->load('roles');

    }
    // public function edit_role()
    // {
    //     $user->assignRole('writer');
    //     return User::with('roles')->get();
    // }
}
