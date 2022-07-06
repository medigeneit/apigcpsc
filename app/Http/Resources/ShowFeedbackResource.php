<?php

namespace App\Http\Resources;

use App\Models\Role;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowFeedbackResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = [];
        $data['appointment_id'] = $this->id;
        $data['schedule_id'] = $this->schedule_id;
        $data['type'] = Role::where('id',$this->type)->pluck('name');
        $data['questions'] = $this->questions;
        // $data['payable'] = $this->payable;
        // $data['payable'] = $this->payable;
        $data['patient_id'] = $this->patient->id;
        $data['patient_name'] = $this->patient->id;
        $data['patient_phone'] = $this->patient->phone;
        $data['patient_email'] = $this->patient->email;
        $data['patient_gender'] = $this->patient->gender;
        $data['mentor_id'] = $this->mentor->id;
        $data['mentor_name'] = $this->mentor->id;
        $data['mentor_phone'] = $this->mentor->phone;
        $data['mentor_email'] = $this->mentor->email;
        $data['mentor_gender'] = $this->mentor->gender;
        $data['patient_feedbacks_id'] = $this->user_feedbacks->id;
        $data['patient_feedbacks_ratings'] = $this->user_feedbacks->ratings;
        $data['patient_feedbacks_question'] = $this->user_feedbacks->question->questions;
        $data['mentor_feedbacks_id'] = $this->mentor_feedbacks->id;
        $data['mentor_feedbacks_ratings'] = $this->mentor_feedbacks->ratings;
        $data['mentor_feedbacks_question'] = $this->mentor_feedbacks->question->questions;
        return $data;
    }
}
