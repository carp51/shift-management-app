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

    events: function (info, successCallback) {
        axios
            .post("work/all-member-get", {
                display_start_day: info.start.valueOf(),
                display_status: 'hope'
            })
            .then((response) => {
                // // カレンダーに読み込み
                var event_data = response.data;

                var resource_data = event_data;

                // リソースをセット
                calendar.setOption('resources', resource_data);
            })
            .catch(() => {
                // エラー時の処理
                alert("登録に失敗しました");
            });
        axios
            .post("work/all-shift-get", {
                start_date: info.start.valueOf(),
                end_date: info.end.valueOf(),
            })
            .then((response) => {
                // カレンダーに読み込み
                var event_data = response.data;
                successCallback(event_data);
            })
            .catch(() => {
                // エラー時の処理
                alert("シフト取得に失敗しました");
            });
    },
});
calendar.render();