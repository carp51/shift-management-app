<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class AdminController extends Controller
{
    protected function validator(Request $request)
     {
        return Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'username' => ['required', 'string', 'max:32', 'unique:users,username', 'regex:/^[a-zA-Z0-9-_]+$/'],
        ]);

        //バリデーションエラー時のリダイレクト先（登録画面）
        if ($validator->fails()) {
            return redirect('/admin/signup')
                ->withInput()
                ->withErrors($validator);
            }
     }

    public function showSignupForm()
   {
       return view('admin.signup');
   }

   public function signup(Request $request)
   {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'username' => ['required', 'string', 'max:32', 'unique:users,username', 'regex:/^[a-zA-Z0-9-_]+$/'],
        ]);

        //バリデーションエラー時のリダイレクト先（登録画面）
        if ($validator->fails()) {
            return redirect('/admin/signup')
            ->withErrors($validator)  // エラーメッセージをセッションに保存
            ->withInput();  // 入力データをセッションに保存
        }

       $admin = User::query()->create([
           'name'=>$request['name'],
           'email'=>NULL,
           'password'=>Hash::make($request['password']),
           # 新規登録できるのはadminのみなので
           'role'=>'admin',
           'store_id'=>0,
           'username'=>$request['username']
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
