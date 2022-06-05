<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MentorAssign;
use App\Models\RoleAssign;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MentorAssignController extends Controller
{
    public function __construct()
    {
        // $this->middleware('role:Owner|Administrator');
    }

    public function index()
    {
        // return 234;
        return MentorAssign::with('user', 'type', 'appointment.patient:id,name')->get();
        // return view('admin.roles.data', compact('roles'));
    }

    public function create()
    {
        // return 123;
        // return view('admin.roles.create', [
        //     'role'          => new Role(),
        //     'permissions'   => Permission::get(),
        //     'assigns'       => [],
        // ]);

        return [
            'roles'          => Role::get(),
            'users'        => User::pluck('name', 'id'),
            'patients'    => User::with('appointments')->get()  
        ];
    }

    public function store(Request $request)
    {
        $role = MentorAssign::create($this->validation($request));

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
