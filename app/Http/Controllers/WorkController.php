<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\User;
use App\Models\Work;
use App\Models\UserShiftConfirm;
use Illuminate\Support\Facades\Auth;
use DB;

class WorkController extends Controller
{
    public function index()
   {
       return view('common.work_confirm');
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
        $display_start_month = date('Y-m-1', $request->input('display_start_day') / 1000);
        $display_status = $request->input('display_status');
        $loggedInUser = Auth::user(); // ログインしているユーザーを取得

        $storeId = $loggedInUser -> store_id;

        $users =  User::query()
            ->select(
                // FullCalendarの形式に合わせる
                'id as id',
                'name as title'
            )
            // FullCalendarの表示範囲のみ表示
            ->where('store_id', '=', $storeId)
            ->get();

        if ($display_status == 'confirm') {
            return $users;
        }

        for ($i=0; $i < $users->count(); $i++) { 
            // 各従業員の表示月のシフト確定状況を取得
            $shiftConfirmStatus = UserShiftConfirm::where('user_id', $users[$i]['id'])
            ->where('month', $display_start_month)
            ->first();

            if ($shiftConfirmStatus === NULL) {
                $users[$i]['title'] = $users[$i]['title'] . " ：未確定";
            } else {
                switch ($shiftConfirmStatus->confirm_status) {
                    case 0:
                        $users[$i]['title'] = $users[$i]['title'] . " ：未確定";
                        break;
                    
                    case 1:
                        $users[$i]['title'] = $users[$i]['title'] . " ：確定";
                        break;
                }
            }
        }

        return $users;
    }

    public function shiftTempCreate(Request $request)
    {
        // バリデーション
        $request->validate([
            'start_date' => 'required|integer',
            'end_date' => 'required|integer'
        ]);

        // カレンダー表示期間
        $start_date = date('Y-m-1', $request->input('start_date') / 1000);
        $end_date = date('Y-m-1', $request->input('end_date') / 1000);

        $loggedInUser = Auth::user(); // ログインしているユーザーを取得
        $storeId = $loggedInUser -> store_id;

        $task = Shift::query()
            ->select(
                // FullCalendarの形式に合わせる
                'start_date',
                'end_date',
                'shift_type',
                'user_id',
                'store_id'
            )
            // FullCalendarの表示範囲のみ表示
            ->where('start_date', '>=', $start_date)
            ->where('end_date', '<=', $end_date)
            ->where('store_id', '=', $storeId)
            ->get();

        $delete_task = Work::query()
            ->select(
                // FullCalendarの形式に合わせる
                'start_date',
                'end_date',
                'shift_type',
                'user_id',
                'store_id'
            )
            // FullCalendarの表示範囲のみ表示
            ->where('start_date', '>=', $start_date)
            ->where('end_date', '<=', $end_date)
            ->where('store_id', '=', $storeId)
            ->delete();

        for ($i=0; $i < $task->count(); $i++) { 
            // 登録処理
            $work = new Work;

            $work->start_date = $task[$i]['start_date'];
            $work->end_date = $task[$i]['end_date'];
            $work->shift_type = $task[$i]['shift_type'];

            $work->user_id = $task[$i]['user_id'];
            $work->store_id = $task[$i]['store_id'];

            // 既存のレコードがあるか確認
            $existingWork = Work::where([
                'start_date' => $work->start_date,
                'end_date' => $work->end_date,
                'shift_type' => $work->shift_type,
                'user_id' => $work->user_id,
                'store_id' => $work->store_id,
            ])->first();

            if (!$existingWork) {
                $work->save();
            } 
        }

        return;
    }
}
