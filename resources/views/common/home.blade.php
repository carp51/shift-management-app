<!doctype html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

    <div class="modal" tabindex="-1" id="exampleModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">シフトの種類を選択してください</h5>
                </div>
                <div class="d-flex justify-content-around">
                    <div>
                        <button type="button" class="early-shift-btn" data-bs-dismiss="modal">早番</button>
                        <button type="button" class="late-shift-btn" data-bs-dismiss="modal">遅番</button>
                        <button type="button" class="fulltime-shift-btn" data-bs-dismiss="modal">通し</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>