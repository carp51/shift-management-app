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

var currentVal = 0;

// JavaScriptでプラスボタンのクリックイベントを設定
document.getElementById('plus-btn').addEventListener('click', function () {
    // 現在の値を取得し、1を加えて再設定
    currentVal = parseInt(document.getElementById('counter-input').value, 10) + 1;
    document.getElementById('counter-input').value = currentVal;
    console.log(currentVal, "p");
});

// JavaScriptでマイナスボタンのクリックイベントを設定
document.getElementById('minus-btn').addEventListener('click', function () {
    // 現在の値を取得し、1を減らして再設定
    if (currentVal - 1 >= 0) {
        currentVal = parseInt(document.getElementById('counter-input').value, 10) - 1;
        document.getElementById('counter-input').value = currentVal;
    }
    console.log(currentVal, "m");
});

var title;
var start_date;
var end_date;

// JavaScriptで決定ボタンのクリックイベントを設定
document.getElementById('counter-confirm').addEventListener('click', function (info) {
    console.log(info);
    axios
        .post("shift-planning/shift-planning-edit", {
            start_date: start_date,
            end_date: end_date,
            title: title,
            need_number: currentVal
        })
        .then((response) => {
            console.log(response);
            document.location.reload();
        })
        .catch(() => {
            // バリデーションエラーなど
            alert("変更に失敗しました");
        });
});

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
    
    eventClick: function (info) {
        start_date = info.event._instance.range.start.valueOf();
        end_date = info.event._instance.range.end.valueOf();
        title = info.event._def.title;

        var str = info.event._def.title;
        currentVal = parseInt(str.substr(str.indexOf('_') + 1), 10);
        document.getElementById('counter-input').value = currentVal;

        $('#counter-modal').modal('show').on('click', function () {
            $('.btn btn-secondary').off('click');
        });
    },
        
});
calendar.render();