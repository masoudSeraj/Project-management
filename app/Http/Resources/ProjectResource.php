<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'            =>  $this->id,
            'title'         =>  $this->title,
            'description'   =>  $this->description,
            'status'        =>  $this->status,
            'started_at'    =>  (string) verta($this->created_at, 'Asia/Tehran'),
            'deadline_at'   =>  (string) verta($this->deadline_at, 'Asia/Tehran'),
            'changed_at'    =>  $this->changed_at
        ];
    }
}
