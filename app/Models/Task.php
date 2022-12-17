<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'title', 'description', 'task_id', 'started_at'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
