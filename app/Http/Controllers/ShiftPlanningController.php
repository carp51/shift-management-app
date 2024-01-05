<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Planning;
use Illuminate\Support\Facades\Auth;

class ShiftPlanningController extends Controller
{
    public function index()
    {
        return view('admin.shift_planning');
    }

    public function shiftPlanningAdd(Request $request)
    {
        // バリデーション
        $request->validate([
            'start_date' => 'required|integer',
            'end_date' => 'required|integer'
        ]);

        // カレンダーに表示されている期間の開始日と終了日を取得
        $display_start_day = date('Y-m', $request->input('start_date') / 1000 + 24 * 60 * 60 * 8);
        $display_end_day = date('Y-m', $request->input('end_date') / 1000 + 24 * 60 * 60 * 8);

        $shift_planning_list = array();
        // $shift_type_dict = {""}

        // カレンダー表示開始日時（ミリ秒単位）を秒に変換したもの
        $now_day_timestamp = $request->input('start_date') / 1000;

        $loggedInUser = Auth::user(); // ログインしているユーザーを取得

        // 50日間のシフトを登録(カレンダーの最初と最後を全探索するので)
        for ($i=0; $i < 50; $i++) { 
            // 表示月範囲外の日付はスキップ
            if (date('Y-m-d', $now_day_timestamp) < $display_start_day || date('Y-m-d', $now_day_timestamp) >= $display_end_day) {
                // 次の日に進む処理
                $now_day_timestamp = strtotime('+1 day', $now_day_timestamp);
                continue;
            };

            // 重複チェック
            $existingPlanning = Planning::where('start_date', date('Y-m-d', $now_day_timestamp))
                ->where('store_id', $loggedInUser->store_id)
                ->first();

            if ($existingPlanning) {
                break;
            }

            for ($j=0; $j < 2; $j++) { 
                // 登録処理
                $planning = new planning;
                // 日付に変換。JavaScriptのタイムスタンプはミリ秒なので秒に変換
                $planning->start_date = date('Y-m-d', $now_day_timestamp);
                $planning->end_date = date('Y-m-d', $now_day_timestamp);

                $planning->store_id = $loggedInUser->store_id;
                $planning->shift_type = $j;
                $planning->need_number = 1;
                
                $planning->save();

                // シフト情報を配列に追加
                array_push($shift_planning_list, [$planning->shift_type, $planning->need_number, $planning->start_date, $planning->end_date]);
            }

            // 次の日に進む処理
            $now_day_timestamp = strtotime('+1 day', $now_day_timestamp);
        }

        // 登録したシフトの情報を返す
        return $shift_planning_list;
    }

    public function shiftPlanningGet(Request $request)
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
        return Planning::query()
            ->select(
                // FullCalendarの形式に合わせる
                'start_date as start',
                'end_date as end',
                'shift_type as title',
                'need_number'
            )
            // FullCalendarの表示範囲のみ表示
            ->where('start_date', '>=', $start_date)
            ->where('end_date', '<', $end_date)
            ->where('store_id', '=', $loggedInUser -> store_id)
            ->get()
            ->map(function ($planning) {
                // shift_typeが0の場合、titleを修正
                if ($planning->title == 0) {
                    $planning->title = "早番_" . $planning->need_number;
                }

                if ($planning->title == 1) {
                    $planning->title = "遅番_" . $planning->need_number;
                }
                return $planning;
            });
    }

    public function shiftPlanningEdit(Request $request)
    {
        $loggedInUser = Auth::user(); // ログインしているユーザーを取得

        // カレンダー表示期間
        $start_date = date('Y-m-d', $request->input('start_date') / 1000);
        $end_date = date('Y-m-d', $request->input('end_date') / 1000 - 24 * 60 * 60);

        $title = $request->input('title');

        if (mb_strstr($title, '_', true) == '早番') {
            $shift_type = 0;
        } else {
            $shift_type = 1;
        }

        $task = Planning::where('start_date', $start_date)
                        ->where('end_date', $end_date)
                        ->where('shift_type', $shift_type)
                        ->where('store_id', $loggedInUser -> store_id)
                        ->first();

        $task -> need_number = $request->input('need_number');

        $task -> save();

        return $task;
    }

    public function shiftPlanningBulkEdit(Request $request)
    {
        // カレンダーに表示されている期間の開始日と終了日を取得
        $display_start_date = date('Y-m', $request->input('display_start_date') / 1000 + 24 * 60 * 60 * 8);
        $display_end_date = date('Y-m', $request->input('display_end_date') / 1000 + 24 * 60 * 60 * 8);

        // カレンダー表示開始日時（ミリ秒単位）を秒に変換したもの
        $now_date_timestamp = $request->input('display_start_date') / 1000;

        // 選択された曜日のリストを取得
        $day_of_week_checked_list = $request->input('day_of_week_checked_list');

        $shift_dict = array(
            'early' => 0,
            'late' => 1,
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
            if (date('Y-m-d', $now_date_timestamp) < $display_start_date || date('Y-m-d', $now_date_timestamp) >= $display_end_date) {
                $now_date_timestamp = strtotime('+1 day', $now_date_timestamp);
                continue;
            };

            // 選択された曜日以外はスキップ
            if (!in_array($day_of_week_dict[$i % 7], $day_of_week_checked_list)) {
                $now_date_timestamp = strtotime('+1 day', $now_date_timestamp);
                continue;
            };

            $loggedInUser = Auth::user();

            $task = Planning::where('start_date', date('Y-m-d', $now_date_timestamp))
                            ->where('end_date', date('Y-m-d', $now_date_timestamp))
                            ->where('shift_type', $shift_dict[$request->input('shift_checked')])
                            ->where('store_id', $loggedInUser->store_id)
                            ->first();

            if ($task) {
                $task -> need_number = $request->input('need_number');
                $task->save();
            }
            // シフト情報を配列に追加
            array_push($shift_info_list, [$task->need_number, $task->start_date, $task->end_date]);

            // 次の日に進む処理
            $now_date_timestamp = strtotime('+1 day', $now_date_timestamp);
        }

        // 登録したシフトの情報を返す
        return $shift_info_list;
    }
}
