<?php

namespace App\Http\Controllers;

use App\Http\Resources\FeedbackResource;
use App\Models\Appointment;
use App\Models\Feedback;
use App\Models\FeedbackQuestion;
use App\Models\Rating;
use App\Models\RatingRatio;
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

    //  private function FunctionName(Request $request, $feedbacks)
    //  {
    //     if($request->type == 1){
    //         return
    //         $feedbacks_questions[0]->feedbacks->groupBy('appointments.user_id');

    //     }elseif($request->type == 0){
    //         return
    //         $feedbacks_questions[0]->feedbacks->groupBy('appointments.mentor_feedbacks.mentor_id');

    //     }
    //  }



    public function index(Request $request)
    {
        //
        if(!$request->has('type')){
            return [
                'message' => "Please select any feedback type",
                'success' => false
            ];
        }

        $feedbacks_questions = FeedbackQuestion::with('user_ratings.user:id,name,phone,gender','rating_ratio')
        ->where('type', $request->type)
        ->latest()
        ->paginate(1);

        return
        FeedbackResource::collection($feedbacks_questions);



         
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

        // return ;

        // return $feedbacks_question = FeedbackQuestion::with('rating_ratio')->find($request->form_data['fq_id']);


        //
        // return $request;
        // return $request->form_data['appointment_id'];
        // return json_encode($request->rating);
        $feedback = Feedback::create([
            'fq_id' => (int)$request->form_data['fq_id'],
            'appointment_id' => $request->form_data['appointment_id'],
            'mentor_id' => $request->mentor_id ?? Null,
            'ratings' => $request->rating,
            'note' => $request->form_data['note'],
        ]);


        if ($request->mentor_id) {
            $user_id = Appointment::find($request->form_data['appointment_id'])->patient->id;
        } else {
            $user_id = Appointment::find($request->form_data['appointment_id'])->mentor->id;
        }

        $all_rating = Rating::where([
            'fq_id' => (int)$request->form_data['fq_id'],
            'user_id' => $user_id
        ])->first();

        if ($all_rating) {
            $sum_ratings =  $all_rating->sum_ratings;
            $count =  $all_rating->count+1;

            foreach (json_decode($request->rating) as $key => $rating) {
                $sum_ratings[$key] += $rating;
            }
            $all_rating->update([
                'sum_ratings' => $sum_ratings,
                'count' => $count
            ]);
        } else {
            Rating::create([
                'fq_id' => $request->form_data['fq_id'],
                'user_id' => $user_id,
                'sum_ratings' => $request->rating,
                'count' => $count =  1
            ]);
        }

        $feedbacks_question = FeedbackQuestion::with('rating_ratio')->find($request->form_data['fq_id']);
        $rating_ratio =  $feedbacks_question->rating_ratio;
        $data=[];
        if($feedbacks_question->rating_ratio->isEmpty()){
            foreach ($feedbacks_question->questions as $key => $question) {
                // $model_rating[$key] =  0;
                $rat_key =json_decode($request->rating)[$key];
                // return[json_decode($request->rating),$rat_key];
                // return gettype($key);
                $data[] = RatingRatio::create([
                    'fq_id' => $request->form_data['fq_id'],
                    'question_key' => $key,
                    "$rat_key".'_star' => 1,
                ]);
            }
            // return ($data);
        }else{
            // return $feedbacks_question;
            foreach ($feedbacks_question->questions as $key => $question) {
                // $model_rating[$key] =  0;
                $rat_key =json_decode($request->rating)[$key];
                $updated_rating_ratio = $rating_ratio->where('question_key', $key)->first()->{"$rat_key".'_star'} + 1;
                $rating_ratio->where('question_key', $key)->first()->update([
                    "$rat_key".'_star' => $updated_rating_ratio
                ]);
            }
        }


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
