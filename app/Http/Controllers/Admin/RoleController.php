<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use Inertia\Inertia;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:role list', ['only' => ['index', 'show']]);
        $this->middleware('can:role create', ['only' => ['create', 'store']]);
        $this->middleware('can:role edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:role delete', ['only' => ['destroy']]);
    }

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
                        ->orWhere('name', 'LIKE', "%{$value}%")
                        ->orWhere('guard_name', 'LIKE', "%{$value}%");
                });
            });
        });
        
        $roles = QueryBuilder::for(Role::class)
        ->defaultSort('name')
        ->allowedSorts(['name', 'guard_name'])
        ->allowedFilters(['name', 'guard_name', $globalSearch])
        ->paginate()
        ->withQueryString();

        return Inertia::render('Admin/Role/Index', [
            'roles' => $roles,
            'can' => [
                'create' => Auth::user()->can('role create'),
                'edit' => Auth::user()->can('role edit'),
                'delete' => Auth::user()->can('role delete'),
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
        // $roles = (new Role)->newQuery();

        // if (request()->has('search')) {
        //     $roles->where('name', 'Like', '%'.request()->input('search').'%');
        // }

        // if (request()->query('sort')) {
        //     $attribute = request()->query('sort');
        //     $sort_order = 'ASC';
        //     if (strncmp($attribute, '-', 1) === 0) {
        //         $sort_order = 'DESC';
        //         $attribute = substr($attribute, 1);
        //     }
        //     $roles->orderBy($attribute, $sort_order);
        // } else {
        //     $roles->latest();
        // }

        // $roles = $roles->paginate(5)->onEachSide(2)->appends(request()->query());

        // return Inertia::render('Admin/Role/Index', [
        //     'roles' => $roles,
        //     'filters' => request()->all('search'),
        //     'can' => [
        //         'create' => Auth::user()->can('role create'),
        //         'edit' => Auth::user()->can('role edit'),
        //         'delete' => Auth::user()->can('role delete'),
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
        $permissions = Permission::all()->pluck("name","id");

        return Inertia::render('Admin/Role/Create', [
            'permissions' => $permissions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreRoleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRoleRequest $request)
    {
        try{
            Role::create(['name' => $request->roleName]);
        }
        catch(\Exception $e)
        {
            abort(500, 'Could not add Role');
        }
        
        return response()->json([
            'message'   =>  'Role successfully added'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $permissions = Permission::all()->pluck("name","id");
        $roleHasPermissions = array_column(json_decode($role->permissions, true), 'id');

        return Inertia::render('Admin/Role/Show', [
            'role' => $role,
            'permissions' => $permissions,
            'roleHasPermissions' => $roleHasPermissions,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all()->pluck("name","id");
        $roleHasPermissions = array_column(json_decode($role->permissions, true), 'id');

        return Inertia::render('Admin/Role/Edit', [
            'role' => $role,
            'permissions' => $permissions,
            'roleHasPermissions' => $roleHasPermissions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\UpdateRoleRequest  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update($request->all());
        $permissions = $request->permissions ?? [];
        $role->syncPermissions($permissions);

        return redirect()->route('role.index')
                        ->with('message', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json([
            'message'  => 'Role Successfully Deleted'
        ]);
    }

    public function details(Request $request)
    {
        return response()->json(['role' => Role::find($request->id)]);
    }

    public function updateRole(Request $request)
    {
        try{
            $role = Role::find($request->roleId);
            $role->name = $request->roleName;
            $role->save();
        }

        catch(\Exception $e){
            abort(500, 'could not edit role');
        }

        return response()->json([
            'message'   =>  'role successfully editted'
        ]);
    }
}
