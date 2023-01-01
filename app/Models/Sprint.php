<?php

namespace App\Models;

use App\Models\Task;
use PhpParser\Node\Expr\FuncCall;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sprint extends Model
{
    use HasFactory;

    protected $guarded = [];

    public Function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

}
