<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;

class mentorScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    static $chembers = null;
    static $mentor_id = 0;


    public function toArray($request)
    {
        $data = [];

        $data['id'] = $this->id ?? '';
        $data['date'] = $this->date ?? '';
        foreach (self::$chembers as $chember) {
            if ($chember->id == $this->chamber_id) {
                $data['chamber_id'] = $chember->id ?? 0;
                $data['chamber_name'] = $chember->name ?? '';
                $data['chamber_address'] = $chember->address ?? '';
            }
        }
        foreach ($this->mentors as $type => $mentors) {
            if (in_array((self::$mentor_id), $mentors)) {

                $data['type'] = (int)($type ?? 0);
            }
        }
        $data['time_schedule'] = $this->time_schedule ?? null;
        $data['total_appointments'] = count($this->appointments) ?? 0;

        return $data;
    }
}
