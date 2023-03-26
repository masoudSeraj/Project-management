<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\Factories\Sequence;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_project_index()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'admin', 'is_admin' => 1]);
        $user->assignRole('admin');

        $this->actingAs($user)->get(route('project.index'))->assertStatus(200);
    }

    public function test_not_admin_can_not_view_project_index()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'user', 'is_admin' => 0]);
        $user->assignRole('user');

        $this->actingAs($user)->get(route('project.index'))->assertRedirect(route('home'));
    }

    public function test_admin_can_create_new_project()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'admin', 'is_admin' => 1]);
        $user->assignRole('admin');

        $deadline = now()->addDays(1000);

        $response = $this->actingAs($user)->post(route('project.store'), [
            'title' => 'project1',
            'description'   => 'description1',
            'task' => 
            [
                ['title' => 'title1'],
                ['title' => 'title2']
            ],
            'date'  => $deadline,
            'status'    => 'Suspended'
        ]);
        
        $response->assertStatus(200);    
        $this->assertDatabaseHas('projects', [
            'title' => 'project1',
            'description'   =>  'description1',
            'status'        => 'Suspended',
            'deadline_at'   =>  $deadline,
            'changed_at'    => null
        ]);

        $this->assertDatabaseHas('tasks', [
            'project_id'    =>  $response->getData()->projectId,
            'title' =>  'title1'
        ]);

        $this->assertDatabaseHas('tasks', [
            'project_id'    =>  $response->getData()->projectId,
            'title' =>  'title2'
        ]);
    }

    public function test_admin_can_see_each_projects_details()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'admin', 'is_admin' => 1]);
        $user->assignRole('admin');

        $project = Project::factory()
        ->has(
            Task::factory()
            ->state(new Sequence(['title' => 'title1'], ['title' => 'title2'], ['title' => 'title3']))
            ->count(3)
        )
        ->create();

        $response = $this->actingAs($user)->getJson(route('project.show', ['project' => $project->id]));

        $response
            ->assertJson(fn (AssertableJson $json) =>
                    $json
                    ->has('tasks', 3)
                    ->where('tasks.0.title', 'title1')
                    ->where('tasks.1.title', 'title2')
                    ->where('tasks.2.title', 'title3')
                    ->etc()
            );
        $response->assertStatus(200);
        
        $response->assertJsonStructure(
            [
                'tasks' => [
                        '*' => [
                            'title',
                            'id'
                        ]
                ],
                'projectName',
                'projectDescription',
                'date',
                'status',
                'projectTasks'
            ]
        );
    }
    public function test_admin_can_edit_projects()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'admin', 'is_admin' => 1]);
        $user->assignRole('admin');

        $date = now()->addDays(10);
        $project = Project::factory()->state(['title' => 'project2'])
        ->has(
            Task::factory()
            ->state(new Sequence(['title' => 'title1'], ['title' => 'title2'], ['title' => 'title3']))
            ->count(3)
        )
        ->create();
        $params = [
            'projectName'   => 'Changed Project',
            'tasks' => [
                [
                    'id'    =>  $project->tasks->first()->id,
                    'title' => 'Changed title1'
                ]
        ],
            'projectDescription'    => 'Changed Description',
            'date' => $date,
            'status'    => 'completed'
        ];
        $response = $this->actingAs($user)->put(route('project.update', ['project' => $project->id]), $params);
        $response->assertStatus(200);
        $this->assertDatabaseHas('projects', [
            'id'   => $project->id,
            'title' => 'Changed Project',
            'description'   => 'Changed Description',
            'deadline_at'  => $date,
            'status'    => 'completed'
        ]);
        // dd($project->tasks->toArray());
        $project->tasks->each(fn($task, $key) =>
              $this->assertDatabaseHas('tasks', [
                'project_id' => $project->id,
                'title' =>  'Changed title1'
              ])
        );
    }

    public function test_projects_are_displayed_in_page(){
        $user = User::factory()->create();
        Role::create(['name' => 'admin', 'is_admin' => 1]);
        $user->assignRole('admin');
        
        // Task::create(['title' => 'task1', 'guard_name' => 'web']);
        // Task::create(['title' => 'task2', 'guard_name' => 'web']);

        $date = now()->addDays(10);
        $project = Project::factory()->state(['title' => 'project2'])
        ->has(
            Task::factory()
            ->state(new Sequence(['title' => 'title1'], ['title' => 'title2'], ['title' => 'title3']))
            ->count(3)
        )
        ->create();

        $this->actingAs($user)->get(route('project.index'))->assertStatus(200);
        
        $this->actingAs($user)->get(route('project.index'))->assertInertia(function(AssertableInertia $page) use($project){
            $page->component('Admin/Project/Index')
                ->has('projects', function(AssertableInertia $page) use($project){
                    $page
                        ->has('data', 1)
                        ->where('data.0.title', 'project2')
                        ->etc();
                })
                ->etc();
        });
    }
}
