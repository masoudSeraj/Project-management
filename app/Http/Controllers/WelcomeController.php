<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectCollection;

class WelcomeController extends Controller
{
    public function index()
    {
        $projects = ProjectResource::collection(Project::whereHas('tasks.users', function($query){
            return $query->where('users.id', auth()->user()?->id);
        })->get());
        
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'projects'  => $projects
        ]);
    }
}
