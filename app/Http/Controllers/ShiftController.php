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

    public function shiftBulkAdd(Request $request)
    {
        // カレンダーに表示されている期間の開始日と終了日を取得
        $display_start_day = date('Y-m', $request->input('display_start_day') / 1000 + 24 * 60 * 60 * 8);
        $display_end_day = date('Y-m', $request->input('display_end_day') / 1000 + 24 * 60 * 60 * 8);

        // カレンダー表示開始日時（ミリ秒単位）を秒に変換したもの
        $now_day_timestamp = $request->input('display_start_day') / 1000;

        // 選択された曜日のリストを取得
        $day_of_week_checked_list = $request->input('day_of_week_checked_list');

        $shift_dict = array(
            'early' => '早番',
            'late' => '遅番',
            'fulltime' => '通し'
        );

        $day_of_week_dict = array(
            0 => 'Sun',
            1 => 'Mon',
            2 => 'Tue',
            3 => 'Wed',
            4 => 'Thu',
            5 => 'Fri',
            6 => 'Sat'
        );

        $shift_info_list = array();

        // 50日間のシフトを登録(カレンダーの最初と最後を全探索するので)
        for ($i=0; $i < 50; $i++) { 
            // 表示月範囲外の日付はスキップ
            if (date('Y-m-d', $now_day_timestamp) < $display_start_day || date('Y-m-d', $now_day_timestamp) >= $display_end_day) {
                $now_day_timestamp = strtotime('+1 day', $now_day_timestamp);
                continue;
            };

            // 選択された曜日以外はスキップ
            if (!in_array($day_of_week_dict[$i % 7], $day_of_week_checked_list)) {
                $now_day_timestamp = strtotime('+1 day', $now_day_timestamp);
                continue;
            };

            // 登録処理
            $shift = new Shift;
            // 日付に変換。JavaScriptのタイムスタンプはミリ秒なので秒に変換
            $shift->start_date = date('Y-m-d', $now_day_timestamp);
            $shift->end_date = date('Y-m-d', $now_day_timestamp);
            $shift->shift_type = $shift_dict[$request->input('shift_checked')];

            $loggedInUser = Auth::user(); // ログインしているユーザーを取得
            $shift->user_id = $loggedInUser->id;
            $shift->store_id = $loggedInUser->store_id;

            // シフト情報を配列に追加
            array_push($shift_info_list, [$shift->shift_type, $shift->start_date, $shift->end_date]);
            // データベースに保存
            $shift->save();

            // 次の日に進む処理
            $now_day_timestamp = strtotime('+1 day', $now_day_timestamp);
        }

        // 登録したシフトの情報を返す
        return $shift_info_list;
    }

    public function shiftGet(Request $request)
    {
        // バリデーション
        $request->validate([
            'start_date' => 'required|integer',
            'end_date' => 'required|integer'
        ]);

        // カレンダー表示期間
        $start_date = date('Y-m-1', $request->input('start_date') / 1000 + 24 * 60 * 60 * 8);
        $end_date = date('Y-m-1', $request->input('end_date') / 1000 + 24 * 60 * 60 * 8);

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
            ->where('start_date', '>=', $start_date)
            ->where('end_date', '<=', $end_date)
            ->where('user_id', '=', $loggedInUser -> id)
            ->get();
    }

    public function shiftDelete(Request $request)
    {
         // バリデーション
         $request->validate([
            'start_date' => 'required|integer',
            'end_date' => 'required|integer',
        ]);

        $start_date = date('Y-m-d', ($request->input('start_date') - 32400000) / 1000);
        $end_date = date('Y-m-d', ($request->input('end_date') - 32400000) / 1000 - 24 * 60 * 60);

        $loggedInUser = Auth::user();

        $task = Shift::where('user_id', $loggedInUser->id)
        ->where('start_date', $start_date)
        ->where('end_date', $end_date)
        ->first();
        
        if ($task) {
            $task->delete();
        }

        return;
    }
}
