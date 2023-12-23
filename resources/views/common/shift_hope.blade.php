<!doctype html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <title>HOME</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include("components.header")

    <!-- <div class="row mt-4">
        <div class="col-md-4 ml-auto">
            <p class="mb-3" id="userShiftConfirmText">希望シフト:  未確定</p>
            <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#userShiftConfirmModal"
                id="userShiftConfirmButton">
            </button>
        </div>
        <div class="col-md-3 ml-auto">
            <p>a</p>
            <button type="button" class="btn btn-primary mb-3" data-toggle="modal"
                data-target="#bulkSelectModal">日付で選択</button>
            <button type="button" class="btn btn-danger mb-3" data-toggle="modal"
                data-target="#bulkDeleteModal">一括削除</button>
        </div>
    </div> -->

    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-5 col-sm-5 mt-1">
            <p class="" id="userShiftConfirmText"></p>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#userShiftConfirmModal"
                id="userShiftConfirmButton">
            </button>
        </div>
        <div class="col-1 col-sm-1"></div>
        <div class="col-6 col-sm-4 mt-4 p-0">
            <div class="p-0 ml-5">
                <button type="button" class="btn btn-primary ml-4" data-toggle="modal"
                    data-target="#bulkSelectModal">日付で選択</button>
            </div>
            <div class="p-0 ml-5">
                <!-- <button type="button" class="btn btn-danger mt-1 ml-4" data-toggle="modal"
                    data-target="#bulkSelectModal">日付で削除</button> -->
                <button type="button" class="btn btn-danger mt-1 ml-4" data-toggle="modal"
                    data-target="#bulkDeleteModal">一括削除</button>
            </div>

        </div>
    </div>

    <!-- シフト一括登録のときに出るモーダル -->
    <div class="modal fade" id="bulkSelectModal" tabindex="-1" role="dialog" aria-labelledby="basicModal"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>一括選択画面</h4>
                </div>
                <div class="modal-body">
                    <p>一括で選択したい曜日を押す</p>
                    <label><input type="checkbox" value="Sun" class="day-of-week-checks"><span>日</span></label>
                    <label><input type="checkbox" value="Mon" class="day-of-week-checks"><span>月</span></label>
                    <label><input type="checkbox" value="Tue" class="day-of-week-checks"><span>火</span></label>
                    <label><input type="checkbox" value="Wed" class="day-of-week-checks"><span>水</span></label>
                    <label><input type="checkbox" value="Thu" class="day-of-week-checks"><span>木</span></label>
                    <label><input type="checkbox" value="Fri" class="day-of-week-checks"><span>金</span></label>
                    <label><input type="checkbox" value="Sat" class="day-of-week-checks"><span>土</span></label>
                </div>
                <div class="modal-body">
                    <p>希望シフトを選択</p>
                    <label><input type="radio" value="early" name="shift-checks"><span>早番</span></label>
                    <label><input type="radio" value="late" name="shift-checks"><span>遅番</span></label>
                    <label><input type="radio" value="fulltime" name="shift-checks"><span>通し</span></label>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
                    <button type="button" class="btn btn-success" id="bulkSelectDecision"
                        data-dismiss="modal">決定</button>
                </div>
            </div>
        </div>
    </div>

    <!-- シフト一括削除のときに出るモーダル -->
    <div class="modal fade" id="bulkDeleteModal" tabindex="-1" role="dialog" aria-labelledby="basicModal"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>一括でシフトを削除しますか</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">いいえ</button>
                    <button type="button" class="btn btn-danger" id="bulkDeleteDecision"
                        data-dismiss="modal">はい</button>
                </div>
            </div>
        </div>
    </div>

    <!-- シフト確定のときに出るモーダル -->
    <div class="modal fade" id="userShiftConfirmModal" tabindex="-1" role="dialog" aria-labelledby="basicModal"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="userShiftConfirmModalMessage">希望シフトを確定しますか？</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">いいえ</button>
                    <button type="button" class="btn btn-primary" id="userShiftConfirmDecision"
                        data-dismiss="modal">はい</button>
                </div>
            </div>
        </div>
    </div>

    <div id='calendar'></div>

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