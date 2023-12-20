<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>index</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    @include("components.header")

    <div id="app" class="p-5">
        {{ \Illuminate\Support\Facades\Auth::user()->store->name }}のメンバー
        <!-- 一覧表示するブロック ① -->
        <div v-if="state=='index'">
            <div class="mb-3">
                <button type="button" class="btn btn-success" @click="changeState('create')">追加</button>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>名前</th>
                        <th>名前</th>
                        <th>役割</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users">
                        <td v-text="user.name"></td>
                        <td v-text="user.name"></td>
                        <td v-text="user.role"></td>
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
                <label>ユーザーID</label>
                <input type="text" id="username" class="form-control" name="username" v-model="params.username">
                <!-- Vue.jsでのエラーメッセージ表示 -->
                <span class="valid-feedback" v-if="errors" v-if="errors.username">OK</span>
                <span class="invalid-feedback" v-if="errors && errors.username" v-text="errors.username[0]"></span>
            </div>
            <div class="bg-light px-3 py-2 mb-3" v-if="state == 'edit'">以下は省略可</div>
            <div class="form-group">
                <label>パスワード</label>
                <input type="password" id="password" class="form-control" name="password" v-model="params.password">
                <span class="valid-feedback" v-if="errors" v-if="errors.password">OK</span>
                <span class="invalid-feedback" v-if="errors && errors.password" v-text="errors.password[0]"></span>
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
                    passwordConfirmation: '',
                    username: ''
                },
                users: [
                    // ユーザーデータをJSON化 ④
                    @foreach($users as $user)
                    {!! $user!!},
                    @endforeach
                ],
            errors: null,
            },
            methods: {
            changeState(state, value) { // 状態を変化させて表示を切り替え ⑤

                if (state === 'create') {

                    this.params = {
                        id: -1,
                        name: '',
                        email: '',
                        password: '',
                        passwordConfirmation: '',
                        username: ''
                    };

                } else if (state === 'edit') {

                    this.params = value;

                }

                this.state = state;

            },
            onSave() { // データ保存（追加＆変更） ⑥

                const params = this.params;
                let url = '/admin/users';
                let method = 'POST';

                if (this.state === 'edit') { // 変更の場合

                    url += '/' + this.params.id;
                    method = 'PUT';

                }

                axios({ url, method, params })
                    .then(response => {
                        if (response.data.length == 0) {

                            location.reload(); // 再読み込み
                        } else {
                            this.errors = response.data;
                            if (!this.errors) {
                                console.error("Error data is undefined.");
                            } else {
                                console.log(this.errors);
                                // alert(this.errors.username[0]);
                                const elm_username = $('#username');
                                const elm_pass = $('#password');

                                console.log('username' in this.errors);
                                console.log('password' in this.errors);


                                if ('username' in this.errors) {
                                    elm_username.removeClass("is-valid");
                                    elm_username.addClass("is-invalid");
                                } else {
                                    elm_username.removeClass("is-invalid");
                                    elm_username.addClass("is-valid");
                                }

                                if ('password' in this.errors) {
                                    elm_pass.removeClass("is-valid");
                                    elm_pass.addClass("is-invalid");
                                } else {
                                    elm_pass.removeClass("is-invalid");
                                    elm_pass.addClass("is-valid");
                                }
                            }
                        }
                    });

            },
            onDelete(user) { // データ削除 ⑦

                if (confirm('削除します。よろしいですか？')) {

                    const url = '/admin/users/' + user.id;
                    axios.delete(url)
                        .then(response => {

                            if (response.data.result === true) {

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