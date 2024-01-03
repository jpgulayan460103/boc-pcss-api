<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:api'], function() {
    Route::resources([
        'users' => UserController::class,
        'offices' => OfficeController::class,
        'schedules' => ScheduleController::class,
        'employees' => EmployeeController::class,
        'holidays' => HolidayController::class,
        'positions' => PositionController::class,
    ]);
});
Route::middleware('auth:api')->post('/schedules/{id}', [ScheduleController::class, 'download']);
Route::middleware('auth:api')->post('/user/change-password', [UserController::class, 'updatePassword']);
Route::middleware('auth:api')->get('/employees/search', [EmployeeController::class, 'updatePassword']);
Route::get('/schedules/{id}/pdf', [ScheduleController::class, 'pdf']);

// Route::resources([
//     'users' => UserController::class,
//     'offices' => OfficeController::class,
//     'schedules' => ScheduleController::class,
//     'employees' => EmployeeController::class,
//     'holidays' => HolidayController::class,
//     'positions' => PositionController::class,
// ]);
