<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LastAppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    static $types = [];
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'schedule_id' => $this->schedule_id,
            'serial' => $this->serial,
            'type_id' => $this->type,
            'type' => self::$types[$this->type],
            'questions' => $this->questions,
            'payable' => $this->payable,
            'requested_mentor_id' => $this->requested_mentor_id,
            'requested_mentor' => $this->requested_mentor->name ?? '',
            'assigned_mentor' => $this->assign_mentor->user ?? '',
            'assigned_mentor_id' => $this->assign_mentor->id ?? '',
            'patientName' => (string) ($this->patient->name ?? ''),
            'patientPhone' => (string) ($this->patient->Phone ?? ''),
            'patientEmail' => (string) ($this->patient->email ?? ''),
            'patientGender' => (string) ($this->patient->gender ?? ''),
            'patientBmdc' => (string) ($this->patient->bmdc ?? ''),
            'patientMedical' => (string) ($this->patient->medical ?? ''),
            'patientSession' => (string) ($this->patient->session ?? ''),

        ];
    }
}
