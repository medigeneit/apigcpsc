<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use phpDocumentor\Reflection\Types\Null_;

class ScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    static $support_type = Null;
    static $previous_counsellor_id = Null;

    private function possibility($percentage)
    {
        if ($percentage >= 60)
            $message = "high possibility";
        if ($percentage < 60 && $percentage >= 40)
            $message = "slightly high possibility";
        if ($percentage < 40 && $percentage >= 20)
            $message = "slightly low possibility";
        if ($percentage < 20)
            $message = "low possibility";
        return $message;
    }


    public function toArray($request)
    {

        $data = [];

        $data["id"] = (int) ($this->id);
        $data["chamber_id"] = (int) ($this->chamber_id);
        $data["date"] = (string) ($this->date);
        $data["time_schedule"] = (object) ($this->time_schedule);
        $appointment_count = count($this->appointments);

        foreach ($this->slot_threshold as $key => $value) {
            if ($key == self::$support_type) {
                // $data["slot_remains"] = (int)(($value->slot - $appointment_count) );
                $data["slot_remains"] = (float)(($value->slot - $appointment_count) / $value->slot * 100);
            }
        }
        // $data["mentor_possibility"] = (object) ($this->appointments->groupBy('requested_mentor_id')->sort()->map(function ($q) use ($appointment_count){
        //     return ($q->count()/ $appointment_count *100);
        // }));
        // $data["previous_counsellor_id"] = self::$previous_counsellor_id;

        if (self::$previous_counsellor_id != Null) {
            $data["mentor_probability"] = (float)(count(($this->appointments->Where('requested_mentor_id', self::$previous_counsellor_id)->sort())) / ($appointment_count + 1) * 100);
            $data["mentor_possibility"] = "Getting your requested mentor has a " . $this->possibility($data["mentor_probability"]) . ", Thank You";

            if (!$data["mentor_probability"]) {
                $data["mentor_probability"] = 100;
                $data["mentor_possibility"] = "You are appreciated to book this appointment, Thank you";
            }
        } else {
            $data["mentor_probability"] = 100;
            $data["mentor_possibility"] = "You are appreciated to book this appointment, Thank you";
        }


        return $data;
    }
}

/*
    {
        "id": 83,
        "chamber_id": 1,
        "date": "2022-01-06",
        "time_schedule": {
            "start_time": "07:30 AM",
            "end_time": "12:35 PM"
        },
        "slot_threshold": {
            "3": {
                "slot": 10,
                "threshold": 3
            },
            "4": {
                "slot": 10,
                "threshold": 2
            }
        },
        "appointments": [
            {
                "schedule_id": 83,
                "requested_mentor_id": 3
            },
            {
                "schedule_id": 83,
                "requested_mentor_id": 1
            },
            {
                "schedule_id": 83,
                "requested_mentor_id": 5
            },
            {
                "schedule_id": 83,
                "requested_mentor_id": 5
            },
            {
                "schedule_id": 83,
                "requested_mentor_id": 5
            },
            {
                "schedule_id": 83,
                "requested_mentor_id": 3
            }
        ]
    },
*/
