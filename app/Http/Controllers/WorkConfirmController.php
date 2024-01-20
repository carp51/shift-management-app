<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\Work;
use App\Models\User;
use App\Models\Store;
use App\Models\WorkConfirm;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;


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

    public function excelFileGet(Request $request)
    {
        $display_start_date = date('Y-m-d', $request->input('display_start_date') / 1000);
        $display_end_date = date('Y-m-d', $request->input('display_end_date') / 1000);
        $loggedInUser = Auth::user();

        $shift_template = IOFactory::load(resource_path('excel/shift_template.xlsx'));
        $output = $this->excelFileEdit($shift_template, $display_start_date, $display_end_date);
        
        $writer = IOFactory::createWriter($output, 'Xlsx');
        $temp_file = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($temp_file);

        $loggedInUser_store_id = $loggedInUser -> id;
        $display_start_year = date('Y', strtotime($display_start_date));
        $display_start_month = date('m', strtotime($display_start_date));
        $store_name = Store::where('store_id', '=', $loggedInUser_store_id) -> first()-> name;
        $file_name = $store_name . "勤務表（" . $display_start_year . "年" . $display_start_month ."月）.xlsx";

        return response()->download($temp_file, $file_name)->deleteFileAfterSend(true);
    }

    private function excelFileEdit($excel_file, $display_start_date, $display_end_date)
    {
        $days_difference = $this->getDaysDifference($display_start_date, $display_end_date);

        $week_index = date('w', strtotime($display_start_date));

        $excel_file = $this->daysWrite($excel_file, "E", 3,  $days_difference, $week_index);
        $excel_file = $this->daysWrite($excel_file, "E", 16, $days_difference, $week_index);
        
        $excel_file = $this->monthWrite($excel_file, strtotime($display_start_date));
        $excel_file = $this->userShiftWrite($excel_file, $display_start_date, $display_end_date);

        return $excel_file;
    }

    private function daysWrite($excel_file, $start_column, $start_row,$days_difference, $week_index)
    {
        $start_column = $start_column;
        $start_row = $start_row;

        $week = [
            '日', //0
            '月', //1
            '火', //2
            '水', //3
            '木', //4
            '金', //5
            '土', //6
          ];

        $sheet = $excel_file->getActiveSheet();

        for ($i=0; $i < $days_difference; $i++) { 
            $target_day_cell =  $start_column . (string) $start_row;
            $target_week_cell = $start_column . (string) ($start_row + 1);
            
            $sheet->setCellValue($target_day_cell, $i + 1);
            $sheet->setCellValue($target_week_cell, $week[$week_index]);

            if ($week_index == 6) {
                $excel_file = $this->saturdayColorWrite($excel_file, $start_column);
            } elseif ($week_index == 0) {
                $excel_file = $this->sundayColorWrite($excel_file, $start_column);
            }

            $start_column = ++$start_column;
            $week_index = ($week_index + 1) % 7;
        }

        return $excel_file;
    }

    private function monthWrite($excel_file, $display_start_date)
    {
        $sheet = $excel_file->getActiveSheet();

        # 和暦と月を書き込む
        $sheet->setCellValue("T2", intval(date('m', $display_start_date)));
        $sheet->setCellValue("T15", intval(date('m', $display_start_date)));
        $sheet->setCellValue("Q2", intval(date('Y', $display_start_date)) - 2018);
        $sheet->setCellValue("Q15", intval(date('Y', $display_start_date)) - 2018);

        $loggedInUser = Auth::user();
        $loggedInUser_store_id = $loggedInUser -> id;
        $store_name = Store::where('store_id', '=', $loggedInUser_store_id) -> first()-> name;
        $title = $store_name . "勤務表";
        $sheet->setCellValue("Q15", intval(date('Y', $display_start_date)) - 2018);

        $sheet->setCellValue("A1", $title);
        $sheet->setCellValue("A14", $title);

        return $excel_file;
    }
    
    private function saturdayColorWrite($excel_file, $now_column)
    {
        $sheet = $excel_file->getActiveSheet();

        for ($i=3; $i < 22; $i++) { 
            if (8 < $i && $i < 16) {
                continue;
            }
            $target_cell = $now_column . (string) $i;
            $sheet->getStyle($target_cell)
            ->getFill()
            ->setFillType('solid')
            ->getStartColor()
            ->setARGB('8DB4E2');
        }
        return $excel_file;
    }

    private function sundayColorWrite($excel_file, $now_column)
    {
        $sheet = $excel_file->getActiveSheet();

        for ($i=3; $i < 22; $i++) { 
            if (8 < $i && $i < 16) {
                continue;
            }
            $target_cell = $now_column . (string) $i;
            $sheet->getStyle($target_cell)
            ->getFill()
            ->setFillType('solid')
            ->getStartColor()
            ->setARGB('FF7C80');
        }
        return $excel_file;
    }

    private function userShiftWrite($excel_file, $start_date, $end_date)
    {
        $sheet = $excel_file->getActiveSheet();

        $start_column = "E";
        $start_row = 5;
        
        $loggedInUser = Auth::user();
        $all_user = User::query()
                    ->where('store_id', '=', $loggedInUser -> store_id)
                    ->get();

        $position_mapping = array();
        
        for ($i=0; $i < $all_user -> count(); $i++) { 
            if ($i < 4) {
                $target_cell = "A" . (string) ($start_row + $i);
                $position_mapping[$all_user[$i]['id']] = $i;
                $sheet->setCellValue($target_cell, $all_user[$i]['name']);
            } else {
                $target_cell = "A" . (string) ($start_row + $i + 9);
                $position_mapping[$all_user[$i]['id']] = $i + 9;
                $sheet->setCellValue($target_cell, $all_user[$i]['name']);
            }
        }

        $all_shift = Work::query()
                    ->select(
                        'start_date',
                        'shift_type',
                        'user_id'
                    )
                    ->where('start_date', '>=', $start_date)
                    ->where('end_date', '<', $end_date)
                    ->where('store_id', '=', $loggedInUser -> store_id)
                    ->get();
        
        $sheet->setCellValue("A50", $all_shift->count());

        for ($i=0; $i < $all_shift->count(); $i++) { 
            $start_date_day = date('d', strtotime($all_shift[$i]['start_date']));
            $user_id = $all_shift[$i]['user_id'];
            $shift_type = $all_shift[$i]['shift_type'];
            
            if ($shift_type == "早番") {
                $shift_type = "早";
            } elseif ($shift_type == "遅番") {
                $shift_type = "遅";
            } elseif ($shift_type == "通し") {
                $shift_type = "通";
            }

            $target_cell =  $this->getAlfabet($start_column, intval($start_date_day)) . (string) ($start_row + $position_mapping[$user_id]);
            $sheet->setCellValue($target_cell, $shift_type);
        }

        return $excel_file;
    }

    private function getAlfabet($alfabet, $plus_num)
    {
        for ($i=0; $i < $plus_num - 1; $i++) { 
            $alfabet = ++$alfabet;
        }
        return $alfabet;
    }

    private function getDaysDifference($display_start_date, $display_end_date)
    {
        $start_date = new \DateTime($display_start_date);
        $end_date = new \DateTime($display_end_date);

        $days_difference = $start_date->diff($end_date);
        $days_difference = $days_difference->days;

        return $days_difference;
    }
}
