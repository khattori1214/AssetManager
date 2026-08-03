@extends('layouts.app')

@section('content')
<<<<<<< HEAD
=======

>>>>>>> e704250 (feat: 資産申請・利用履歴機能を実装)
    <h1>資産一覧・申請画面</h1>

    {{-- 成功メッセージ --}}
    @if (session('success'))
        <div>
            {{ session('success') }}
        </div>
    @endif

    {{-- エラーメッセージ --}}
    @if (session('error'))
        <div>
            {{ session('error') }}
        </div>
    @endif

    {{-- バリデーションエラー --}}
    @if ($errors->any())
        <div>
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- 貸出ロック警告 --}}
    @if ($isLocked ?? false)
        <div>
            【警告】返却期限を7日以上過ぎている資産があります。
        </div>
    @endif

    {{-- 検索フォーム --}}
    <div class="box">
        <form action="/assets/search" method="get">
            <h2>検索条件</h2>

            <div>
                <label for="keyword">資産名</label>

                <input type="text" id="keyword" name="keyword" value="{{ request('keyword') }}" placeholder="例：PC">
            </div>

            <div>
                <label for="asset_type">資産種別</label>

                <select id="asset_type" name="asset_type">
                    <option value="">すべて</option>

                    <option value="loan" @selected(request('asset_type') === 'loan')>
                        貸出資産
                    </option>

                    <option value="consumable" @selected(request('asset_type') === 'consumable')>
                        消耗品
                    </option>
                </select>
            </div>

            <button type="submit" id="searchBtn">
                検索
            </button>
        </form>
    </div>

    {{-- 貸出資産一覧 --}}
    <h2>貸出資産一覧</h2>

    <table border="1">
        <thead>
            <tr>
                <th>NO.</th>
                <th>資産名</th>
                <th>カテゴリ</th>
                <th>状態</th>
                <th>最大貸出期間</th>
                <th>操作</th>
            </tr>
        </thead>

        <tbody>
            @php
                $loanAssetExists = false;
            @endphp

            @foreach ($assetData as $asset)
                @if ($asset->asset_type === 'loan')
                    @php
                        $loanAssetExists = true;

                        $loanHistory = new \App\Models\LoanHistory();

                        $isBorrowed = $loanHistory->isBorrowed(
                            $asset->asset_id
                        );
                    @endphp

                    <tr>
                        <td>{{ $asset->asset_id }}</td>

                        <td>{{ $asset->asset_name }}</td>

                        <td>
                            {{ $asset->category_name }}
                        </td>

                        <td>
                            @if ($isBorrowed)
                                貸出中
                            @else
                                利用可能
                            @endif
                        </td>

                        <td>
                            {{ $asset->max_loan_days }}日
                        </td>

                        <td>
                            <button type="button" onclick="
                                            document
                                                .getElementById(
                                                    'borrowModal{{ $asset->asset_id }}'
                                                )
                                                .showModal()
                                        " @disabled(
                                            $isBorrowed ||
                                            ($isLocked ?? false)
                                        )>
                                @if ($isBorrowed)
                                    貸出中
                                @else
                                    貸出
                                @endif
                            </button>

                            {{-- 貸出確認ダイアログ --}}
                            <dialog id="borrowModal{{ $asset->asset_id }}" class="dialog">
                                <h2>貸出確認</h2>

                                <p>
                                    「{{ $asset->asset_name }}」を
                                    貸し出しますか？
                                </p>

                                <form method="post" action="/assets/borrow">
                                    @csrf

                                    <input type="hidden" name="asset_id" value="{{ $asset->asset_id }}">

                                    <button type="submit">
                                        はい
                                    </button>

                                    <button type="button" onclick="
                                                    document
                                                        .getElementById(
                                                            'borrowModal{{ $asset->asset_id }}'
                                                        )
                                                        .close()
                                                ">
                                        いいえ
                                    </button>
                                </form>
                            </dialog>
                        </td>
                    </tr>
                @endif
            @endforeach

            @if (!$loanAssetExists)
                <tr>
                    <td colspan="6">
                        貸出資産はありません。
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- 消耗品一覧 --}}
    <h2>消耗品一覧</h2>

    <table border="1">
        <thead>
            <tr>
                <th>NO.</th>
                <th>品名</th>
                <th>カテゴリ</th>
                <th>在庫数</th>
                <th>状態</th>
                <th>操作</th>
            </tr>
        </thead>

        <tbody>
            @php
                $consumableExists = false;
            @endphp

            @foreach ($assetData as $asset)
                @if ($asset->asset_type === 'consumable')
                    @php
                        $consumableExists = true;
                    @endphp

                    <tr>
                        <td>{{ $asset->asset_id }}</td>

                        <td>{{ $asset->asset_name }}</td>

                        <td>
                            {{ $asset->category_name }}
                        </td>

                        <td>
                            {{ $asset->stock }}
                        </td>

                        <td>
                            @if ($asset->stock < $asset->min_stock)
                                <strong>要発注</strong>
                            @else
                                在庫あり
                            @endif
                        </td>

                        <td>
                            <button type="button" onclick="
                                            document
                                                .getElementById(
                                                    'acquireModal{{ $asset->asset_id }}'
                                                )
                                                .showModal()
                                        " @disabled(
                                            $asset->stock <= 0 ||
                                            ($isLocked ?? false)
                                        )>
                                取得
                            </button>

                            {{-- 消耗品取得ダイアログ --}}
                            <dialog id="acquireModal{{ $asset->asset_id }}" class="dialog">
                                <h2>取得数量入力</h2>

                                <p>
                                    「{{ $asset->asset_name }}」の
                                    取得数量を入力してください。
                                </p>

                                <form method="post" action="/assets/acquire">
                                    @csrf

                                    <input type="hidden" name="asset_id" value="{{ $asset->asset_id }}">

                                    <div>
                                        <label for="quantity{{ $asset->asset_id }}">
                                            数量
                                        </label>

                                        <input type="number" id="quantity{{ $asset->asset_id }}" name="quantity" min="1"
                                            max="{{ $asset->stock }}" value="1" required>
                                    </div>

                                    <button type="submit">
                                        はい
                                    </button>

                                    <button type="button" onclick="
                                                    document
                                                        .getElementById(
                                                            'acquireModal{{ $asset->asset_id }}'
                                                        )
                                                        .close()
                                                ">
                                        いいえ
                                    </button>
                                </form>
                            </dialog>
                        </td>
                    </tr>
                @endif
            @endforeach

            @if (!$consumableExists)
                <tr>
                    <td colspan="6">
                        消耗品はありません。
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <div>
        {{ $assetData->links() }}
    </div>

