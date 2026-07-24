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
        <p>password</p>
        <input type="password" name="password">
        <br>
        <input type="submit" value="ログイン">
    </form>



</body>

</html>