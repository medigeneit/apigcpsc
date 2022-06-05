<?php

namespace App\Http\Controllers;

use App\Models\Administration;
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




    public function mentor_index()
    {
        return
        $roles = Role::with('users')->where('type', 2)->get();
        return $roles[0]->users;

        $user = User::first();

        return $user->getRoleNames();
        Administration::join('users','users.id','=','administration.user_id')
        // ->where('users.id','administration.user_id')
        ->where('type',2)
        ->orwhe9re('type',3)
        ->select('users.*')
        ->get();

        // $sql = "SELECT u.* FROM administration as ad join users as u on ad.user_id=u.id where ad.type = 2 or ad.type = 3";
        // return
        // DB::select($sql);

    }

    public function time(Request $request)
    {

        return
        Schedule::setValueOfTimeScheduleAttribute($request->time);


        return [
            time(),
            date("d/m/Y", time()),
            now(),
            Carbon::now()
        ];
    }
}
