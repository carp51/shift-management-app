import { Calendar } from "@fullcalendar/core";
import interactionPlugin from '@fullcalendar/interaction';
import resourceTimelinePlugin from "@fullcalendar/resource-timeline";
import axios from 'axios';

// https://fullcalendar.io/docs/timeline-view

var calendarEl = document.getElementById("work_confirm_calendar");

// 現在の日付を取得
var currentDate = new Date();

// 現在の月に1ヶ月を足して次の月に設定
var nextMonthDate = new Date(currentDate);
nextMonthDate.setMonth(currentDate.getMonth() + 1);

// 表示付きの最初と最後の日を保存
var displayStartDay;
var displayEndDay;

var shiftShowButtonStatus = 0;

// 従業員の希望シフトをコピーするのボタンを押したら
document.getElementById('shiftTempCreate').addEventListener('click', function(info){
    if (confirm('従業員の希望シフトを従業員に公開するシフトに上書きしますか？')) {
    axios
        .post("/user/work/shift-temp-create", {
            start_date: displayStartDay,
            end_date: displayEndDay,
        })
        .then(() => {
            document.location.reload();
        })
        .catch(() => {
            // エラー時の処理
            alert("登録に失敗しました");
        });
    }
});

// シフトを公開するのボタンを押したら
document.getElementById('shiftShow').addEventListener('click', function(info){
    if (shiftShowButtonStatus) {
        var $massage = "シフトの公開を取り消しますか？";
    } else {
        var $massage = "シフトを公開しますか";
    }

    if (confirm($massage)) {
        axios
            .post("/user/work/confirm/shift-show", {
                display_start_date: displayStartDay,
            })
            .then(() => {
                document.location.reload();
            })
            .catch(() => {
                // エラー時の処理
                alert("登録に失敗しました");
            });
        }
});

let calendar = new Calendar(calendarEl, {
    schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
    plugins: [interactionPlugin, resourceTimelinePlugin],
    initialView: "resourceTimelineMonth",
    initialDate: nextMonthDate,
    headerToolbar: {
        left: "",
        center: "title",
        right: "",
    },
    locale: "ja",
    contentHeight: 'auto',

    editable: true,
    
    // ヘッダのタイトルを変更
    resourceAreaHeaderContent: "従業員",

    // 左側のリソースの一覧
    resources: [
    ],

    selectable: true,
    select: function (info) {
        var select_start_date = info.start.toISOString().slice(0, 10);
        var select_end_date = new Date(info.end.getTime() - 24 * 60 * 60 * 1000).toISOString().slice(0, 10);

        // 選択された日付が2日以上だと警告
        if (select_start_date !== select_end_date) {
            alert("一日だけ選択してください")
            return
        }

        var shiftType = null;

        $('#exampleModal').modal('show').on('click', function () {
            $('.early-shift-btn, .late-shift-btn, .fulltime-shift-btn').off('click');
        });

        $('.early-shift-btn, .late-shift-btn, .fulltime-shift-btn').on('click', function () {
            shiftType = $(this).text(); // クリックされたボタンのテキストを取得
            // ここで取得した shiftType を使って必要な処理を行う
            if (shiftType) {
                axios
                    .post("/user/work/confirm/shift-add", {
                        start_date: info.start.valueOf(),
                        end_date: info.end.valueOf(),
                        shift_type: shiftType,
                        user_id: info.resource._resource.id
                    })
                    .then(() => {
                        // イベントの追加
                        // calendar.addEvent({
                        //     title: shiftType,
                        //     start: info.start,
                        //     end: info.end,
                        //     allDay: true,
                        // }, true
                        // );
                        document.location.reload();
                    })
                    .catch(() => {
                        // エラー時の処理
                        alert("登録に失敗しました");
                    });
            }
            $('#exampleModal').hide();
            $('.early-shift-btn, .late-shift-btn, .fulltime-shift-btn').off('click');
        });
    },

    events: function (info, successCallback) {
        // 現在、表示されているカレンダーの最初と終わりの日付を取得する
        displayStartDay = info.start.valueOf();
        displayEndDay = info.end.valueOf();
        // Laravelのイベント取得処理の呼び出し
        axios
            .post("work/all-member-get", {
                display_start_day: info.start.valueOf(),
                display_status: 'confirm'
            })
            .then((response) => {
                // // カレンダーに読み込み
                var event_data = response.data;

                var resource_data = event_data;

                // リソースをセット
                calendar.setOption('resources', resource_data);
            })
            .catch(() => {
                //エラー時の処理
                alert("登録に失敗しました");
            });
        axios
            .post("work/confirm/all-shift-get", {
                start_date: info.start.valueOf(),
                end_date: info.end.valueOf(),
            })
            .then((response) => {
                // // カレンダーに読み込み
                successCallback(response.data);
            })
            .catch(() => {
                //エラー時の処理
                alert("登録に失敗しました");
            });
        axios
            .post("work/confirm/shift-show-status-get", {
                start_date: info.start.valueOf(),
            })
            .then((response) => {
                if (response.data){
                    document.getElementById('shiftShow').innerText = 'シフトの公開を取り消す';
                    shiftShowButtonStatus = 1;
                } else {
                    document.getElementById('shiftShow').innerText = 'シフトを公開する';
                    shiftShowButtonStatus = 0;
                };
            })
            .catch(() => {
                //エラー時の処理
                alert("登録に失敗しました");
            });
    },

    eventClick: function (info) {
        if (confirm('削除しますか？')) {
            axios
                .post("work/confirm/shift-delete", {
                    start_date: info.event._instance.range.start.valueOf(),
                    end_date: info.event._instance.range.end.valueOf(),
                    user_id:info.event._def.resourceIds
                })
                .then(() => {
                })
                .catch(() => {
                    // エラー時の処理
                    alert("削除に失敗しました");
                });
            info.event.remove()
        }
    }
});
calendar.render();