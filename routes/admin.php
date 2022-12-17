<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProjectTaskController;

Route::group([
    'namespace' => 'App\Http\Controllers\Admin',
    'prefix' => 'admin',
    'middleware' => ['auth'],
], function () {
    Route::resource('user', 'UserController');
    Route::resource('role', 'RoleController');
    Route::resource('project', 'ProjectController');
    Route::resource('task', 'TaskController');
    Route::resource('projectTask', 'ProjectTaskController');
    Route::post('task/search', [TaskController::class, 'search'])->name('task.search');
    Route::post('role/updateRole', [RoleController::class, 'updateRole'])->name('admin.role.updateRole');
    Route::post('role/details', [RoleController::class, 'details'])->name('admin.role.details');
    Route::resource('permission', 'PermissionController');
    Route::post('permission/details', [PermissionController::class, 'details'])->name('admin.permission.details');
    Route::post('permission/updatePermission', [PermissionController::class, 'updatePermission'])->name('admin.permission.updatePermission');
    Route::get('edit-account-info', 'UserController@accountInfo')->name('admin.account.info');
    Route::post('edit-account-info', 'UserController@accountInfoStore')->name('admin.account.info.store');
    Route::post('change-password', 'UserController@changePasswordStore')->name('admin.account.password.store');
});