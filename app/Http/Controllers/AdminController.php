<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AdminController extends Controller
{
    public function showRegister()
   {
       return view('register');
   }

   public function register(Request $request)
   {
       $user = User::query()->create([
           'name'=>$request['name'],
           'email'=>$request['email'],
           'password'=>Hash::make($request['password']),
           # 新規登録できるのはadminのみなので
           'role'=>'admin',
           'store'=>'メガプライス',
       ]);

       Auth::login($user);

       return redirect()->route('profile');
   }

   public function profile()
   {
       return view('profile');
   }
}
