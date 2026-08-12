<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン | AssetManager</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=3">
</head>

<body class="login-page">
    <main class="login-card">
        <h1>AssetManager</h1>
        <p class="login-subtitle">
            社内資産・備品管理システム
        </p>

        <form action="/login" method="post" class="login-form">
            @csrf

            @error('login')
                <div class="error-message">
                    {{ $message }}
                </div>
            @enderror

            <div class="form-group">
                <label for="employee_no">社員番号ID</label>
                <input type="text" id="employee_no" name="employee_no" placeholder="社員番号を入力">
                @error('employee_no')
                    <p class="field-error">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="form-group">
            
                <label for="password">パスワード</label>
                <div class="password-field">
                <input type="password" id="password" name="password" placeholder="パスワードを入力">
                @error('password')
                    <p class="field-error">
                        {{ $message }}
                    </p>
                @enderror
                </div>
            </div>

            <button type="submit" class="login-button">
                ログイン
            </button>
        </form>
    </main>

</body>

</html>