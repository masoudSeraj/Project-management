<?php namespace App\Services\TaskService;

use App\Models\Task;
use App\Models\Sprint;
use App\Models\Project;

class TaskService
{
    public function tasksForProjectSprint(Project $project)
    {
        $tasks = $project?->tasks;
        $sprintTasks = $this->tasksBySprint($project);
        $totaltasks = $tasks->diff($sprintTasks);

        return $totaltasks;
    }

    public function getLastSprintName(Project $project) :?string
    {
        return $project?->sprints()->orderBy('degree', 'DESC')->first()->degree;
    }

    
    private function tasksBySprint($project)
    {
        $sprint_ids = $project?->sprints->pluck('id');
        return Task::whereIn('sprint_id', $sprint_ids)->get();
    }
}