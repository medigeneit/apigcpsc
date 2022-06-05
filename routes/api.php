<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChamberController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FeedbackQuestionController;
use App\Http\Controllers\MentorAssignController;
use App\Http\Controllers\RoleAssignController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserController;
use App\Models\Schedule;
use App\Models\User;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register'])
    ->middleware('throttle:20,1,register');

Route::resource('/schedules', ScheduleController::class);
Route::get('/search-schedule/{support_type}', [ScheduleController::class, 'search_schedule']);
Route::get('/mentor-schedule', [ScheduleController::class, 'mentor_schedule']);
// Route::get('/search-schedule-demo/{support_type}',[ScheduleController::class,'search_schedule']);

Route::get('/previous-history', [ScheduleController::class, 'prev_history']);

Route::resource('/appointments', AppointmentController::class);

Route::get('/my-appointments', [AppointmentController::class, 'my_appointments']);

Route::post('/mentor-assign', [AppointmentController::class, 'mentor_assign']);

Route::put('/mentor-assign/{id}', [AppointmentController::class, 'mentor_assign_edit']);

Route::resource('/feedback-questions', FeedbackQuestionController::class);
Route::resource('/feedback', FeedbackController::class);

Route::resource('chambers', ChamberController::class);
Route::resource('roles', RoleController::class);
Route::resource('mentor-assigns', MentorAssignController::class);

Route::get('time', [UserController::class, 'time']);
Route::get('mentors', [UserController::class, 'mentor_index']);
