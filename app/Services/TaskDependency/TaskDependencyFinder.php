<?php namespace App\Services\TaskDependency;

use App\Models\Task;
use App\Models\TaskDependency;
use App\Services\TaskDependency\TaskDependencyFinderInterface;

class TaskDependencyFinder implements TaskDependencyFinderInterface {

    public function taskHasDependency(Task $task)
    {   
        return $task->parentTask()->exists();
    }

    public function parentTaskNotFinished(Task $task)
    {
        return $task->parentTask->status != 'Active';
    }
}