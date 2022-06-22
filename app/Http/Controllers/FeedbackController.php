<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Feedback;
use App\Models\FeedbackQuestion;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use phpDocumentor\Reflection\Types\Null_;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        return
        FeedbackQuestion::with('feedbacks')
        ->where('type',$request->type)
        ->when($request->type == 1, function($query){
            $query->with('feedbacks.mentor');
        })
        ->when($request->type == 0, function($query){
            $query->with('feedbacks.mentor');
        })
        ->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        // Schedule::$mentor_details = true;


        // return
        $appointment = Appointment::with('assign_mentor')->find($request->appointment_id);
        // $mentors = [];
        // foreach($appointment->schedule->mentors ?? [] as $type)
        // {
            //     $mentors [] = $type;
            // }
            $user_id = $request->user()->id;

            // if (in_array($user_id, Arr::collapse($mentors))) {
        if ($user_id == ($appointment->assign_mentor->mentor_id ?? 0)) {

            $questions = FeedbackQuestion::where('type', 1)
                ->latest()
                ->first();
        }
        elseif($appointment->user_id==$user_id ) {
            $questions = FeedbackQuestion::where('type', 0)
                ->latest()
                ->first();
        }
        // if (Role::find($user_id)->type == 1) {
        //     $questions = FeedbackQuestion::where('type', 0)
        //         ->latest()
        //         ->first();
        // }
        return [
            'appointment_id'    =>$request->appointment_id,
            'user_id'           =>$request->user_id,
            'questions'         =>$questions ?? []
        ];
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
        // return $request;
        $feedback = Feedback::create([
            'fq_id' => (int)($request->id),
            'appointment_id' => $request->appointment_id,
            'mentor_id' => $request->mentor_id ?? Null,
            'ratings' => $request->ratings,
            'note' => $request->note,
        ]);

        if($feedback){
            return [
                'message'=>"Thanks for your feedback",
                'success'=>true,
                'feedback'=>$feedback,
            ];

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Http\Response
     */
    public function show(Feedback $feedback)
    {
        //

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Http\Response
     */
    public function edit(Feedback $feedback)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Feedback $feedback)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Http\Response
     */
    public function destroy(Feedback $feedback)
    {
        //
    }
}
