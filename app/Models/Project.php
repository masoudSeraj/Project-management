<?php

namespace App\Models;

use App\Models\Task;
use App\Models\Sprint;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static $status = ['Active', 'Suspended', 'Completed'];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function sprints()
    {
        return $this->hasMany(Sprint::class);
    }

    public function projectSprints()
    {
        return $this->hasManyThrough(Sprint::class, Task::class);
    }
}
