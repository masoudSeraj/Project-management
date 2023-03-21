<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\PermissionController;

Route::group([
    'namespace' => 'App\Http\Controllers\Admin',
    'prefix' => 'admin',
    'middleware' => ['auth', 'isAdmin'],
], function () {
    Route::resource('user', 'UserController');
    Route::resource('role', 'RoleController');
    Route::resource('project', 'ProjectController');
    Route::resource('task', 'TaskController');
    Route::resource('sprint', 'SprintController');
    Route::get('task/start/{task}', [TaskController::class, 'start'])->name('task.start');
    Route::get('task/stop/{task}', [TaskController::class, 'stop'])->name('task.stop');
    Route::post('task/search', [TaskController::class, 'search'])->name('task.search');
    Route::put('role/updateRole/{role}', [RoleController::class, 'updateRole'])->name('admin.role.updateRole');
    Route::post('role/details', [RoleController::class, 'details'])->name('admin.role.details');
    Route::resource('permission', 'PermissionController');
    Route::post('permission/details', [PermissionController::class, 'details'])->name('admin.permission.details');
    Route::get('edit-account-info', 'UserController@accountInfo')->name('admin.account.info');
    Route::post('edit-account-info', 'UserController@accountInfoStore')->name('admin.account.info.store');
    Route::post('change-password', 'UserController@changePasswordStore')->name('admin.account.password.store');
});
