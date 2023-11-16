import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from '@fullcalendar/interaction';
import axios from 'axios';

var calendarEl = document.getElementById("calendar");

// カレンダーで選択された曜日とシフトを取得
var displayStartDay;
var displayEndDay;

document.getElementById('bulkSelectDecision').addEventListener('click', function(){
    var day_of_week_checks = document.getElementsByClassName('day-of-week-checks');
    var day_of_week_checked_list = [];

    for (let i = 0; i < 7; i++) {
        if ( day_of_week_checks[i].checked === true ) {
            day_of_week_checked_list.push(day_of_week_checks[i].value);
        }
    }

    var shift_checks = document.getElementsByName('shift-checks');
    var shift_checked = '';

    for (let i = 0; i < 3; i++) {
        if ( shift_checks[i].checked === true ) {
            shift_checked = shift_checks[i].value;
        }
    }

    // 選択された曜日とシフトがある場合
    if (day_of_week_checked_list.length != 0 && shift_checked != '') {
        axios
            .post("/user/home/shift-bulk-add", {
                display_start_day: displayStartDay,
                display_end_day: displayEndDay,
                shift_checked: shift_checked,
                day_of_week_checked_list: day_of_week_checked_list,
            })
            .then((response) => {
                // サーバーからの応答を処理してカレンダーに追加
                for (let i = 0; i < response.data.length; i++) {
                    var shiftType = response.data[i][0];
                    var startInfo = response.data[i][1];
                    var endInfo = response.data[i][2];
                    calendar.addEvent({
                        title: shiftType,
                        start: startInfo,
                        end: endInfo,
                        allDay: true,
                    },
                    );
                }
            })
            .catch(() => {
                // エラー時の処理
                alert("登録に失敗しました");
            });
    }
});

document.getElementById('bulkDeleteDecision').addEventListener('click', function(){
    axios
        .post("/user/home/shift-bulk-delete", {
            display_start_day: displayStartDay,
            display_end_day: displayEndDay,
        })
        .then(() => {
            // 追加したイベントを削除
            calendar.removeAllEvents();
        })
        .catch(() => {
            // エラー時の処理
            alert("登録に失敗しました");
        });
});

let calendar = new Calendar(calendarEl, {
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: "dayGridMonth",
    headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "",
    },
    locale: "ja",

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
                // Laravelの登録処理の呼び出し
                axios
                    .post("/user/home/shift-add", {
                        start_date: info.start.valueOf(),
                        end_date: info.end.valueOf(),
                        shift_type: shiftType,
                    })
                    .then(() => {
                        // イベントの追加
                        calendar.addEvent({
                            title: shiftType,
                            start: info.start,
                            end: info.end,
                            allDay: true,
                        }, true
                        );
                    })
                    .catch(() => {
                        // バリデーションエラーなど
                        alert("登録に失敗しました");
                    });
            }
            $('#exampleModal').hide();
            $('.early-shift-btn, .late-shift-btn, .fulltime-shift-btn').off('click');
        });
    },

    events: function (info, successCallback, failureCallback) {
        // Laravelのイベント取得処理の呼び出し

        //現在、表示されているカレンダーの最初と終わりの日付を取得する
        displayStartDay = info.start.valueOf();
        displayEndDay = info.end.valueOf();

        axios
            .post("home/shift-get", {
                start_date: info.start.valueOf(),
                end_date: info.end.valueOf(),
            })
            .then((response) => {
                // 追加したイベントを削除
                calendar.removeAllEvents();
                // カレンダーに読み込み
                successCallback(response.data);
            })
            .catch(() => {
                // バリデーションエラーなど
                alert("登録に失敗しました");
            });
    },

    eventClick: function (info) {
        if (confirm('削除しますか？')) {
            axios
                .post("home/shift-delete", {
                    start_date: info.event._instance.range.start.valueOf(),
                    end_date: info.event._instance.range.end.valueOf(),
                })
                .then(() => {
                })
                .catch(() => {
                    // バリデーションエラーなど
                    alert("削除に失敗しました");
                });
            info.event.remove()
        }
    },
});
calendar.render();