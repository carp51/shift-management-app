<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>登録画面</title>
</head>
<body>
    <h1>登録画面</h1>
    <form action="" method="post">
        @csrf
        <label for="email">店舗名</label>
        <input type="text" name="store" id="store">
        <label for="name">ユーザー名</label>
        <input type="text" name="name" id="name">
        <label for="password">パスワード</label>
        <input type="password" name="password" id="password">
        <button type="submit">送信</button>
    </form>
</body>
</html>