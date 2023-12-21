<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\Work;
use App\Models\WorkConfirm;
use Illuminate\Support\Facades\Auth;

class WorkConfirmController extends Controller
{
    public function allShiftGet(Request $request)
    {
        // バリデーション
        $request->validate([
            'start_date' => 'required|integer',
            'end_date' => 'required|integer'
        ]);

        // カレンダー表示期間
        $start_date = date('Y-m-1', $request->input('start_date') / 1000);
        $end_date = date('Y-m-1', $request->input('end_date') / 1000);

        return Work::query()
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

    public function shiftAdd(Request $request)
    {
        // バリデーション
        $request->validate([
            'start_date' => 'required|integer',
            'end_date' => 'required|integer',
            'shift_type' => 'required|max:32',
            'user_id' => 'required|integer',
        ]);

        // 登録処理
        $work = new Work;

        $work->start_date = date('Y-m-d', $request->input('start_date') / 1000);
        $work->end_date = date('Y-m-d', $request->input('end_date') / 1000 - 24 * 60 * 60);
        $work->shift_type = $request->input('shift_type');
        $work->user_id = $request->input('user_id');

        $loggedInUser = Auth::user(); // ログインしているユーザーを取得
        $work->store_id = $loggedInUser->store_id;

        $work->save();

        return;
    }

    public function shiftDelete(Request $request)
    { 
        $start_date = date('Y-m-d', ($request->input('start_date') - 32400000) / 1000);
        $end_date = date('Y-m-d', ($request->input('end_date') - 32400000) / 1000 - 24 * 60 * 60);

        $user_id = $request->input('user_id');

        $task = Work::where('user_id', $user_id)
        ->where('start_date', $start_date)
        ->where('end_date', $end_date)
        ->first();
        
        if ($task) {
            $task->delete();
        }

        return;
    }

    public function shiftShow(Request $request)
    { 
        $display_start_month = date('Y-m-d', $request->input('display_start_date') / 1000);
        $loggedInUser = Auth::user();

        // // JavaScriptから送信された日付（1月1日）
        // $jsStartDate = $request->input('display_start_day');

        // // JavaScriptの月のインデックスは0から始まるため、1を加える
        // $phpStartDate = date('Y-m-d', strtotime($jsStartDate . '+1 month'));

        // 同じ月の確定情報がすでに存在するか確認
        $existingRecord = WorkConfirm::where('store_id', $loggedInUser->store_id)
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

        $task = new WorkConfirm;
        $task -> store_id = $loggedInUser -> store_id;
        $task -> month = $display_start_month;
        $task -> confirm_status = 1;

        $task->save();
        return $confirm_status;
    }

    public function shiftShowStatusGet(Request $request)
    {
        $display_start_month = date('Y-m-1', $request->input('start_date') / 1000 + 24 * 60 * 60 * 8);
        $loggedInUser = Auth::user();

        // 同じ月の確定情報がすでに存在するか確認
        $existingRecord = WorkConfirm::where('store_id', $loggedInUser->store_id)
        ->where('month', $display_start_month)
        ->first();

        $confirm_status = 0;

        if ($existingRecord) {
            switch ($existingRecord->confirm_status) {
                case 1:
                    $confirm_status = 1;
                    break;
                
                case 0:
                    $confirm_status = 0;
                    break;
            }
        }

        return $confirm_status;
    }

    public function userShiftShow(Request $request)
    {
        $display_start_month = date('Y-m-1', $request->input('start_day') / 1000 + 24 * 60 * 60 * 8);
        $start_date = date('Y-m-1', $request->input('start_day') / 1000 + 24 * 60 * 60 * 8);
        $end_date = date('Y-m-1', $request->input('end_day') / 1000 + 24 * 60 * 60 * 8);
        $loggedInUser = Auth::user();

        $work_confirm_status = WorkConfirm::where('store_id', $loggedInUser->store_id)
                            ->where('month', $display_start_month)
                            ->first();

        if ($work_confirm_status === null) {
            return;
        }

        if ($work_confirm_status['confirm_status'] == 0) {
            return;
        }

        $task = Work::query()
            ->select(
                // FullCalendarの形式に合わせる
                'start_date as start',
                'end_date as end',
                'shift_type as title',
            )
            // FullCalendarの表示範囲のみ表示
            ->where('start_date', '>=', $start_date)
            ->where('end_date', '<=', $end_date)
            ->where('user_id', '=', $loggedInUser -> id)
            ->get();

        $user_salary_sum = 0;
        $minimum_hourly_wage = 960;

        for ($i=0; $i < $task->count(); $i++) { 
            $shift_type = $task[$i]['title'];
            if ($shift_type == '早番' || $shift_type == '遅番') {
                $user_salary_sum += $minimum_hourly_wage * 5;
            } else {
                $user_salary_sum += $minimum_hourly_wage * 9;
            }
        }

        return [$task, $user_salary_sum];
    }
}
