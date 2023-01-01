<?php

namespace App\Http\Controllers\Admin;

use PDO;
use App\Models\Task;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\TaskHasDependencyException;
use App\Services\TaskDependency\TaskDependencyFinder;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use App\Services\TaskDependcy\TaskDependencyFinderInterface;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                Collection::wrap($value)->each(function ($value) use ($query) {
                    $query
                        ->whereHas('project', function($query) use($value){
                            return $query->where('title', 'LIKE', "%{$value}%");
                        })
                        ->orWhere('title', 'LIKE', "%{$value}%");
                });
            });
        });
        
        $tasks = QueryBuilder::for(Task::class)
        ->defaultSort('title')
        ->allowedSorts(['title', 'project'])
        ->allowedIncludes(['project'])
        ->with('project')
        ->allowedFilters(['title', $globalSearch])
        ->paginate()
        ->withQueryString();

        // dd($permissions);
        return Inertia::render('Admin/Task/Index', [
            'tasks' => $tasks,
            // 'can' => [
            //     'create' => Auth::user()->can('permission create'),
            //     'edit' => Auth::user()->can('permission edit'),
            //     'delete' => Auth::user()->can('permission delete'),
            // ]
        ])->table(function (InertiaTable $table) {

            $table
            ->column(
                key: 'title',
                label: 'title',
                canBeHidden: true,
                hidden: false,
                sortable: true,
            )
            ->column(
                key: 'project',
                label: 'project',
                canBeHidden: true,
                hidden: false,
                sortable: true,
            )
            ->withGlobalSearch()
            ->defaultSort('title');
    });
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task, Request $request)
    {
        // dd($task);
        // return response()->json([
        //     'taskDescription'   => $task->description,
        //     'taskName'          =>  $task->title,
        //     'date'              =>  $task->date,
        //     'status'            =>  $task->status
        // ]);

        return new TaskResource($task);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
    public function update(Request $request, Task $task, TaskDependencyFinder $finder)
    {
        // dd(collect($request->value)->pluck('id'));
        // dd($request->all());
        try{
            $request->validate([
                'value.*'    => 'nullable',
                'started_at'    => 'nullable',
                'paused_at'     => ['nullable', Rule::requiredIf(isset($request->paused_at))],
                'started_at'    => ['nullable', Rule::requiredIf(isset($request->started_at))],
                'deadline'       =>  'required'
            ]);
    
            $task->title = $request->taskName;
            $task->description = $request->taskDescription;
            $request->whenFilled($request->started_at, function() use($task, $request){
                $task->started_at = $request->started_at;
            })->whenFilled($request->started_at, function() use($task, $request){
                $task->paused_at = $request->paused_at;
            });
    
            $task->status = $request->status;
            $task->deadline_at = $request->deadline;

            $request->whenFilled('started_at', function() use($finder, $task){
                if($finder->taskHasDependency($task)){                    
                    throw new TaskHasDependencyException('The task has dependencies', 500);
                };
            });

            $request->whenFilled('value', function() use($task, $request){
                $task->dependencies()->sync(collect($request->value)->pluck('id')->toArray());
            });

            $request->whenFilled('users', function() use($task, $request){
                $task->users()->sync(collect($request->users)->pluck('id')->toArray());
            });

            $task->save();
        }
        catch(TaskHasDependencyException $e){
            abort(500, $e->getMessage());
        }
        catch(\Exception $e)
        {
            abort(500, 'Something went wrong!' . $e->getMessage());
        }

        return response()->json(['message' => 'Task successfully editted']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['message' => 'Task successfully Deleted']);
    }

    public function start(Request $request, Task $task)
    {
        $task->started_at = now();
        $task->paused_at = null;
        $task->save();

        return response()->json([
            'date'  => $task->started_at,
            'taskStarted'   => true
        ]);

    }

    public function stop(Request $request, Task $task)
    {
        // dd($task);
        $task->paused_at = now();
        $task->started_at = null;
        $task->save();

        return response()->json([
            'date'  => $task->paused_at,
            'taskStarted'   => false
        ]);
    }
}
