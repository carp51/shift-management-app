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
                successCallback(response.data);
            })
            .catch(() => {
                //バリデーションエラーなど
                alert("登録に失敗しました");
            });
    }
});
calendar.render();