@extends('layouts.app')

@section('content')

    <div class="content-area">

        <a href="/admin/user/index" class="{{ request()->is('admin/user/index') ? 'active' : '' }}">
            <h1>ユーザー一覧画面</h1>
        </a>
        {{-- ユーザー一覧 --}}
        <h2>ユーザー一覧</h2>

        <a href="/admin/user/create/">新規ユーザー登録</a>

        <table border="1">
            <tr>
                <th>社員番号ID</th>
                <th>ユーザー名</th>
                <th>メールアドレス</th>
                <th>権限名</th>
                <th>ユーザー情報編集</th>
            </tr>

            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->user_id }}</td>
                    <td>{{ $user->user_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role_name }}</td>
                    <td>
                        <a href="/admin/user/edit/{{ $user->user_id }}">ユーザー情報編集</a>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection