<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Task;
use Inertia\Inertia;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Validator;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class ProjectController extends Controller
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
                        ->orWhere('name', 'LIKE', "%{$value}%");
                });
            });
        });
        
        $projects = QueryBuilder::for(Project::class)
        ->defaultSort('title')
        ->allowedSorts(['title'])
        ->allowedFilters(['title', $globalSearch])
        ->paginate()
        ->withQueryString();

        // dd($permissions);
        return Inertia::render('Admin/Project/Index', [
            'projects' => $projects,
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
            )
            ->withGlobalSearch()
            ->defaultSort('name');
    });
    }

    public function store(Request $request)
    {
        $request->validate([
            'task.*.title'    =>  'required'
        ],
        [
            'task.*.title.required' =>  'Tasks are required!'
        ]);
        
        try{
            $project = Project::create([
                'title'         => $request->title,
                'description'   => $request->description,
                'deadline_at'   => Carbon::createFromFormat('Y-m-d H:i:s', $request->date)->toDateTimeString(),
                'status'        => $request->status
            ]);

            collect($request->task)->each(function($task) use($project){
                Task::create([
                    'title'  => $task['title'],
                    'project_id'    => $project->id
                ]);
            });
        }
        catch(\Exception $e)
        {
            abort(500, 'Project creation failed '  . $e->getMessage());
        }

        return response()->json([
            'message'   =>  'Successfully created',
            'projectId' => $project->id
        ]);
    }

    public function show(Project $project)
    {
        return response()->json([
            'tasks'    => $project->tasks()->select('title', 'id')->get()->toArray(),
            'projectName'   => $project->title,
            'projectDescription'    =>  $project->description,
            'date'  => $project->deadline_at,
            'status'    => $project->status,
            'projectTasks'  =>  $project->tasks()->with('sprint')->get(),
        ]);
    }


    public function update(Request $request, Project $project)
    {
        $request->validate([
            'task.*.title'    =>  'required'
        ],
        [
            'task.*.title.required' =>  'Tasks are required!'
        ]);
        
        try{
            $project->update([
                'title'         => $request->projectName,
                'description'   => $request->projectDescription,
                'deadline_at'   => Carbon::createFromFormat('Y-m-d H:i:s', $request->date)->toDateTimeString(),
                'status'        => $request->status
            ]);

            
            collect($request->tasks)->each(function($task) use($project){
                // $project->tasks()->updateOrCreate(
                //     ['id' => $task['id'] ?? null],
                //     ['title'  => $task['title']]
                // );
                $project->tasks()->delete();
                $project->tasks()->create([
                    'title' => $task['title']
                ]);
            });
                        

        }
        catch(\Exception $e)
        {
            abort(500, 'Project creation failed '  . $e->getMessage());
        }

        return response()->json([
            'message'   =>  'Successfully created'
            ]);
        }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return response()->json([
            'message'   => 'Project and all the associated tasks deleted successfully!'
        ]);
        
    }
}
