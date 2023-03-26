<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function dependencies()
    {
        return $this->belongsToMany(Task::class, 'dependency_task', 'task_id', 'dependency_id')->withTimestamps();
    }

    public function parentTask()
    {
        return $this->belongsToMany(Task::class, 'dependency_task', 'dependency_id', 'task_id')->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'task_user');
    }

    public function sprint()
    {
        return $this->belongsTo(Sprint::class);
    }

    public function taskHasDependency()
    {   
        return $this->parentTask()->exists();
    }

    public function parentTaskNotFinished()
    {
        return $this->parentTask->status != 'Active';
    }

}
