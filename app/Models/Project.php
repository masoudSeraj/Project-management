<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'deadline_at', 'status'];

    public static $status = ['Active', 'Suspended', 'Completed'];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
