<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>HOME</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    {{\Illuminate\Support\Facades\Auth::user()->name}}でログインしています。

    <form action="{{route('admin.logout')}}" method="post">
        @csrf
        <button>ログアウト</button>
    </form>

    @if (Auth::user()->role === 'admin')
        <p>あなたは管理者です</p>
    @elseif (Auth::user()->role === 'user')
        <p>あなたは一般ユーザーです</p>
    @endif
    <a href="/admin/users">管理</a>

    <div id='calendar'></div>
</body>
</html>