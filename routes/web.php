<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;

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
    Route::post('/login',[UserController::class,'login']);

    Route::middleware('auth')->group(function (){
        Route::get('/home',[AdminController::class,'index']) -> name('common.home');
    });
});

Route::prefix('admin')->group(function() {
    Route::get('/signup',[AdminController::class,'showSignupForm']) -> name('admin.signup');
    Route::post('/signup',[AdminController::class,'signup']);

    Route::middleware('auth')->group(function (){
        // Route::get('/home',[AdminController::class,'index']) -> name('common.home');
        Route::resource('users', UserController::class);
        Route::post('logout',[AdminController::class,'logout'])->name('admin.logout');
    });
});