<<<<<<< HEAD
        <tr>
            @foreach ($assetData as $asset)
                <td scope="col" class="px6 py-2">{{$asset->asset_id}}</td>
                <td scope="col" class="px6 py-2">{{$asset->asset_name}}</td>
                <td scope="col" class="px6 py-2">{{$asset->category_id}}</td>
                <td scope="col" class="px6 py-2">{{$asset->stock}}</td>

                <!-- 貸出資産取得モーダル -->
                <button id="openButton" onClick="document.getElementById('modalDialog').showModal()">モーダルを開く(貸出資産)</button>
                <dialog id="modalDialog" class="dialog">
                    <div id="dialog-container">
                        <header>
                            <span>取得</span>
                            <button id="closeButton" type="button" onclick="document.getElementById('modalDialog').close()">
                                <p>閉じる</p>
                            </button>
                        </header>
                        <div>Message</div>
                        <form method="post" action="/assets/borrow">
                            @csrf

                            <input type="submit" name="asset_id" value="{{ $asset->asset_id }}">
                            <input type="submit" name="quantity" min="1" max="{{ $asset->stock }}" value="1" required>
                            <button type="submit">はい</button>
                            <button type="button" onclick="document.getElementById('modalDialog').close()">いいえ</button>
                        </form>
                    </div>
                </dialog>

                <!-- 消耗品取得モーダル -->
                <button id="openButton" onClick="document.getElementById('modalDialog').showModal()">モーダルを開く(消耗品)</button>
                <dialog id="modalDialog" class="dialog">
                    <div id="dialog-container">
                        <header>
                            <span>取得</span>
                            <button id="closeButton" type="button" onclick="document.getElementById('modalDialog').close()">
                                <p>閉じる</p>
                            </button>
                        </header>
                        <div>Message</div>
                        <form method="post" action="/assets/acquire">
                            @csrf

                            <input type="hidden" name="asset_id" value="{{ $asset->asset_id }}">
                            <input type="number" name="quantity" min="1" max="{{ $asset->stock }}" value="1" required>
                            <button type="submit">はい</button>
                            <button type="button" onclick="document.getElementById('modalDialog').close()">いいえ</button>
                        </form>
                    </div>
                </dialog>
            @endforeach
        </tr>
    </table>


=======
>>>>>>> e704250 (feat: 資産申請・利用履歴機能を実装)
@endsection