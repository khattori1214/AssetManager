@extends('layouts.app')

@section('content')


    <div class="content-area">

        <h1>ユーザー登録画面</h1>

        <form method="POST" action="/admin/user/create">
            @csrf
            <label>従業員番号:
                <input type="text" name="employee_no">
            </label>
            <br>
            <label>ユーザー名:
                <input type="text" name="user_name">
            </label>
            <br>
            <label>メールアドレス:
                <input type="email" name="email">
            </label>
            <br>
            <label>パスワード:
                <input type="password" name="password">
            </label>
            <br>
            <label>権限ID:
                <select id="role_id" name="role_id">
                    <option value="1" @selected(request('role_id') === '1')>
                        管理者
                    </option>
                    <option value="2" @selected(request('role_id') === '2')>
                        一般社員
                    </option>
                </select>
            </label>
            <br>
            <button type="submit">登録</button>
        </form>
@endsection