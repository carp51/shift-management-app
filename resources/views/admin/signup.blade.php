<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <title>登録画面</title>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h1 class="text-center">登録画面</h1>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.signup') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="name">氏名</label>
                                <input type="text" class="form-control" name="name" id="name" required value="{{ old('name') }}">
                            </div>
                            <div class="form-group">
                                <label for="username">ユーザー名</label>
                                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username">
                                @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="store">店舗名</label>
                                <input type="text" class="form-control" name="store" id="store" required value="{{ old('store') }}">
                            </div>
                            <div class="form-group">
                                <label for="password">パスワード</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" required>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">送信</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
