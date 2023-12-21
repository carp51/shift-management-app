<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <title>ログイン画面</title>
    <style>
        /* 追加のスタイルをここに追加できます */
    </style>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h1 class="text-center">ログイン画面</h1>
                    </div>
                    <div class="card-body">
                        <form action="" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="username">ユーザー名</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" id="username" value="{{ old('username') }}" required>
                                @error('username')
                                    <span class="invalid-feedback" role="alert">
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="password">パスワード</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" required>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>ログインできませんでした</strong>
                                    </span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">送信</button>
                        </form>
                        <div class="mt-3 text-center">
                            <p>管理者の方は新規登録ができます。 <br><a href="/admin/signup">新規登録</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
