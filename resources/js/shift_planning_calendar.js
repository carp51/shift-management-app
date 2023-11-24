import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from '@fullcalendar/interaction';
import axios from 'axios';

var calendarEl = document.getElementById("shift_planning_calendar");

// 現在の日付を取得
var currentDate = new Date();

// 現在の月に1ヶ月を足して次の月に設定
var nextMonthDate = new Date(currentDate);
nextMonthDate.setMonth(currentDate.getMonth() + 1);

let calendar = new Calendar(calendarEl, {
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: "dayGridMonth",
    initialDate: nextMonthDate,
    headerToolbar: {
        left: "",
        center: "title",
        right: "",
    },
    locale: "ja",

    events: function (info, successCallback, failureCallback) {
        // Laravelのイベント取得処理の呼び出し
        axios
            .post("shift-planning/shift-planning-add", {
                start_date: info.start.valueOf(),
                end_date: info.end.valueOf(),
            })
            .then((response) => {
            })
            .catch((response) => {
                //バリデーションエラーなど
                alert("登録に失敗しました");
            });
        axios
            .post("shift-planning/shift-planning-get", {
                start_date: info.start.valueOf(),
                end_date: info.end.valueOf(),
            })
            .then((response) => {
                successCallback(response.data);
            })
            .catch((response) => {
                //バリデーションエラーなど
                alert("取得に失敗しました");
            });
        },
    
    // 人数の変更機能は追々やっていく
    // eventClick: function (info) {
    //     console.log(info);
    //     if (confirm('削除しますか？')) {
    //         axios
    //             .post("shift-planning/shift-planning-edit", {
    //                 start_date: info.event._instance.range.start.valueOf(),
    //                 end_date: info.event._instance.range.end.valueOf(),
    //             })
    //             .then(() => {
    //             })
    //             .catch(() => {
    //                 // バリデーションエラーなど
    //                 alert("削除に失敗しました");
    //             });
    //         info.event.remove()
    //     }
    // },
        
});
calendar.render();