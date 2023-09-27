<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $per_page = 5; // １ページごとの表示件数

        $loggedInUser = Auth::user(); // ログインしているユーザーを取得
        $store = $loggedInUser->store; // ログインしているユーザーのstore 

        $users = User::where('store', $store)->paginate($per_page);
        // $users = DB::table('users')->paginate($per_page);
        return view('users.index')->with('users', $users);
    }

    public function store(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        // $user->password = bcrypt($request->password);
        $user->role = 'user';

        $loggedInUser = Auth::user(); // ログインしているユーザーを取得
        $adminStore = $loggedInUser->store; // ログインしているユーザーのstore 
        
        $user->store = $adminStore;
        $result = $user->save();
        return ['result' => $result];
    }

    public function update(Request $request, User $user)
    {
        $user->name = $request->name;
        $user->email = $request->email;
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
