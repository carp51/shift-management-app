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
    
    
</body>
</html>