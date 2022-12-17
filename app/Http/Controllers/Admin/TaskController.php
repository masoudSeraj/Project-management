<?php

namespace App\Http\Controllers\Admin;

use App\Models\Task;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use PDO;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

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

    public function search(Request $request)
    {
        dd( Task::where('title', 'LIKE', "%$request->search%")->pluck('title', 'value')->get()->toArray());
    }
}
