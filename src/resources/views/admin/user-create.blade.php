@extends('layouts.app')

@section('content')


    <div class="content-area">

          <h1>ユーザー登録画面</h1>
        
        <form method="POST" action="/admin/user/create">
            @csrf
            <label>社員番号ID: <input type="text" name="employee_no"></label><br>
            <label>ユーザー名: <input type="text" name="user_name"></label><br>
            <label>メールアドレス: <input type="email" name="email"></label><br>
            <label>パスワード: <input type="password" name="password"></label><br>
            <label>権限ID: <input type="text" name="role_id"></label><br>
            <button type="submit">登録</button>
        </form>

@endsection