<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class WorkController extends Controller
{
    public function index()
   {
       return view('common.work');
   }

   public function allShiftGet(Request $request)
    {
        // バリデーション
        $request->validate([
            'start_date' => 'required|integer',
            'end_date' => 'required|integer'
        ]);

        // カレンダー表示期間
        $start_date = date('Y-m-1', $request->input('start_date') / 1000 + 24 * 60 * 60 * 8);
        $end_date = date('Y-m-1', $request->input('end_date') / 1000 + 24 * 60 * 60 * 8);

        return Shift::query()
            ->select(
                // FullCalendarの形式に合わせる
                'start_date as start',
                'end_date as end',
                'shift_type as title',
                'user_id as resourceId'
            )
            // FullCalendarの表示範囲のみ表示
            ->where('start_date', '>=', $start_date)
            ->where('end_date', '<=', $end_date)
            ->get();
    }

    public function allMemberGet(Request $request)
    {
        $loggedInUser = Auth::user(); // ログインしているユーザーを取得

        $storeId = $loggedInUser -> store_id;

        return User::query()
            ->select(
                // FullCalendarの形式に合わせる
                'id as id',
                'name as title'
            )
            // FullCalendarの表示範囲のみ表示
            ->where('store_id', '=', $storeId)
            ->get();
    }
}
