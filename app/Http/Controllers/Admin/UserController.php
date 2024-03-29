<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\QueryBuilder\QueryBuilder;
use App\Actions\Admin\User\CreateUser;
use App\Actions\Admin\User\UpdateUser;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\UserResource;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class UserController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('can:user list', ['only' => ['index', 'show']]);
    //     $this->middleware('can:user create', ['only' => ['create', 'store']]);
    //     $this->middleware('can:user edit', ['only' => ['edit', 'update']]);
    //     $this->middleware('can:user delete', ['only' => ['destroy']]);
    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $users = $users->paginate(5)->onEachSide(2)->appends(request()->query());
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                Collection::wrap($value)->each(function ($value) use ($query) {
                    $query
                        ->orWhere('name', 'LIKE', "%{$value}%")
                        ->orWhere('email', 'LIKE', "%{$value}%");
                });
            });
        });
        
        $users = QueryBuilder::for(User::class)
        ->defaultSort('name')
        ->allowedSorts(['name', 'email'])
        ->allowedFilters(['name', 'email', $globalSearch])
        ->paginate()
        ->withQueryString();
        
        return Inertia::render('Admin/User/Index', [
            'users' => $users,
            'filters' => request()->all('search'),
            'can' => [
                'create' => Auth::user()->can('user create'),
                'edit' => Auth::user()->can('user edit'),
                'delete' => Auth::user()->can('user delete'),
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
                key: 'email',
                label: 'email',
                canBeHidden: true,
                hidden: false,
                sortable: true,
            )
            ->withGlobalSearch()
            ->defaultSort('name');
    });
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all()->pluck("name","id");

        return Inertia::render('Admin/User/Create', [
            'roles' => $roles,
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'user.name' => ['required', 'string', 'max:255'],
            'user.email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'user.password' => ['required', 'confirmed'],
            'user.password_confirmation' => 'required_with:user.password|min:6'
        ],
        [
            'user.name.required' => 'name is requrired!',
            'user.email.required'   => 'email is required!',
            'user.password.confirmed'   =>  'Passwords missmatch!',
            'user.email.unique'     =>  'Email alredy taken!'
        ]
    );
        try{
            $user = User::create([
                'name' =>   $request->input('user.name'),
                'email' =>  $request->input('user.email'),
                'password'  =>  Hash::make($request->input('user.password'))
            ]);

            $request->whenFilled('user.selectedRoles', function($roles) use($user){
                $user->assignRole($roles);
            });
        }
        catch(\Exception $e)
        {
            abort(500, 'خطایی رد داد');
        }

        return response()->json([
            'message'   => 'کاربر با موفقیت ذخیره شد.',
            'userId'    => $user->id
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $roles = Role::all()->pluck("name","id");
        $userHasRoles = array_column(json_decode($user->roles, true), 'id');

        return Inertia::render('Admin/User/Edit', [
            'user' => $user,
            'roles' => $roles,
            'userHasRoles' => $userHasRoles,
        ]);
    }
    public function update(Request $request, User $user)
    {
        // dd($request);
        try{
            $user->name = $request->name;
            $user->email = $request->email;
            
            $request
            ->whenFilled(['password', 'passwordConfirm'], function() use($user, $request){
                $user->password = bcrypt($request->password);
            })
            ->whenFilled('selectedRoles', function($roles) use($user){
                $user->syncRoles($roles);
            });

            $user->save();
        }
        catch(\Exception $e){
            abort(500, 'مشکلی در ویرایش کاربر پیش آمد.');
        }
        return response()->json([
            'message'   =>  'عملیات ویرایش موفقیت آمیز بود.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {

        // dd($user->all());
        try{
            $user->delete();
        }
        catch(\Exception $e)
        {
            abort(500, 'مشکلی هنگام حذف کاربر پیش آمد');
        }
        
        return response()->json([
            'message'   =>  'کاربر با موفقیت حذف شد.'
        ]);
    }

    /**
     * Show the user a form to change their personal information & password.
     */
    public function accountInfo()
    {
        $user = \Auth::user();

        return Inertia::render('Admin/User/AccountInfo', [
            'user' => $user,
        ]);
    }

    /**
     * Save the modified personal information for a user.
     */
    public function accountInfoStore(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.\Auth::user()->id],
        ]);

        $user = \Auth::user()->update($request->except(['_token']));

        if ($user) {
            $message = 'Account updated successfully.';
        } else {
            $message = 'Error while saving. Please try again.';
        }

        return redirect()->route('admin.account.info')->with('message', __($message));
    }

    /**
     * Save the new password for a user.
     */
    public function changePasswordStore(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'old_password' => ['required'],
            'new_password' => ['required', Rules\Password::defaults()],
            'confirm_password' => ['required', 'same:new_password', Rules\Password::defaults()],
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($validator->failed()) {
                return;
            }
            if (! Hash::check($request->input('old_password'), \Auth::user()->password)) {
                $validator->errors()->add(
                    'old_password', __('Old password is incorrect.')
                );
            }
        });

        $validator->validate();

        $user = \Auth::user()->update([
            'password' => Hash::make($request->input('new_password')),
        ]);

        if ($user) {
            $message = 'Password updated successfully.';
        } else {
            $message = 'Error while saving. Please try again.';
        }

        return redirect()->route('admin.account.info')->with('message', __($message));
    }
}
