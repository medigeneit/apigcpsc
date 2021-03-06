<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
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
    public function index(Request $request)
    {
        //
        // return $request;
        $user  = User::when($request->search, function($query) use($request){
            $query -> where('name','like', "%{$request->search}%")
             -> orWhere('phone','like', "%{$request->search}%");
        });
        // return  $user->paginate($request->perpage ?? 10);
        // return $user ->get();

        return UserResource::collection( $user->paginate($request->perpage ?? 10));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fields = $this->validation($request);
        $fields ['hash_password']  = bcrypt($fields['password']);
        // return  $fields ;
        $user = User::create($fields);

        if ($user) {
            return [
                'success' => true,
                'message' => 'Record saved successfully..'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Sorry !!!\\nRecord couldnot be saved...'
            ];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        // $user->makeVisible('password');
        // return

        UserResource::$EditPassword = true;

        $data = new UserResource($user);
        return $data;
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
        // return $request;

        $phone_change = $email_change = true;
        if($user->phone == $request->phone){
            $phone_change = false;
        }
        if($user->email == $request->email){
            $email_change = false;
        }
        // return[$user->phone == $request->phone, $phone_change , $email_change];
        $fields = $this->validation($request,$phone_change,$email_change);
        // return $fields;
        $user->update($fields);
        if ($user) {
            return [
                'success' => true,
                'message' => 'Record updated successfully..'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Sorry !!!\\nRecord couldnot be updated...'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $flag = $user->delete();
        if ($flag) {
            return [
                'success' => true,
                'message' => 'Record deleted successfully..'
            ];
        }else {
            return [
                'success' => false,
                'message' => 'Sorry !!!\\nRecord couldnot be deleted...'
            ];
        }
    }







    public function find_user(Request $request)
    {

        // return 341654;

        $user = User::where('phone', $request->phone)->first();

        if ($user) {
            return [
                'user' => $user,
                'success' => true,
            ];
        } else {
            return [
                'user' => $user,
                'success' => false,
            ];
        }
    }

    public function assigned_users()
    {
        $users =  User::with('roles')->has('roles')->get()->makeHidden(['created_at', 'updated_at', 'email_verified_at'])->map(function ($user) {
            // $user['all_roles'] = [];
            $user['name'] =  $user['name'] ?? '';
            $user['phone'] =  $user['phone'] ?? '';
            $user['email'] =  $user['email'] ?? '';
            $user['gender'] =  $user['gender'];
            $user['bmdc'] =  $user['bmdc'] ?? '';
            $user['medical'] =  $user['medical'] ?? '';
            $user['session'] =  $user['session'] ?? '';
            $user['all_roles'] =  $user['roles']->pluck(['name']);
            return $user;
        });

        return $users->makeHidden(['roles']);
        // return $users->unsetRelation('roles');
    }

    public function role_assign(Request $request)
    {
        // return $request;
        $user = User::find($request->user_id);
        $user->assignRole($request->role);
        return $this->assigned_users();
    }
    public function role_assign_update(Request $request)
    {
        // return $request;
        $user = User::find($request->user_id);
        $user->syncRoles($request->role);
        return $this->assigned_users();
    }
    public function role_assign_edit(User $user)
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

        return [
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
    public function user_role(User $user)
    {
        // $user_role = $user->roles->pluck('id');

        return $user->load('roles');
    }
    // public function edit_role()
    // {
    //     $user->assignRole('writer');
    //     return User::with('roles')->get();
    // }

    private function validation($request, $phone_change = true, $email_change = true)
    {
        // $phone_change =
        $fields = [
            'name'      => "required",
            'gender'    => "required",
            'bmdc'      => "",
            'medical'   => "",
            'password'   => "",
        ];
        if ($phone_change) {
            $fields['phone'] = "required|unique:users,phone";
        }
        if ($email_change) {
            $fields['email'] = "nullable|email|unique:users,email";

        }
        // return $fields;
        // return[ $phone_change , $email_change ];

        return $request->validate($fields);
    }
}
