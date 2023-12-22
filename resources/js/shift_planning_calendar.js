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

const plusButton = document.getElementById('plus-btn');
const modalPlusButton = document.getElementById('bulk-modal-plus-btn');

// JavaScriptでプラスボタンのクリックイベントを設定
plusButton.addEventListener('click', function () {
    // 現在の値を取得し、1を加えて再設定
    currentVal = parseInt(document.getElementById('counter-input').value, 10) + 1;
    document.getElementById('counter-input').value = currentVal;
    console.log(currentVal, "p");
});

// JavaScriptでプラスボタンのクリックイベントを設定
modalPlusButton.addEventListener('click', function () {
    // 現在の値を取得し、1を加えて再設定
    currentVal = parseInt(document.getElementById('bulk-modal-counter-input').value, 10) + 1;
    document.getElementById('bulk-modal-counter-input').value = currentVal;
    console.log(currentVal, "p");
});

const minusButton = document.getElementById('minus-btn');
const modalMinusButton = document.getElementById('bulk-modal-minus-btn');

// JavaScriptでマイナスボタンのクリックイベントを設定
minusButton.addEventListener('click', function () {
    // 現在の値を取得し、1を減らして再設定
    if (currentVal - 1 >= 0) {
        currentVal = parseInt(document.getElementById('counter-input').value, 10) - 1;
        document.getElementById('counter-input').value = currentVal;
    }
    console.log(currentVal, "m");
});

// JavaScriptでマイナスボタンのクリックイベントを設定
modalMinusButton.addEventListener('click', function () {
    // 現在の値を取得し、1を減らして再設定
    if (currentVal - 1 >= 0) {
        currentVal = parseInt(document.getElementById('bulk-modal-counter-input').value, 10) - 1;
        document.getElementById('bulk-modal-counter-input').value = currentVal;
    }
    console.log(currentVal, "m");
});

var title;
var start_date;
var end_date;
var display_start_date;
var display_end_date;

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

// JavaScriptで一括選択モーダルでの決定ボタンのクリックイベントを設定
document.getElementById('bulkSelectDecision').addEventListener('click', function () {
    var day_of_week_checks = document.getElementsByClassName('day-of-week-checks');
    var day_of_week_checked_list = [];

    for (let i = 0; i < 7; i++) {
        if ( day_of_week_checks[i].checked === true ) {
            day_of_week_checked_list.push(day_of_week_checks[i].value);
        }
    }

    var shift_checks = document.getElementsByName('shift-checks');
    var shift_checked = '';

    for (let i = 0; i < 2; i++) {
        if ( shift_checks[i].checked === true ) {
            shift_checked = shift_checks[i].value;
        }
    }

    console.log(day_of_week_checked_list);
    console.log(shift_checked);

    // 選択された曜日とシフトがある場合
    if (!(day_of_week_checked_list.length != 0 && shift_checked != '')) {
        alert("曜日とシフトを選択してください")
        return;
    }
    
    axios
        .post("shift-planning/shift-planning-bulk-edit", {
            display_start_date: display_start_date,
            display_end_date: display_end_date,
            shift_checked: shift_checked,
            day_of_week_checked_list: day_of_week_checked_list,
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
        display_start_date = info.start.valueOf();
        display_end_date = info.end.valueOf();

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

        $('#counter-modal').modal('show').off('click', '.btn-secondary');
    },
        
});
calendar.render();