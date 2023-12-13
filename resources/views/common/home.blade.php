<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <title>シフト確定画面</title>
    @vite(['resources/css/app.css', 'resources/js/shift_view_calender.js'])
</head>
<body>
    @include("components.header")

    <h3 style="text-align: center; margin-top: 30px;">今月のシフト</h3>
    <div id='shift_view_calendar'></div>
</body>
</html>