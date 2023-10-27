<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    public function shiftAdd(Request $request)
    {
        // バリデーション
        $request->validate([
            'start_date' => 'required|integer',
            'end_date' => 'required|integer',
            'shift_type' => 'required|max:32',
        ]);

        // 登録処理
        $shift = new Shift;
        // 日付に変換。JavaScriptのタイムスタンプはミリ秒なので秒に変換
        $shift->start_date = date('Y-m-d', $request->input('start_date') / 1000);
        $shift->end_date = date('Y-m-d', $request->input('end_date') / 1000 - 24 * 60 * 60);
        $shift->shift_type = $request->input('shift_type');

        $loggedInUser = Auth::user(); // ログインしているユーザーを取得
        $shift->user_id = $loggedInUser->id;
        $shift->store_id = $loggedInUser->store_id;

        $shift->save();

        return;
    }

    public function shiftGet(Request $request)
    {
        // バリデーション
        $request->validate([
            'start_date' => 'required|integer',
            'end_date' => 'required|integer'
        ]);

        // カレンダー表示期間
        $start_date = date('Y-m-d', $request->input('start_date') / 1000);
        $end_date = date('Y-m-d', $request->input('end_date') / 1000);

        $loggedInUser = Auth::user(); // ログインしているユーザーを取得

        // 登録処理
        return Shift::query()
            ->select(
                // FullCalendarの形式に合わせる
                'start_date as start',
                'end_date as end',
                'shift_type as title'
            )
            // FullCalendarの表示範囲のみ表示
            ->where('end_date', '>', $start_date)
            ->where('start_date', '<', $end_date)
            ->where('user_id', '=', $loggedInUser -> id)
            ->get();
    }
}
