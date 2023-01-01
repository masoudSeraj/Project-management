<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Sprint;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class SprintController extends Controller
{
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
        
        $sprints = QueryBuilder::for(Sprint::class)
        ->defaultSort('title')
        ->allowedSorts(['title', 'project'])
        ->allowedIncludes(['project'])
        ->with('project')
        ->allowedFilters(['title', $globalSearch])
        ->paginate()
        ->withQueryString();

        // dd($permissions);
        return Inertia::render('Admin/Sprint/Index', [
            'sprints' => $sprints,
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
}
