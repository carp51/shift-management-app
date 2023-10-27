<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
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
        <a href="/admin/users">管理</a>
    @elseif (Auth::user()->role === 'user')
        <p>あなたは一般ユーザーです</p>
    @endif

    <div id='calendar'></div>
</body>
</html>