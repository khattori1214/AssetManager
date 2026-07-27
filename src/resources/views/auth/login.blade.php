<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>ログイン画面</h1>

    <form action="/login" method="post">
        @csrf
        <p>社員番号ID</p>
        <input type="text" name="employee_no">
        @error('employee_no')
        <div class="error"><span>{{ $message }}</span></div>
        @enderror
        @error('login')
        <div class="error"><span>{{ $message }}</span></div>
        @enderror
        <p>password</p>
        <input type="password" name="password">
        @error('password')
        <div class="error"><span>{{ $message }}</span></div>
        @enderror
        @error('login')
        <div class="error"><span>{{ $message }}</span></div>
        @enderror
        <br>
        <input type="submit" value="ログイン">
    </form>



</body>

</html>