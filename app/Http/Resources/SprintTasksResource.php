<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SprintTasksResource extends JsonResource
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
            'sprint'            => $this->title,
            'degree'            => $this->degree,
            'started_at'        => $this->started_at,
            'deadline_at'       =>  $this->deadline_at,
            'availableTasks'    =>  new ProjectSprintTasksCollection($this->project->tasks->whereNotIn('id', $this->tasks->pluck('id'))),
            'selectedTasks'     =>  new ProjectSprintTasksCollection($this->tasks)
        ];
    }
}
