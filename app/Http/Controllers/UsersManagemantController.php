<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        // $user->password = bcrypt($request->password);
        $user->role = 'user';

        $loggedInUser = Auth::user(); // ログインしているユーザーを取得
        $adminStore = $loggedInUser->store_id; // ログインしているユーザーのstore_id
        
        $user->store_id = $adminStore;
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
