import { Calendar } from "@fullcalendar/core";
import interactionPlugin from '@fullcalendar/interaction';
import resourceTimelinePlugin from "@fullcalendar/resource-timeline";
import axios from 'axios';

// https://fullcalendar.io/docs/timeline-view

var calendarEl = document.getElementById("work_calendar");

let calendar = new Calendar(calendarEl, {
    schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
    plugins: [interactionPlugin, resourceTimelinePlugin],
    initialView: "resourceTimelineMonth",
    headerToolbar: {
        left: "prev,next today",
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
        // { id: 1, title: "田中"},
        // { id: 2, title: "鈴木"},
    ],

    // selectable: true,
    // select: function (info) {
    //     //alert("selected " + info.startStr + " to " + info.endStr);

    //     var select_start_date = info.start.toISOString().slice(0, 10);
    //     var select_end_date = new Date(info.end.getTime() - 24 * 60 * 60 * 1000).toISOString().slice(0, 10);

    //     if (select_start_date !== select_end_date) {
    //         alert("一日だけ選択してください")
    //         return
    //     }

    //     // 入力ダイアログ
    //     const shiftType = prompt("イベントを入力してください");

    //     if (shiftType) {
    //         // Laravelの登録処理の呼び出し
    //         axios
    //             .post("/user/home/shift-add", {
    //                 start_date: info.start.valueOf(),
    //                 end_date: info.end.valueOf(),
    //                 shift_type: shiftType,
    //             })
    //             .then(() => {
    //                 // イベントの追加
    //                 calendar.addEvent({
    //                     title: shiftType,
    //                     start: info.start,
    //                     end: info.end,
    //                     allDay: true,
    //                 });
    //                 console.log(info.start.valueOf(), info.end.valueOf());
    //             })
    //             .catch(() => {
    //                 // バリデーションエラーなど
    //                 alert("登録に失敗しました");
    //             });
    //     }
    // },

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
                console.log(event_data);

                // for (let i = 0; i < event_data.length; i++) {
                //     event_data[i]["resourceId"] = loggedInUserId;
                // }
                successCallback(event_data);
            })
            .catch((response) => {
                //バリデーションエラーなど
                alert("登録に失敗しました");
            });
    },
});
calendar.render();