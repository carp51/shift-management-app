<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AdminController extends Controller
{
    public function showSignupForm()
   {
       return view('admin_signup_form');
   }

   public function signup(Request $request)
   {
       $admin = User::query()->create([
           'name'=>$request['name'],
           'email'=>$request['email'],
           'password'=>Hash::make($request['password']),
           # 新規登録できるのはadminのみなので
           'role'=>'admin',
           'store'=>'メガプライス',
       ]);

       Auth::login($admin);

       return redirect()->route('admin.home');
   }

   public function index()
   {
       return view('admin_home');
   }
}
