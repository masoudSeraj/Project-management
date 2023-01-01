<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\Sprint;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TaskService\TaskService;
use App\Http\Resources\ProjectSprintTasksCollection;

class ProjectSprintTasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Project $project, TaskService $taskService)
    {
        return new ProjectSprintTasksCollection($taskService->tasksForProjectSprint($project));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Project $project)
    {
        // dd($request->all());
        $request->validate([
            'sprint'    => 'required',
            'degree'    =>  'required|unique:sprints',
            'tasks.*'   =>  'required',
            'startDate' =>  'required',
            'endDate'   =>  'required'
        ],
        [
            'required' => 'the :attribute field is required!',
            'unique'    =>  'the :attribute field has to be unique!'
        ]
    );
        try{
            $sprint = Sprint::create([
                'project_id'    =>  $project->id,
                'title'         =>  $request->sprint,
                'degree'        =>  $request->degree,
                'started_at'    =>  Carbon::createFromFormat('Y-m-d H:i:s', $request->startDate)->toDateTimeString(),
                'deadline_at'      =>  Carbon::createFromFormat('Y-m-d H:i:s', $request->endDate)->toDateTimeString()
            ]);
    
            Task::whereIn('id', collect($request->tasks)->pluck('id'))->update([
                'sprint_id' => $sprint->id
            ]);
        }
        catch(\Exception $e)
        {
            abort(500, $e->getMessage());
        }


        return response()->json(['message' => 'Sprint Successfully Added']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
