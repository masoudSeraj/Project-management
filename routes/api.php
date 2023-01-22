<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\SprintController;
use App\Http\Controllers\Api\V1\PermissionController;
use App\Http\Controllers\Api\V1\ProjectSprintTasksController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();

//     Route::apiResource('');
// });

Route::group(
    [
    'prefix' => 'v1',
    'middleware' => ['api'],
    'namespace' =>  'App\Http\Controllers\Api\V1'
    ],
    function(){
        Route::apiResource('user', UserController::class);
        // Route::apiResource('projectSprintTasks', ProjectSprintTasksController::class);
        Route::get('project/index/{project}', [ProjectSprintTasksController::class, 'index'])->name('project.index');
        Route::post('project/store/{project}', [ProjectSprintTasksController::class, 'store'])->name('project.store');

        Route::apiResource('sprint', SprintController::class);
        Route::get('sprint/index/{sprint}', [SprintController::class, 'index'])->name('sprint.index');
        Route::apiResource('role', RoleController::class);
        Route::apiResource('permission', PermissionController::class);
    });
