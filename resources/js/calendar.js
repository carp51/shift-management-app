import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from '@fullcalendar/interaction';
import axios from 'axios';

var calendarEl = document.getElementById("calendar");

// カレンダーで選択された曜日とシフトを取得
var displayStartDay;
var displayEndDay;
// 表示月のシフト確定状況
var displayShiftConfirmStatus;
// 表示月のシフトの公開状況
var displayShiftShowStatus;

// 曜日で選択ボタンが押されたら
document.getElementById('bulkSelectDecision').addEventListener('click', function(){
    // 曜日を選択するチェックボックスの要素を取得
    var day_of_week_checks = document.getElementsByClassName('day-of-week-checks');
    var day_of_week_checked_list = [];

    // シフト確定状態の場合、アラートを表示して処理を終了
    if (displayShiftConfirmStatus === 1) {
        alert("シフト確定済みのためシフトを追加できません")
        return;
    }

    // シフト公開状態の場合、アラートを表示して処理を終了
    if (displayShiftShowStatus === 1) {
        alert("シフト公開済みのためシフトを追加できません")
        return;
    }

    // 選択された曜日をリストに追加
    for (let i = 0; i < 7; i++) {
        if ( day_of_week_checks[i].checked === true ) {
            day_of_week_checked_list.push(day_of_week_checks[i].value);
        }
    }

    // シフトの選択状態を取得
    var shift_checks = document.getElementsByName('shift-checks');
    var shift_checked = '';

    for (let i = 0; i < 3; i++) {
        if ( shift_checks[i].checked === true ) {
            shift_checked = shift_checks[i].value;
        }
    }

    // 選択された曜日とシフトがある場合
    if (day_of_week_checked_list.length != 0 && shift_checked != '') {
        axios
            .post("/user/home/shift-hope/shift-bulk-add", {
                display_start_day: displayStartDay,
                display_end_day: displayEndDay,
                shift_checked: shift_checked,
                day_of_week_checked_list: day_of_week_checked_list,
            })
            .then(() => {
                document.location.reload();
            })
            .catch(() => {
                // エラー時の処理
                alert("一括登録に失敗しました");
            });
    }
});

// 一括削除ボタンが押されたら
document.getElementById('bulkDeleteDecision').addEventListener('click', function(info, successCallback, failureCallback){
    // シフトが確定状態の場合、アラートを表示して処理を終了
    if (displayShiftConfirmStatus === 1) {
        alert("シフト確定済みのためシフトを削除できません")
        return;
    }

    // シフトが公開状態の場合、アラートを表示して処理を終了
    if (displayShiftShowStatus === 1) {
        alert("シフト公開済みのためシフトを削除できません")
        return;
    }

    axios
        .post("/user/home/shift-hope/shift-bulk-delete", {
            display_start_day: displayStartDay,
            display_end_day: displayEndDay,
        })
        .then(() => {
            document.location.reload();
        })
        .catch(() => {
            // エラー時の処理
            alert("一括削除に失敗しました");
        });
});

