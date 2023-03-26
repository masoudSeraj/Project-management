<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\Sequence;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_task_index()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'admin', 'is_admin' => 1]);
        $user->assignRole('admin');

        $this->actingAs($user)->get(route('task.index'))->assertStatus(200);    
    }

    // public function test_admin_can_update_tasks()
    // {
    //     $user = User::factory()->create();
    //     Role::create(['name' => 'admin', 'is_admin' => 1]);
    //     $user->assignRole('admin');
    //     $date = now()->addDays(10);

    //     $project = Project::factory()->state(['title' => 'project1'])->create();
    //     $tasks = Task::factory()
    //         ->count(3)
    //         ->state(new Sequence(
    //                 ['title' => 'title1'], 
    //                 ['title' => 'title2'], 
    //                 ['title' => 'title3'])
    //         )->for($project)->create();
        
                
    //     $response = $this->actingAs($user)->put(route('task.update'), );
    // }
}
