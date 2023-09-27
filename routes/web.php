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
    return view('login');
});

Route::get('/welcome', function () {
    return view('welcome');
});

Route::prefix('admin')->group(function() {
    Route::get('/signup',[AdminController::class,'showSignupForm']);
    Route::post('/signup',[AdminController::class,'signup']);
    Route::middleware('auth')->group(function (){
        Route::get('/home',[AdminController::class,'index']) -> name('admin.home');
        Route::resource('users', UserController::class);
    });
});