import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from '@fullcalendar/interaction';
import axios from 'axios';

var calendarEl = document.getElementById("calendar");

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
                    .then((response) => {
                        const shiftId = response.data.shift_id;
                        // イベントの追加
                        calendar.addEvent({
                            id: shiftId,
                            title: shiftType,
                            start: info.start,
                            end: info.end,
                            allDay: true,
                        }, true // make the event "stick"
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
        console.log(info.event._instance.range.start.valueOf())
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