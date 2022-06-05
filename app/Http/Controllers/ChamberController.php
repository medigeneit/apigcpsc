<?php

namespace App\Http\Controllers;

use App\Models\Chamber;
use Illuminate\Http\Request;

class ChamberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return
        Chamber::get();
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
        // return $request;
        $chamber = new Chamber;
        $chamber->name = $request->name;
        $chamber->address = $request->address;
        $chamber->save();

        if(!$chamber) {
            return [
                'message' => 'Something is wrong...!',
                'success' => false
            ];
        }
        return [
            'message' => 'Record Successfully.',
            'success' => true
        ];

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Chamber  $chamber
     * @return \Illuminate\Http\Response
     */
    public function show(Chamber $chamber)
    {
        return $chamber;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Chamber  $chamber
     * @return \Illuminate\Http\Response
     */
    public function edit(Chamber $chamber)
    {
        //
        return $chamber;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Chamber  $chamber
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Chamber $chamber)
    {
        // return $request;
        $chamber->name = $request->name;
        $chamber->address = $request->address;
        $chamber->save();

        if(!$chamber) {
            return [
                'message' => 'Something is wrong...!',
                'success' => false
            ];
        }
        return [
            'message' => 'Record Updated Successfully.',
            'success' => true
        ];

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Chamber  $chamber
     * @return \Illuminate\Http\Response
     */
    public function destroy(Chamber $chamber)
    {
        if(!$chamber) {
            return [
                'message' => 'Something is wrong...!',
                'success' => false
            ];
        }
        
        Chamber::destroy($chamber->id);
        return [
            'message' => 'Record Deleted Successfully.',
            'success' => true
        ];
    }
}
