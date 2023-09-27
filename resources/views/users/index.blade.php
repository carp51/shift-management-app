<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>index</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div id="app" class="p-5">
        {{\Illuminate\Support\Facades\Auth::user()->store}}のメンバー
        <!-- 一覧表示するブロック ① -->
        <div v-if="state=='index'">
            <div class="mb-3">
                <button type="button" class="btn btn-success" @click="changeState('create')">追加</button>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>名前</th>
                        <th>E-Mail</th>
                        <th>ddd</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users">
                        <td v-text="user.name"></td>
                        <td v-text="user.email"></td>
                        <td class="text-right">
                            <button class="btn btn-warning" type="button" @click="changeState('edit', user)">変更</button>
                            <button class="btn btn-danger" type="button" @click="onDelete(user)">削除</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- ページ移動のリンク ③ -->
            {{ $users->links() }}
        </div>
        <!-- 追加＆変更するブロック ② -->
        <div v-if="state=='create' || state == 'edit'">
            <div class="form-group">
                <label>名前</label>
                <input type="text" class="form-control" v-model="params.name">
            </div>
            <div class="form-group">
                <label>メールアドレス</label>
                <input type="text" class="form-control" v-model="params.email">
            </div>
            <div class="bg-light px-3 py-2 mb-3" v-if="state == 'edit'">以下は省略可</div>
            <div class="form-group">
                <label>パスワード</label>
                <input type="password" class="form-control" v-model="params.password">
            </div>
            <div class="form-group">
                <label>パスワード（確認）</label>
                <input type="password" class="form-control" v-model="params.passwordConfirmation">
            </div>
            <button type="button" class="btn btn-link" @click="changeState('index')">戻る</button>
            <button type="button" class="btn btn-primary" @click="onSave">保存する</button>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.min.js"></script>
    <script>

        new Vue({
            el: '#app',
            data: {
                state: 'index',
                params: {
                    id: -1,
                    name: '',
                    email: '',
                    password: '',
                    passwordConfirmation: ''
                },
                users: [
                    // ユーザーデータをJSON化 ④
                    @foreach($users as $user)
                    {!! $user !!},
                    @endforeach
                ]
            },
            methods: {
                changeState(state, value) { // 状態を変化させて表示を切り替え ⑤

                    if(state === 'create') {

                        this.params = {
                            id: -1,
                            name: '',
                            email: '',
                            password: '',
                            passwordConfirmation: ''
                        };

                    } else if(state === 'edit') {

                        this.params = value;

                    }

                    this.state = state;

                },
                onSave() { // データ保存（追加＆変更） ⑥

                    const params = this.params;
                    let url = '/admin/users';
                    let method = 'POST';

                    if(this.state === 'edit') { // 変更の場合

                        url += '/'+ this.params.id;
                        method = 'PUT';

                    }

                    axios({ url, method, params })
                        .then(response => {

                            if(response.data.result === true) {

                                location.reload(); // 再読み込み

                            }

                        });

                },
                onDelete(user) { // データ削除 ⑦

                    if(confirm('削除します。よろしいですか？')) {

                        const url = '/admin/users/'+ user.id;
                        axios.delete(url)
                            .then(response => {

                                if(response.data.result === true) {

                                    location.reload(); // 再読み込み

                                }

                            });

                    }

                }
            }
        });

    </script>
    </body>
</html>