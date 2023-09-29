<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン画面</title>
</head>
<body>
<h1>ログイン画面</h1>
    <form action="" method="post">
        @csrf
        <label for="name">ユーザーネーム</label>
        <input type="name" name="name" id="name">
        <label for="password">パスワード</label>
        <input type="password" name="password" id="password">
        <button type="submit">送信</button>
    </form>
</body>
</html>