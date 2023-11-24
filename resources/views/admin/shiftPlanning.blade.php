<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <title>シフト確定画面</title>
    @vite(['resources/css/app.css', 'resources/js/shift_planning_calendar.js'])
</head>
<body>
    <a href="/user/home" class="btn btn-primary">ホーム画面</a>
    <h3>日付ごとの勤務人数</h3>
    <div id='shift_planning_calendar'></div>
</body>
</html>