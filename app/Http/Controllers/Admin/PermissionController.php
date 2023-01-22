<?php

namespace App\Http\Controllers\Admin;

use Error;
use Exception;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Http\Requests\Admin\StorePermissionRequest;
use App\Http\Requests\Admin\UpdatePermissionRequest;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:permission list', ['only' => ['index', 'show']]);
        $this->middleware('can:permission create', ['only' => ['create', 'store']]);
        $this->middleware('can:permission edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:permission delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd(User::all());
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                Collection::wrap($value)->each(function ($value) use ($query) {
                    $query
                        ->orWhere('name', 'LIKE', "%{$value}%")
                        ->orWhere('guard_name', 'LIKE', "%{$value}%");
                });
            });
        });
        
        $permissions = QueryBuilder::for(Permission::class)
        ->defaultSort('name')
        ->allowedSorts(['name', 'guard_name'])
        ->allowedFilters(['name', 'guard_name', $globalSearch])
        ->paginate()
        ->withQueryString();

        // dd($permissions);
        return Inertia::render('Admin/Permission/Index', [
            'permissions' => $permissions,
            'can' => [
                'create' => Auth::user()->can('permission create'),
                'edit' => Auth::user()->can('permission edit'),
                'delete' => Auth::user()->can('permission delete'),
            ]
        ])->table(function (InertiaTable $table) {

            $table
            ->column(
                key: 'name',
                label: 'name',
                canBeHidden: true,
                hidden: false,
                sortable: true,
            )->column(
                key: 'guard_name',
                label: 'guard name',
                canBeHidden: true,
                hidden: false,
                sortable: true,
            )
            ->withGlobalSearch()
            ->defaultSort('name');
    });
        // $permissions = (new Permission)->newQuery();

        // if (request()->has('search')) {
        //     $permissions->where('name', 'Like', '%'.request()->input('search').'%');
        // }

        // if (request()->query('sort')) {
        //     $attribute = request()->query('sort');
        //     $sort_order = 'ASC';
        //     if (strncmp($attribute, '-', 1) === 0) {
        //         $sort_order = 'DESC';
        //         $attribute = substr($attribute, 1);
        //     }
        //     $permissions->orderBy($attribute, $sort_order);
        // } else {
        //     $permissions->latest();
        // }

        // $permissions = $permissions->paginate(5)->onEachSide(2)->appends(request()->query());

        // return Inertia::render('Admin/Permission/Index', [
        //     'permissions' => $permissions,
        //     'filters' => request()->all('search'),
        //     'can' => [
        //         'create' => Auth::user()->can('permission create'),
        //         'edit' => Auth::user()->can('permission edit'),
        //         'delete' => Auth::user()->can('permission delete'),
        //     ]
        // ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Admin/Permission/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\StorePermissionRequest  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(StorePermissionRequest $request)
    // {
    //     dd($request);
    //     Permission::create($request->all());

    //     return redirect()->route('permission.index')
    //                     ->with('message', __('Permission created successfully.'));
    // }

    public function store(UpdateRoleRequest $request)
    {
        try{
            Permission::create(['name' => $request->permissionName]);
        }
        catch(\Exception $e){
            abort(500, 'Couldn\'nt add to database');
        }

        return response()->json(['message' => 'Successfully Added to Database']);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission)
    {
        return Inertia::render('Admin/Permission/Show', [
            'permission' => $permission,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        return Inertia::render('Admin/Permission/Edit', [
            'permission' => $permission,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\UpdatePermissionRequest  $request
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    // public function update(UpdatePermissionRequest $request, Permission $permission)
    // {
    //     $permission->update($request->all());

    //     return redirect()->route('permission.index')
    //                     ->with('message', __('Permission updated successfully.'));
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return response()->json([
            'message' => 'Succesfully Deleted'
        ]);
    }

    public function details(Request $request)
    {
        return response()->json(['permission' => Permission::find($request->id)]);
    }

    public function updatePermission(Request $request)
    {
        try{
            $permission = Permission::find($request->permissionId);
            $permission->name = $request->permissionName;
            $permission->save();
        }

        catch(\Exception $e){
            abort(500, 'could not edit permission');
        }

        return response()->json([
            'message'   =>  'permission successfully editted'
        ]);
    }
}
