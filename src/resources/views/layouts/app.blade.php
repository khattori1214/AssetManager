<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>AssetManager</title>
</head>

<body>

    <!-- 上部ヘッダー -->
    <header>

        <h2>AssetManager</h2>

        <div>
            ログイン中：
            {{ Auth::user()->user_name }}
        </div>

        <form action="/logout" method="post">
            @csrf
            <button>ログアウト</button>
        </form>

    </header>

    <hr>

    <div style="display:flex;">

        <!-- 左メニュー -->
        <aside style="width:220px">

            <ul>

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
                            資産登録・在庫管理
                        </a>
                    </li>

                @endif

            </ul>

        </aside>

        <!-- 各画面 -->
        <main style="flex:1">

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