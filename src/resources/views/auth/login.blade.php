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

        @error('login')
        <div class="error"><span>{{ $message }}</span></div>
        @enderror
        
        <label>社員番号ID</label>
        <input type="text" id="employee_no" name="employee_no">
        @error('employee_no')
        <div class="error"><span>{{ $message }}</span></div>
        @enderror
    
        <label>password</label>
        <input type="password" id="password" name="password">
        @error('password')
        <div class="error"><span>{{ $message }}</span></div>
        @enderror
        
        <br>
        <input type="submit" value="ログイン">
    </form>



</body>

</html>