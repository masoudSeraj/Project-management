<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Sprint;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TaskService\TaskService;
use App\Http\Resources\SprintTasksResource;
use App\Http\Resources\SprintTasksCollection;
use App\Http\Resources\ProjectSprintTasksCollection;

class SprintController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Sprint $sprint, TaskService $taskService)
    {
        return new SprintTasksResource($sprint);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
