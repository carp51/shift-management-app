<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <title>シフト確定画面</title>
    @vite(['resources/css/app.css', 'resources/js/work_hope_calender.js', 'resources/js/work_confirm_calender.js'])
</head>
<body>
    <a href="/user/home" class="btn btn-primary">ホーム画面</a>
    <h3>従業員の希望シフト</h3>
    <div id='work_hope_calendar'></div>

    <a href="/user/home" class="btn btn-primary">自動作成</a>

    <div id='work_confirm_calendar'></div>
</body>
</html>