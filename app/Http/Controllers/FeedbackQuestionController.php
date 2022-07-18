<?php

namespace App\Http\Controllers;

use App\Models\FeedbackQuestion;
use Illuminate\Http\Request;

class FeedbackQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $feedbackQuestion = FeedbackQuestion::where('type', $request->type ? $request->type : 0 )->pluck('questions');
        return [
            'question' =>  $feedbackQuestion
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return FeedbackQuestion::$Types;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $questions = [];

        foreach ($request->questions as $value) {
            $questions[] = $value;
        }
        // return $questions;
        //
        FeedbackQuestion::create([
            'type' => $request->type,
            'questions' => $questions,
        ]);
        // FeedbackQuestion::create([
        //     'type'=> 1,
        //     'questions'=> json_encode([
        //         '1'=> "Mentor Feed back question 1",
        //         '2'=> "Mentor Feed back question 2",
        //         '3'=> "Mentor Feed back question 3",
        //         '4'=> "Mentor Feed back question 4",
        //         '5'=> "Mentor Feed back question 5",
        //     ]),
        // ]);
        // FeedbackQuestion::create([
        //     'type'=> 0,
        //     'questions'=> json_encode([
        //         '1'=> "Student Feed back question 1",
        //         '2'=> "Student Feed back question 2",
        //         '3'=> "Student Feed back question 3",
        //         '4'=> "Student Feed back question 4",
        //         '5'=> "Student Feed back question 5",
        //     ]),
        // ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FeedbackQuestion  $feedbackQuestion
     * @return \Illuminate\Http\Response
     */
    public function show(FeedbackQuestion $feedbackQuestion)
    {
        // return 6464;
        return $feedbackQuestion;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FeedbackQuestion  $feedbackQuestion
     * @return \Illuminate\Http\Response
     */
    public function edit(FeedbackQuestion $feedbackQuestion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FeedbackQuestion  $feedbackQuestion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FeedbackQuestion $feedbackQuestion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FeedbackQuestion  $feedbackQuestion
     * @return \Illuminate\Http\Response
     */
    public function destroy(FeedbackQuestion $feedbackQuestion)
    {
        //
    }
}