// シフト確定ボタンが押されたら
document.getElementById('userShiftConfirmDecision').addEventListener('click', function(info, successCallback, failureCallback){
    // サーバーにシフト確定のリクエストを送信
    axios
        .post("/user/home/shift-hope/user-shift-confirm", {
            display_start_day: displayStartDay,
        })
        .then((response) => {
            // サーバーからの応答に応じてUIを更新
            if (response.data){
                // シフトが確定された場合のUIの変更
                document.getElementById('userShiftConfirmButton').innerText = '確定取り消し';
                document.getElementById('userShiftConfirmText').innerText = '希望シフト:  確定';
                document.getElementById('userShiftConfirmModalMessage').innerText = '希望シフトを取り消しますか？';
                displayShiftConfirmStatus = 1;
            } else {
                // シフトが未確定の場合のUIの変更
                document.getElementById('userShiftConfirmButton').innerText = 'シフト確定';
                document.getElementById('userShiftConfirmText').innerText = '希望シフト:  未確定';
                document.getElementById('userShiftConfirmModalMessage').innerText = '希望シフトを確定しますか？';
                displayShiftConfirmStatus = 0;
            };
        })
        .catch(() => {
            // エラー時の処理
            alert("シフト確定の処理に失敗しました");
        });
});


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

    // 日付範囲が選択された時の処理
    selectable: true,
    select: function (info) {
        var select_start_date = info.start.toISOString().slice(0, 10);
        var select_end_date = new Date(info.end.getTime() - 24 * 60 * 60 * 1000).toISOString().slice(0, 10);

        // 選択された日付が2日以上だと警告
        if (select_start_date !== select_end_date) {
            alert("一日だけ選択してください")
            return
        }

        // シフトが確定状態の場合、アラートを表示して処理を終了
        if (displayShiftConfirmStatus === 1) {
            alert("シフト確定済みのためシフトを追加できません")
            return;
        }

        // シフトが公開状態の場合、アラートを表示して処理を終了
        if (displayShiftShowStatus === 1) {
            alert("シフト公開済みのためシフトを追加できません")
            return;
        }

        var shiftType = null;

        // モーダルウィンドウを表示
        $('#exampleModal').modal('show').on('click', function () {
            // 以前のイベントリスナーを削除
            $('.early-shift-btn, .late-shift-btn, .fulltime-shift-btn').off('click');
        });

        $('.early-shift-btn, .late-shift-btn, .fulltime-shift-btn').on('click', function () {
            // クリックされたボタンのテキストを取得
            shiftType = $(this).text(); 
            if (shiftType) {
                axios
                    .post("/user/home/shift-hope/shift-add", {
                        start_date: info.start.valueOf(),
                        end_date: info.end.valueOf(),
                        shift_type: shiftType,
                    })
                    .then(() => {
                    document.location.reload();
                    })
                    .catch(() => {
                        // エラー時の処理
                        alert("シフト登録に失敗しました");
                    });
            }
            // モーダルウィンドウを閉じる
            $('#exampleModal').hide();
            // イベントリスナーを削除
            $('.early-shift-btn, .late-shift-btn, .fulltime-shift-btn').off('click');
        });
    },

    events: function (info, successCallback) {
        // 現在、表示されているカレンダーの最初と終わりの日付を取得する
        displayStartDay = info.start.valueOf();
        displayEndDay = info.end.valueOf();
      
        // Laravelのイベント取得処理の呼び出し
        const shiftPromise = axios.post("shift-hope/shift-get", {
          start_date: info.start.valueOf(),
          end_date: info.end.valueOf(),
        });
      
        const shiftStatusPromise = axios.post("shift-hope/shift-status-get", {
          start_date: info.start.valueOf(),
          end_date: info.end.valueOf(),
        });
      
        // Promise.allを使用して両方の非同期処理が完了したら結果を処理
        Promise.all([shiftPromise, shiftStatusPromise])
          .then((responses) => {
            const shiftData = responses[0].data;
            const shiftStatusData = responses[1].data;
      
            // 追加したイベントを削除
            calendar.removeAllEvents();
      
            // カレンダーに読み込み
            successCallback([...shiftData, ...shiftStatusData]);
          })
          .catch(() => {
            // エラー時の処理
            document.location.reload();
          });
        // シフト人数を追加する
        axios
            .post("../../admin/shift-planning/shift-planning-add", {
                start_date: info.start.valueOf(),
                end_date: info.end.valueOf(),
            })
            .then(() => {
            })
            .catch(() => {
                // エラー時の処理
                alert("シフト人数の登録に失敗しました");
            });
        // ユーザーのシフト確定状態を取得する
        axios
            .post("/user/home/shift-hope/user-shift-confirm-status-get", {
                display_start_day: displayStartDay,
            })
            .then((response) => {
                displayShiftConfirmStatus = response.data[0];
                displayShiftShowStatus = response.data[1]
                // UIを更新してシフト確定状態を表示
                if (displayShiftConfirmStatus){
                    document.getElementById('userShiftConfirmButton').innerText = '確定取り消し';
                    document.getElementById('userShiftConfirmText').innerText = '希望シフト: 確定';
                    document.getElementById('userShiftConfirmModalMessage').innerText = '希望シフトを取り消しますか？';
                } else {
                    document.getElementById('userShiftConfirmButton').innerText = 'シフト確定';
                    document.getElementById('userShiftConfirmText').innerText = '希望シフト:  未確定';
                    document.getElementById('userShiftConfirmModalMessage').innerText = '希望シフトを確定しますか？';
                };
            })
            .catch(() => {
                // エラー時の処理
                alert("ユーザーのシフト確定状態の取得に失敗しました");
            });
      },

    eventClick: function (info) {
        // 選択したシフトを取得
        var yourVariable = info.event._def.title; 
        // 選択したシフトが特定の条件を満たす場合、シフトの追加処理を行う
        if (yourVariable.trim().charAt(0) === '_' || yourVariable === '') {
            var shiftType = null;
    
            // モーダルウィンドウを表示
            $('#exampleModal').modal('show').on('click', function () {
                $('.early-shift-btn, .late-shift-btn, .fulltime-shift-btn').off('click');
            });
    
            // シフトタイプを選択するボタンのイベントリスナーを設定
            $('.early-shift-btn, .late-shift-btn, .fulltime-shift-btn').on('click', function () {
                // シフトが確定済みの場合、アラートを表示して処理を終了
                if (displayShiftConfirmStatus === 1) {
                    alert("シフト確定済みのためシフトを追加できません")
                    return;
                }
        
                // シフトが公開済みの場合、アラートを表示して処理を終了
                if (displayShiftShowStatus === 1) {
                    alert("シフト公開済みのためシフトを追加できません")
                    return;
                }

                // クリックされたシフトを取得
                shiftType = $(this).text(); 
                if (shiftType) {
                    axios
                        .post("/user/home/shift-hope/shift-add", {
                            start_date: info.event._instance.range.start.valueOf(),
                            end_date: info.event._instance.range.end.valueOf(),
                            shift_type: shiftType,
                        })
                        .then(() => {
                            // イベントの追加
                            calendar.addEvent({
                                title: shiftType,
                                start: info.event._instance.range.start,
                                end: info.event._instance.range.end,
                                allDay: true,
                            }, true
                            );
                            document.location.reload();
                        })
                        .catch(() => {
                            // エラー時の処理
                            alert("シフトの登録に失敗しました");
                        });
                }
                // モーダルウィンドウを閉じる
                $('#exampleModal').hide();
                // イベントリスナーを削除
                $('.early-shift-btn, .late-shift-btn, .fulltime-shift-btn').off('click');
            });
        } else {
            // シフトが確定済みの場合、アラートを表示して処理を終了
            if (displayShiftConfirmStatus === 1) {
                alert("シフト確定済みのためシフトを削除できません")
                return;
            }
            // シフトが公開済みの場合、アラートを表示して処理を終了
            if (displayShiftShowStatus === 1) {
                alert("シフト公開済みのためシフトを削除できません")
                return;
            }
            
            // 追加したシフトをクリックした場合、削除の確認ダイアログを表示
            if (confirm('削除しますか？')) {
                axios
                    .post("shift-hope/shift-delete", {
                        start_date: info.event._instance.range.start.valueOf(),
                        end_date: info.event._instance.range.end.valueOf(),
                    })
                    .then(() => {
                        document.location.reload();
                    })
                    .catch(() => {
                        // エラー時の処理
                        alert("削除に失敗しました");
                    });
                info.event.remove()
            }
        }
        
    },
      
});
calendar.render();