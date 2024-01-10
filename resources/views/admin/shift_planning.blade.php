<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <title>シフト確定画面</title>
    @vite(['resources/css/app.css', 'resources/js/shift_planning_calendar.js'])
</head>
<body>
    @include("components.header")

    <h3>日付ごとの勤務人数</h3>

    <div class="row">
            <div class="p-0 ml-5">
                <button type="button" class="btn btn-primary ml-4" data-toggle="modal"
                    data-target="#bulkSelectModal">曜日で選択</button>
            </div>
    </div>

    <div id='shift_planning_calendar'></div>

    <div class="modal" tabindex="-1" id="counter-modal">
        <div class="modal-dialog mt-5">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">シフトの人数を変更できます</h5>
                </div>
                <div class="modal-body text-center">
                    <!-- モーダルの中身 -->
                    <div class="row justify-content-center">
                        <div class="col-2">
                            <span class="modal-btn">
                                <span id="minus-btn" class="btn btn-white btn-minuse" type="button">-</span>
                            </span>
                        </div>
                        <div class="col-4">
                            <input id="counter-input" type="text" class="form-control text-center height-25" value="0">
                        </div>
                        <div class="col-2">
                            <span class="modal-btn">
                                <span id="plus-btn" class="btn btn-red btn-plus" type="button">+</span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="counter-confirm" type="button" class="btn btn-primary" data-bs-dismiss="modal">決定</button>
                    <button  ype="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                    <!-- モーダルの他のボタンやアクションを追加 -->
                </div>
            </div>
        </div>
    </div>
    
    <!-- 曜日で一括変更のときに出るモーダル -->
    <div class="modal fade" id="bulkSelectModal" tabindex="-1" role="dialog" aria-labelledby="basicModal"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>一括選択画面</h4>
                </div>
                <div class="modal-body text-center mx-auto">
                    <p>一括で選択したい曜日を選択</p>
                    <label><input type="checkbox" value="Sun" class="day-of-week-checks"><span>日</span></label>
                    <label><input type="checkbox" value="Mon" class="day-of-week-checks"><span>月</span></label>
                    <label><input type="checkbox" value="Tue" class="day-of-week-checks"><span>火</span></label>
                    <label><input type="checkbox" value="Wed" class="day-of-week-checks"><span>水</span></label>
                    <label><input type="checkbox" value="Thu" class="day-of-week-checks"><span>木</span></label>
                    <label><input type="checkbox" value="Fri" class="day-of-week-checks"><span>金</span></label>
                    <label><input type="checkbox" value="Sat" class="day-of-week-checks"><span>土</span></label>
                </div>
                <div class="modal-body text-center mx-auto">
                    <p>変更したいシフトを選択</p>
                    <label><input type="radio" value="early" name="shift-checks"><span>早番</span></label>
                    <label><input type="radio" value="late" name="shift-checks"><span>遅番</span></label>
                </div>
                <div class="row justify-content-center">
                    <p class="col-12 text-center">希望のシフト人数を選択</p>
                    <div class="col-2">
                        <span class="modal-btn">
                            <span id="bulk-modal-minus-btn" class="btn btn-white btn-minuse" type="button">-</span>
                        </span>
                    </div>
                    <div class="col-4">
                        <input id="bulk-modal-counter-input" type="text" class="form-control text-center height-25" value="0">
                    </div>
                    <div class="col-2">
                        <span class="modal-btn">
                            <span id="bulk-modal-plus-btn" class="btn btn-red btn-plus" type="button">+</span>
                        </span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
                    <button type="button" class="btn btn-success" id="bulkSelectDecision"
                        data-dismiss="modal">決定</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>