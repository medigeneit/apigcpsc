<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FeedbackResource extends JsonResource
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

        $data['id'] = $this->id;
        $data['type'] = $this->type;
        $data['questions'] = $this->questions;
        $data['user_ratings'] = UserRatingsRatioResource::collection($this->user_ratings);
        $data['rating_ratio'] = $this->rating_ratio;
        return $data;
    }
}
