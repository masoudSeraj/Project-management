<?php

namespace App\Http\Resources;

use App\Http\Resources\ProjectResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'task_id'       =>  $this->task_id,
            'project_id'    =>  $this->project_id,
            'title'         =>  $this->title,
            'description'   =>  $this->description,
            'deadline_at'   =>  $this->deadline_at,
            'taskStarted'    =>  $this->started_at ? true : false ,
            'started_at'    =>  $this->started_at ?? '',
            'paused_at'     =>  $this->paused_at ?? '',
            'project'       =>  new ProjectResource($this->project),
            'tasks'         =>  $this->project->tasks->map(function($task){
                                    return ['id' => $task->id, 'value' => $task->title];
                                }),
            'value'         =>  $this->dependencies->map(function($task){
                return ['id' => $task->id, 'value' => $task->title];
            })
            // 'project'       => $this->project
        ];
    }
}
