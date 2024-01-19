<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsersManagemantController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\ShiftPlanningController;
use App\Http\Controllers\WorkConfirmController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/user/login');
});

Route::get('/welcome', function () {
    return view('welcome');
});

Route::prefix('user')->group(function() {
    Route::get('/login',[UserController::class,'showLogin']) -> name('user.login');
    Route::post('/login',[UserController::class,'login']) ->name('user.login');

    Route::middleware('auth')->group(function (){
        Route::get('/home',[AdminController::class,'index']) -> name('common.home');
        Route::post('/home/user-shift-show',[WorkConfirmController::class,'userShiftShow']) -> name('user-shift-show');

        Route::get('/home/shift-hope',[ShiftController::class,'index']) -> name('common.shift_hope');
        Route::post('/home/shift-hope/shift-add', [ShiftController::class, 'shiftAdd'])->name('shift-add');
        Route::post('/home/shift-hope/shift-bulk-add', [ShiftController::class, 'shiftBulkAdd'])->name('shift-bulk-add');
        Route::post('/home/shift-hope/shift-get', [ShiftController::class, 'shiftGet'])->name('shift-get');
        Route::post('/home/shift-hope/shift-status-get', [ShiftController::class, 'shiftStatusGet'])->name('shift-status-get');
        Route::post('/home/shift-hope/shift-delete', [ShiftController::class, 'shiftDelete'])->name('shift-delete');
        Route::post('/home/shift-hope/shift-bulk-delete', [ShiftController::class, 'shiftBulkDelete'])->name('shift-bulk-delete');
        Route::post('/home/shift-hope/user-shift-confirm', [ShiftController::class, 'userShiftConfirm'])->name('user-shift-confirm');
        Route::post('/home/shift-hope/user-shift-confirm-status-get', [ShiftController::class, 'userShiftConfirmStatusGet'])->name('user-shift-confirm-status-get');

        Route::get('/work',[WorkController::class,'index']) -> name('common.work');
        Route::post('/work/all-shift-get',[WorkController::class,'allShiftGet']) -> name('all-shift-get');
        Route::post('/work/all-member-get',[WorkController::class,'allMemberGet']) -> name('all-member-get');
        Route::post('/work/shift-temp-create',[WorkController::class,'shiftTempCreate']) -> name('shift-temp-create');

        Route::post('/work/confirm/all-shift-get',[WorkConfirmController::class,'allShiftGet']) -> name('all-shift-get');
        Route::post('/work/confirm/shift-add',[WorkConfirmController::class,'shiftAdd']) -> name('shift-add');
        Route::post('/work/confirm/shift-delete',[WorkConfirmController::class,'shiftDelete']) -> name('shift-delete');
        Route::post('/work/confirm/shift-show',[WorkConfirmController::class,'shiftShow']) -> name('shift-show');
        Route::post('/work/confirm/shift-show-status-get',[WorkConfirmController::class,'shiftShowStatusGet']) -> name('shift-show-status-get');

        Route::post('/work/confirm/excel-file-get',[WorkConfirmController::class,'excelFileGet']);
    });
});

Route::prefix('admin')->group(function() {
    Route::get('/signup',[AdminController::class,'showSignupForm']) -> name('admin.signup');
    Route::post('/signup',[AdminController::class,'signup']);

    Route::middleware('auth')->group(function (){
        // Route::get('/home',[AdminController::class,'index']) -> name('common.home');
        Route::resource('users', UsersManagemantController::class);
        Route::post('logout',[AdminController::class,'logout'])->name('admin.logout');
        Route::get('/shift-planning',[ShiftPlanningController::class,'index']) -> name('admin.shift_planning');
        Route::post('/shift-planning/shift-planning-add',[ShiftPlanningController::class,'shiftPlanningAdd']) -> name('shift-planning-add');
        Route::post('/shift-planning/shift-planning-get',[ShiftPlanningController::class,'shiftPlanningGet']) -> name('shift-planning-get');
        Route::post('/shift-planning/shift-planning-edit',[ShiftPlanningController::class,'shiftPlanningEdit']) -> name('shift-planning-edit');
        Route::post('/shift-planning/shift-planning-bulk-edit',[ShiftPlanningController::class,'shiftPlanningBulkEdit']) -> name('shift-planning-bulk-edit');
    });
});

