<?php namespace App\Services\TaskDependency;

use App\Models\Task;

interface TaskDependencyFinderInterface {
    public function taskHasDependency(Task $task);
}