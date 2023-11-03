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
        //alert("selected " + info.startStr + " to " + info.endStr);

        var select_start_date = info.start.toISOString().slice(0, 10);
        var select_end_date = new Date(info.end.getTime() - 24 * 60 * 60 * 1000).toISOString().slice(0, 10);

        if (select_start_date !== select_end_date) {
            alert("一日だけ選択してください")
            return
        }

        // // 入力ダイアログ
        // const shiftType = prompt("イベントを入力してください");

        var shiftType = null;

        $('#exampleModal').modal('show').on('click', function() {
            $('.early-shift-btn, .late-shift-btn, .fulltime-shift-btn').off('click');
        });

        $('.early-shift-btn, .late-shift-btn, .fulltime-shift-btn').on('click', function() {
            shiftType = $(this).text(); // クリックされたボタンのテキストを取得
            console.log('選択されたシフトタイプ:', info);
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
                        });
                        console.log(info.start.valueOf(), info.end.valueOf());
                    })
                    .catch(() => {
                        // バリデーションエラーなど
                        alert("登録に失敗しました");
                    });
            }
            $('#exampleModal').hide();
            $('.early-shift-btn, .late-shift-btn, .fulltime-shift-btn').off('click');
        });

        // $('.btn btn-secondary').on('click', function() {
        //     $('#exampleModal').hide();
        //     $('.early-shift-btn, .late-shift-btn, .fulltime-shift-btn').off('click');
        //     console.log("UNKO")
        // });

        console.log(info);
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
                console.log(response);
                console.log(info.start.valueOf(), info.end.valueOf());
            })
            .catch((response) => {
                // バリデーションエラーなど
                alert("登録に失敗しました");
            });
    },
});
calendar.render();