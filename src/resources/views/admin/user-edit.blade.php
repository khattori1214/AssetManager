@extends('layouts.app')

@section('content')

        <div class="content-area">

                <h1>ユーザー編集画面</h1>

                <form method="POST" action="/admin/user/edit/{{$user->user_id}}">
                        @csrf
                        @method('PUT')
                        <label>
                                社員番号ID:
                                {{ $user->employee_no }}（変更不可）
                        </label>
                        <br>
                        <label>
                                ユーザー名:
                                <input type="text" name="user_name" value="{{ old('user_name', $user->user_name) }}" required>
                        </label>
                        <br>
                        <label>
                                メールアドレス:
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                        </label>
                        <br>
                        <label>
                                パスワード:
                                <input type="password" name="password" value="{{ old('password', $user->password) }}" required>
                        </label>
                        <br>
                        <label>
                                権限ID:
                                <select id="role_id" name="role_id">
                                        <option value="1" @selected(request('role_id') === '1')>
                                                管理者
                                        </option>
                                        <option value="2" @selected(request('role_id') === '2')>
                                                一般社員
                                        </option>
                                </select>
                        </label>
                        <button type="submit">登録する</button>
                </form>
@endsection