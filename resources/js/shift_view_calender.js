import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from '@fullcalendar/interaction';
import axios from 'axios';

var calendarEl = document.getElementById("shift_view_calendar");

let calendar = new Calendar(calendarEl, {
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: "dayGridMonth",
    headerToolbar: {
        left: "prev",
        center: "title",
        right: "next",
    },
    locale: "ja",

    events: function (info, successCallback, failureCallback) {
        axios
            .post("home/user-shift-show", {
                start_day: info.start.valueOf(),
                end_day: info.end.valueOf(),
            })
            .then((response) => {
                console.log(response);
                if (response.data.length > 0) {
                    successCallback(response.data[0]);
                
                    var user_salary_sum = String(response.data[1]);
                    document.getElementById("accordion-salary-text").innerText = `あなたの表示月の月収は${user_salary_sum}円です`;
                } else {
                    document.getElementById("accordion-salary-text").innerText = `あなたの表示月の月収は0円です`;
                }
                
            })
            .catch(() => {
                //バリデーションエラーなど
                alert("登録に失敗しました");
            });
    }
});
calendar.render();