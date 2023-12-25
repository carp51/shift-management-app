<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class UsersManagemantController extends Controller
{
    public function index()
    {
        $per_page = 5; // １ページごとの表示件数

        $loggedInUser = Auth::user(); // ログインしているユーザーを取得
        $store_id = $loggedInUser->store_id; // ログインしているユーザーのstore_id

        $users = User::where('store_id', $store_id)->paginate($per_page);
        // $users = DB::table('users')->paginate($per_page);

        return view('admin.users_managemant')->with('users', $users);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'username' => ['required', 'string', 'max:32', 'unique:users,username', 'regex:/^[a-zA-Z0-9-_]+$/'],
        ]);

        //バリデーションエラー時のリダイレクト先（登録画面）
        if ($validator->fails()) {
            return response()->json($validator->messages());
        }


        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->username = $request->username;
        $user->role = 'user';

        $loggedInUser = Auth::user(); // ログインしているユーザーを取得
        $adminStore = $loggedInUser->store_id; // ログインしているユーザーのstore_id
        
        $user->store_id = $adminStore;
        $result = $user->save();
        return ['result' => $result];
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['confirmed'],
            'username' => ['required', 'string', 'max:32', 'unique:users,username', 'regex:/^[a-zA-Z0-9-_]+$/'],
        ]);

        $loggedInUser = Auth::user(); // ログインしているユーザーを取得

        //バリデーションエラー時のリダイレクト先（登録画面）
        if ($validator->fails()) {
            if (!$loggedInUser->username == $request->username) {
                return response()->json($validator->messages());
            }
        }
        
        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        if($request->filled('password')) { // パスワード入力があるときだけ変更
            $user->password = Hash::make($request->password);
        }
        $result = $user->save();
        return ['result' => $result];
    }

    public function destroy(User $user)
    {
        $result = $user->delete();
        return ['result' => $result];
    }
}
