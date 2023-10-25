<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AdminController extends Controller
{
    public function showSignupForm()
   {
       return view('admin.signup');
   }

   public function signup(Request $request)
   {
       $admin = User::query()->create([
           'name'=>$request['name'],
           'email'=>NULL,
           'password'=>Hash::make($request['password']),
           # 新規登録できるのはadminのみなので
           'role'=>'admin',
           'store_id'=>0,
       ]);

       $store = Store::query()->create([
            'store_id' => $admin->id,
            'name'=>$request['store'],
        ]);

       // storeカラムの値を新しいレコードのIDと同じに設定
        $admin->update([
            'store_id' => $admin->id,
        ]);

       Auth::login($admin);

       return redirect()->route('common.home');
   }

   public function logout()
   {
       Auth::logout();

       return redirect() ->route('user.login');
   }

   public function index()
   {
       return view('common.home');
   }
}
