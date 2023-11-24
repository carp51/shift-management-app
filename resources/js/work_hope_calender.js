import { Calendar } from "@fullcalendar/core";
import interactionPlugin from '@fullcalendar/interaction';
import resourceTimelinePlugin from "@fullcalendar/resource-timeline";
import axios from 'axios';

// https://fullcalendar.io/docs/timeline-view

var calendarEl = document.getElementById("work_hope_calendar");

// 現在の日付を取得
var currentDate = new Date();

// 現在の月に1ヶ月を足して次の月に設定
var nextMonthDate = new Date(currentDate);
nextMonthDate.setMonth(currentDate.getMonth() + 1);

document.getElementById('shiftCreate').addEventListener('click', function(){
    axios
        .post("/user/work/shift-create", {
        })
        .then((response) => {
            // 追加したイベントを削除
            // calendar.removeAllEvents();
            console.log(response);
        })
        .catch(() => {
            // エラー時の処理
            alert("登録に失敗しました");
        });
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

    events: function (info, successCallback, failureCallback) {
        // Laravelのイベント取得処理の呼び出し
        axios
            .post("work/all-member-get", {
            })
            .then((response) => {
                // // カレンダーに読み込み
                var event_data = response.data;

                var resource_data = event_data;

                // リソースをセット
                calendar.setOption('resources', resource_data);
            })
            .catch((response) => {
                //バリデーションエラーなど
                alert("登録に失敗しました");
            });
        axios
            .post("work/all-shift-get", {
                start_date: info.start.valueOf(),
                end_date: info.end.valueOf(),
            })
            .then((response) => {
                // // カレンダーに読み込み
                var event_data = response.data;
                successCallback(event_data);
            })
            .catch((response) => {
                //バリデーションエラーなど
                alert("登録に失敗しました");
            });
    },
});
calendar.render();