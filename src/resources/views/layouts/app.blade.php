<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    <title>AssetManager</title>
</head>

<body>

    <!-- 上部ヘッダー -->
    <header class="app-header">

        <h2>AssetManager-社内資産・備品管理システム-</h2>

        <div>
            {{ Auth::user()->user_name }}
        </div>

        <form action="/logout" method="POST" class="logout-form">
            @csrf

            <button type="submit" class="logout-button">
                ログアウト
            </button>
        </form>

    </header>

    <div class="app-body">
        <!-- 左メニュー -->
        <aside class="sidebar">

            <ul>

                <li>
                    <a href="/top">
                        トップ画面
                    </a>
                </li>

                <li>
                    <a href="/assets">
                        資産一覧・申請画面
                    </a>
                </li>

                <li>
                    <a href="/histories">
                        利用履歴・返却画面
                    </a>
                </li>

                @if(Auth::user()->role_id == 1)

                    <li>
                        <a href="/admin">
                            資産登録・在庫管理<br>【管理者のみ】
                        </a>
                    </li>

                @endif

            </ul>

        </aside>

        <!-- 各画面 -->
        <main class="main-content">

            {{-- 成功メッセージ --}}
            @if (session('success'))
                <div class="message success-message">
                    {{ session('success') }}
                </div>
            @endif

            {{-- エラーメッセージ --}}
            @if (session('error'))
                <div class="message error-message">
                    {{ session('error') }}
                </div>
            @endif

            {{-- バリデーションエラー --}}
            @if ($errors->any())
                <div class="message error-message">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif


            @yield('content')

        </main>

    </div>

</body>

</html>