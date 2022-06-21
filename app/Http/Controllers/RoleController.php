<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\SpatieRole;

class RoleController extends Controller
{
    public function __construct()
    {
        // $this->middleware('role:Owner|Super Admin');
    }

    public function index()
    {
        // if(!request()->flag) {
        //     return view('admin.roles.index');
        // }
        $roles = Role::query()
            ->with('users')
            ->where('name', '!=', 'Owner')
            ->where('name', '!=', 'Super Admin')
            ->get();
        // ->paginate(request()->perpage);


        // dd($roles);

        return compact('roles');
    }

    public function create()
    {
        return  [
            'role'          => Role::pluck('name', 'id'),
            'permissions'   => Permission::pluck('name', 'id'),
            // 'permissions'   => Permission::get(),
            'types'       => Role::$TYPES,
        ];
    }

    public function store(Request $request)
    {
        $role = Role::create($this->validation($request));

        $role->syncPermissions($request->permission);

        return $this->index();
        // ->with('status', 'The record has been successfully created');
    }

    public function show(Role $role)
    {
        if (in_array($role->name, ['Owner', 'Super Admin'])) {
            return abort(404);
        }
        $role->load('permissions');
        return  compact('role');
    }

    public function edit(Role $role)
    {
        if (in_array($role->name, ['Owner', 'Super Admin'])) {
            return abort(404);
        }

        // $assigns = ;
        return  [
            'assigns'       =>  $role->permissions->pluck('id')->toArray(),
            'role'          => $role->unsetRelation('permissions'),
            'permissions'   => Permission::pluck('name', 'id'),
            'types'       => Role::$TYPES,
        ];
    }

    public function update(Request $request, Role $role)
    {
        if (in_array($role->name, ['Owner', 'Super Admin'])) {
            return abort(404);
        }

        $role->update($this->validation($request, $role->id));

        $role->syncPermissions($request->permission);

        return $this->index();
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, ['Owner', 'Super Admin'])) {
            return abort(404);
        }

        return $role->delete();

        return $this->index();
    }

    private function validation($request, $id = '')
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique(Role::class, 'name')
                    ->ignore($id),
            ],
            'type' => "required"
        ]);
    }
}
