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
        $data['ratings'] = $this->id;
        $data['id'] = $this->id;
        $data['id'] = $this->id;
        $data['id'] = $this->id;
        $data['id'] = $this->id;
        $data['id'] = $this->id;
        $data['id'] = $this->id;
        return ;
    }
}
