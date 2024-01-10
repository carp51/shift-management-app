<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\Planning;
use App\Models\UserShiftConfirm;
use App\Models\WorkConfirm;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    public function index()
    {
        return view('common.shift_hope');
    }

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

        // 同じ日にシフトがすでに存在するか確認
        $existingRecord = Shift::where('start_date', $shift->start_date)
        ->where('end_date', $shift->end_date)
        ->where('user_id', $shift->user_id)
        ->first();

        if ($existingRecord) {
            $existingRecord -> delete();
        }

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

            // 同じ日にシフトがすでに存在するか確認
            $existingRecord = Shift::where('start_date', date('Y-m-d', $now_day_timestamp))
            ->where('end_date', date('Y-m-d', $now_day_timestamp))
            ->where('user_id', $loggedInUser->id)
            ->first();

            if ($existingRecord) {
                $existingRecord -> delete();
            }

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

    public function shiftStatusGet(Request $request)
    {
        // バリデーション
        $request->validate([
            'start_date' => 'required|integer',
            'end_date' => 'required|integer'
        ]);

        // カレンダーに表示されている期間の開始日と終了日を取得
        $display_start_day = date('Y-m', $request->input('start_date') / 1000 + 24 * 60 * 60 * 8);
        $display_end_day = date('Y-m', $request->input('end_date') / 1000 + 24 * 60 * 60 * 8);

        $shift_status_list = array();

        // カレンダー表示開始日時（ミリ秒単位）を秒に変換したもの
        $now_day_timestamp = $request->input('start_date') / 1000;

        // 50日間のシフトを登録(カレンダーの最初と最後を全探索するので)
        for ($i=0; $i < 50; $i++) { 
            // 表示月範囲外の日付はスキップ
            if (date('Y-m-d', $now_day_timestamp) < $display_start_day || date('Y-m-d', $now_day_timestamp) >= $display_end_day) {
                // 次の日に進む処理
                $now_day_timestamp = strtotime('+1 day', $now_day_timestamp);
                continue;
            };

            $loggedInUser = Auth::user(); // ログインしているユーザーを取得

            $early_shift_need_number = Planning::where('store_id', $loggedInUser->store_id)
                                    ->where('start_date', date('Y-m-d', $now_day_timestamp))
                                    ->where('shift_type', 0)
                                    ->first();

            $late_shift_need_number = Planning::where('store_id', $loggedInUser->store_id)
                                    ->where('start_date', date('Y-m-d', $now_day_timestamp))
                                    ->where('shift_type', 1)
                                    ->first();

            $task = Shift::where('store_id', $loggedInUser->store_id)
                    ->where('start_date', date('Y-m-d', $now_day_timestamp))
                    ->get();

            $shift_status = $this->generateShiftStatus($task, $now_day_timestamp, $early_shift_need_number, $late_shift_need_number);

            $shift_status_list[] = $shift_status[0];

            $shift_status_list[] = [
                'start' => date('Y-m-d', $now_day_timestamp),
                'end' => date('Y-m-d', $now_day_timestamp),
                'title' => "_早番 " . $shift_status[1],
                'textColor' => $shift_status[3],
                'color' => "rgba(255, 0, 0, 0)", 
            ];

            $shift_status_list[] = [
                'start' => date('Y-m-d', $now_day_timestamp),
                'end' => date('Y-m-d', $now_day_timestamp),
                'title' => "_遅番 " .  $shift_status[2],
                'textColor' => "$shift_status[4]",
                'color' => "rgba(255, 0, 0, 0)", 
            ];

            // 次の日に進む処理
            $now_day_timestamp = strtotime('+1 day', $now_day_timestamp);
        }

        // 登録したシフトの情報を返す
        return $shift_status_list;
    }

    private function generateShiftStatus($task, $now_day_timestamp, $early_shift_need_number, $late_shift_need_number)
    {
        // デフォルトのステータス
        $status = [
            'start' => date('Y-m-d', $now_day_timestamp),
            'end' => date('Y-m-d', $now_day_timestamp),
            'display' => "background",
            'color' => "#FF0000", 
        ];

        $shift_check = [0, 0];
        $shift_check_color = ["red", "red"];

        $early_shift_need_number = $early_shift_need_number->need_number;
        $late_shift_need_number = $late_shift_need_number->need_number;

        for ($i=0; $i < count($task); $i++) { 
            switch ($task[$i]->shift_type) {
                case '早番':
                    $shift_check[0] += 1;
                    break;
                case '遅番':
                    $shift_check[1] += 1;
                    break;
                case '通し':
                    $shift_check[0] += 1;
                    $shift_check[1] += 1;
                    break;
            }
        }

        if ($shift_check[0] == $early_shift_need_number && $shift_check[1] == $late_shift_need_number) {
            $status['color'] = "#00FF00";
        } elseif ($shift_check[0] < $early_shift_need_number && $shift_check[1] < $late_shift_need_number) {
            $status['color'] = "gray";
        } elseif ($shift_check[0] == $early_shift_need_number && $shift_check[1] < $late_shift_need_number) {
            $status['color'] = "blue";
        } elseif ($shift_check[0] < $early_shift_need_number && $shift_check[1] == $late_shift_need_number) {
            $status['color'] = "blue";
        }

        if ($early_shift_need_number - $shift_check[0] == 0) {
            $shift_check_color[0] = "black";
        }
        if ($late_shift_need_number - $shift_check[1] == 0) {
            $shift_check_color[1] = "black";
        }
        return [$status, $early_shift_need_number - $shift_check[0], $late_shift_need_number - $shift_check[1], $shift_check_color[0], $shift_check_color[1]];
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

    public function shiftBulkDelete(Request $request)
    {
        // カレンダーに表示されている期間の開始日と終了日を取得
        $display_start_day = date('Y-m', $request->input('display_start_day') / 1000 + 24 * 60 * 60 * 8);
        $display_end_day = date('Y-m', $request->input('display_end_day') / 1000 + 24 * 60 * 60 * 8);

        // カレンダー表示開始日時（ミリ秒単位）を秒に変換したもの
        $now_day_timestamp = $request->input('display_start_day') / 1000;

        // 50日間のシフトを登録(カレンダーの最初と最後を全探索するので)
        for ($i=0; $i < 50; $i++) { 
            // 表示月範囲外の日付はスキップ
            if (date('Y-m-d', $now_day_timestamp) < $display_start_day || date('Y-m-d', $now_day_timestamp) >= $display_end_day) {
                $now_day_timestamp = strtotime('+1 day', $now_day_timestamp);
                continue;
            };

            // 削除処理
            $loggedInUser = Auth::user();

            $task = Shift::where('user_id', $loggedInUser->id)
                        ->where('start_date', date('Y-m-d', $now_day_timestamp))
                        ->first();
            
            if ($task) {
                $task->delete();
            }

            // 次の日に進む処理
            $now_day_timestamp = strtotime('+1 day', $now_day_timestamp);
        }
        return;
    }

    public function userShiftConfirm(Request $request)
    {
        $display_start_month = date('Y-m-1', $request->input('display_start_day') / 1000 + 24 * 60 * 60 * 8);
        $loggedInUser = Auth::user();

        // 同じ月の確定情報がすでに存在するか確認
        $existingRecord = UserShiftConfirm::where('user_id', $loggedInUser->id)
        ->where('month', $display_start_month)
        ->first();

        $confirm_status = 1;

        if ($existingRecord) {
            switch ($existingRecord->confirm_status) {
                case 1:
                    $existingRecord->confirm_status = 0;
                    $confirm_status = 0;
                    break;
                
                case 0:
                    $existingRecord->confirm_status = 1;
                    $confirm_status = 1;
                    break;
            }
            $existingRecord->save();
            return $confirm_status;
        }

        $task = new UserShiftConfirm;
        $task -> user_id = $loggedInUser -> id;
        $task -> month = $display_start_month;
        $task -> confirm_status = 1;

        $task->save();
        return $confirm_status;
    }

    public function userShiftConfirmStatusGet(Request $request)
    {
        $display_start_month = date('Y-m-1', $request->input('display_start_day') / 1000 + 24 * 60 * 60 * 8);
        $loggedInUser = Auth::user();

        // 同じ月の確定情報がすでに存在するか確認
        $existingRecord = UserShiftConfirm::where('user_id', $loggedInUser->id)
        ->where('month', $display_start_month)
        ->first();

        // 同じ月の公開情報がすでに存在するか確認
        $existingShowRecord = WorkConfirm::where('store_id', $loggedInUser->store_id)
        ->where('month', $display_start_month)
        ->first();

        $confirm_status = [0, 0];

        if ($existingRecord) {
            switch ($existingRecord->confirm_status) {
                case 1:
                    $confirm_status[0] = 1;
                    break;
                
                case 0:
                    $confirm_status[0] = 0;
                    break;
            }
        }

        if ($existingShowRecord) {
            switch ($existingShowRecord->confirm_status) {
                case 1:
                    $confirm_status[1] = 1;
                    break;
                
                case 0:
                    $confirm_status[1] = 0;
                    break;
            }
        }

        return $confirm_status;
    }
}
