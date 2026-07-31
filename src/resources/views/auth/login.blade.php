<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=2">
</head>

<body class="login-page">
    <h3>AssetManager
        -社内資産・備品管理システム-</h3>
    <h4>ログイン画面</h4>

    <form action="/login" method="post">
        @csrf

        @error('login')
            <div class="error"><span>{{ $message }}</span></div>
        @enderror

        <label>社員番号ID</label>
        <br>
        <input type="text" id="employee_no" name="employee_no">
        @error('employee_no')
            <div class="error"><span>{{ $message }}</span></div>
        @enderror

        <br>
        <label>password</label>
        <br>
        <input type="password" id="password" name="password">
        @error('password')
            <div class="error"><span>{{ $message }}</span></div>
        @enderror

        <br>
        <input type="submit" value="ログイン">
    </form>



</body>

</html>