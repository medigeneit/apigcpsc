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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('join', [AuthController::class, 'join']);
//->middleware('throttle:50,1,join');
// confirm otp
Route::post('check-user', [Auth\ResetPasswordController::class, 'checkUser']);
//->middleware('throttle:1,2,check-user');
Route::post('confirm', [AuthController::class, 'confirm']);

Route::post('login', [AuthController::class, 'login']);
Route::post('logout',  [AuthController::class, 'logout']);

Route::post('register', [AuthController::class, 'register'])
    ->middleware('throttle:20,1,register');



//->middleware('throttle:6,1,confirm');



// Route::get('/search-schedule-demo/{support_type}',[ScheduleController::class,'search_schedule']);


#~~~~~~~Authentication Optional~~~~~~~~~~~~~~~
Route::middleware('auth:sanctum.ignore-unauthenticated')->group(function () {
    Route::get('/search-schedule/{support_type}', [ScheduleController::class, 'search_schedule']);
    Route::get('/previous-history', [ScheduleController::class, 'prev_history']);
});



Route::middleware('auth:sanctum')->group(function () {

    #~~~~~~~~~~~~~~~~Admin Dashboard~~~~~~~~~~~~~~~~~~
    {

        Route::resource('chambers', ChamberController::class);

        #~~~Schedules~~~~
        Route::resource('/schedules', ScheduleController::class);

        #~~~Appointments~~~~
        Route::resource('/appointments', AppointmentController::class);
        Route::post('/mentor-assign', [AppointmentController::class, 'mentor_assign']);
        Route::put('/mentor-assign/{id}', [AppointmentController::class, 'mentor_assign_edit']);
        Route::get('find-users', [UserController::class, 'find_user']);


        #~~~Roll management~~~~
        Route::resource('roles', RoleController::class);
        Route::get('assigned-users', [UserController::class, 'assigned_users']);
        Route::get('assigned-user/{user}', [UserController::class, 'user_role']);
        Route::post('role-assign', [UserController::class, 'role_assign']);
        Route::put('role-assign', [UserController::class, 'role_assign_update']);
        Route::get('role-assign-edit/{user}', [UserController::class, 'role_assign_edit']);
        Route::delete('role-assign/{user}', [UserController::class, 'role_assign_delete']);
    }


    #~~~~~~~~~~~~Mentor-User COMMON Dashboard~~~~~~~~~~~~~~~~
    Route::resource('/feedback-questions', FeedbackQuestionController::class);
    Route::resource('/feedback', FeedbackController::class);


    #~~~~~~~~~~~~Mentor Dashboard~~~~~~~~~~~~~~~~
    {
        Route::get('/mentor-schedule', [ScheduleController::class, 'mentor_schedule']);
        Route::get('/mentor-appointments', [AppointmentController::class, 'mentor_appointment']);
    }

    #~~~~~~~~~~~~User Dashboard~~~~~~~~~~~~~~
    {
        Route::get('/my-profile', [AppointmentController::class, 'my_profile']);
    }
});













// Route::resource('mentor-assigns', MentorAssignController::class);

