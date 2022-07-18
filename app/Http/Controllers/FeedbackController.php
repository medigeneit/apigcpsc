<?php

namespace App\Http\Controllers;

use App\Http\Resources\FeedbackResource;
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

        // return   
        $feedbacks_questions = FeedbackQuestion::with('feedbacks')
            ->where('type', $request->type)
            ->when($request->type == 1, function ($query) {
                $query->with('feedbacks:id,fq_id,ratings,appointment_id', 'feedbacks.appointments.patient');
            })
            ->when($request->type == 0, function ($query) {
                $query->with('feedbacks:id,fq_id,ratings,appointment_id', 'feedbacks.appointments.mentor_feedbacks.mentor');
            })
            ->latest()
            ->get();
            
            
            $feedback_rettings = [1, 2, 3, 4, 5];
            $data = []; {
                foreach ($feedbacks_questions as $feedbacks_question) {
                    $data['count'] = 0;
                    $data['questions'] =  $feedbacks_question->questions;
                    
                    foreach ($feedbacks_question->questions as $key => $question) {
                        foreach ($feedback_rettings as $retting) {
                            $data['question_rettings'][$key][$retting] = 0;
                    }
                }
                $feedback_array = $feedbacks_question->feedbacks;
                foreach ($feedback_array as $feedback) {
                    $data['count'] += 1;
                    foreach ($feedback->ratings as $key => $ret) {
                        $data['question_rettings'][$key][$ret] += 1;
                    }
                }
            }
        }
        
        
        $feedbacks_questions[0]->feedbacks->groupBy('appointments.mentor_feedbacks.mentor_id');
        
        
        $overall_retings = $feedbacks_questions[0]->feedbacks->groupBy('appointments.mentor_feedbacks.mentor_id')->map(function($group_feedbacks, $key){
            $overall_feedback =[];
            $overall_feedback = $group_feedbacks[0]->appointments->mentor_feedbacks->mentor;
            $overall_feedback ['ratings']= $group_feedbacks->pluck('ratings');
            $overall_feedback ['avg_ratings'] = array_filter($overall_feedback ['rettings'],function($query){

            });
            return  $overall_feedback ;
        });
        return $overall_retings->values() ;
        // $feedbacks_questions->feedbacks->map(function($feedback))

        // FeedbackResource::collection($feedbacks_questions);

        return  [$data, $feedbacks_questions];


        // {
        //     foreach ($feedbacks_questions as $feedbacks_question) {
        //         $feedback_array = $feedbacks_question->feedbacks->pluck('ratings')->toArray();
        //         $data['questions'] =  $feedbacks_question->questions;
        //         $data['question_rettings'] =  [];
        //         foreach ($feedbacks_question->questions as $feedback_position => $question) {
        //             foreach ($feedback_rettings as $feedback_value) {

        //                 $data['question_rettings'][$feedback_position][$feedback_value] = count(array_filter($feedback_array, function ($ratings) use ($feedback_position, $feedback_value) {
        //                     return $ratings[$feedback_position] == $feedback_value;
        //                 }));
        //             }
        //         }
        //     }
        // }

        return $data;
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
        } elseif ($appointment->user_id == $user_id) {
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
            'appointment_id'    => $request->appointment_id,
            'user_id'           => $user_id,
            'questions'         => $questions ?? []
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
        // return $request->form_data['appointment_id'];
        // return json_encode($request->rating);
        $feedback = Feedback::create([
            'fq_id' => (int)$request->form_data['fq_id'],
            'appointment_id' => $request->form_data['appointment_id'],
            'mentor_id' => $request->mentor_id ?? Null,
            'ratings' => $request->rating,
            'note' => $request->form_data['note'],
        ]);

        if ($feedback) {
            return [
                'message' => "Thanks for your feedback",
                'success' => true,
                'feedback' => $feedback,
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
        return  $feedback;
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
