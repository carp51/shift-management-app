<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.2/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.8/FileSaver.min.js"></script>
    <title>シフト確定画面</title>
    @vite(['resources/css/app.css', 'resources/js/work_hope_calender.js', 'resources/js/work_confirm_calender.js'])
</head>
<body>
    @include("components.header")
    
    <h3>従業員の希望シフト</h3>
    <div id='work_hope_calendar'></div>

    <h3>従業員に公開するシフト</h3>
    <div id='work_confirm_calendar'></div>
    <a class="btn btn-primary d-block mx-auto" id="shiftTempCreate" style="width: 200px; color:white; margin-bottom:30px; margin-top:30px;">従業員の希望シフトをコピーする</a>
    <a class="btn btn-primary d-block mx-auto" id="shiftShow" style="width: 200px; color:white; margin-bottom:30px; margin-top:30px;">シフトを公開する</a>

    <a class="btn btn-primary d-block mx-auto" id="shiftToExcel" style="width: 200px; color:white; margin-bottom:30px; margin-top:30px;">シフトをEXCELで出力する</a>

    <div class="modal" tabindex="-1" id="exampleModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">シフトの種類を選択してください</h5>
                </div>
                <div class="d-flex justify-content-around">
                    <div>
                        <button type="button" class="early-shift-btn" data-bs-dismiss="modal">早番</button>
                        <button type="button" class="late-shift-btn" data-bs-dismiss="modal">遅番</button>
                        <button type="button" class="fulltime-shift-btn" data-bs-dismiss="modal">通し</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>