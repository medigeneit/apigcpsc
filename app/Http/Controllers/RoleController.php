<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        // $this->middleware('role:Owner|Administrator');
    }

    public function index()
    {
        // if(!request()->flag) {
        //     return view('admin.roles.index');
        // }
        $roles = Role::query()
            ->where('name', '!=', 'Owner')
            ->where('name', '!=', 'Administrator')
            ->paginate(request()->perpage);

        return view('admin.roles.data', compact('roles'));
    }

    public function create()
    {
        return  [
            'role'          => Role::pluck('name','id','type')
            // 'permissions'   => Permission::get(),
            // 'assigns'       => [],
        ];


    }

    public function store(Request $request)
    {
        $role = Role::create($this->validation($request));

        $role->syncPermissions($request->permission);

        return redirect()
            ->route('admin.roles.show', $role->id)
            ->with('status', 'The record has been successfully created');
    }

    public function show(Role $role)
    {
        if (in_array($role->name, ['Owner', 'Administrator'])) {
            return abort(404);
        }

        return view('admin.roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        if (in_array($role->name, ['Owner', 'Administrator'])) {
            return abort(404);
        }

        return view('admin.roles.edit', [
            'role'          => $role,
            'permissions'   => Permission::get(),
            'assigns'       => $role->permissions->pluck('id')->toArray(),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        if (in_array($role->name, ['Owner', 'Administrator'])) {
            return abort(404);
        }

        $role->update($this->validation($request, $role->id));

        $role->syncPermissions($request->permission);

        return redirect()
            ->route('admin.roles.show', $role->id)
            ->with('status', 'The record has been successfully updated');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, ['Owner', 'Administrator'])) {
            return abort(404);
        }

        // return $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('status', 'The record has been successfully trashed');
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
        ]);
    }
}
