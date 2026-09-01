@extends('layouts.app')

@section('content')
    <div class="content-area">
        <a href="/assets" class="{{ request()->is('assets*') ? 'active' : '' }}">
            <h1>資産一覧・申請画面</h1>
        </a>

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

        {{-- 返却期限超過警告 --}}
        @if ($overdueCount > 0)
            <div class="error-message">
                【警告】返却期限を過ぎている資産があります
                （{{ $overdueCount }}件）。
            </div>
        @endif

        {{-- 検索フォーム --}}
        <div class="box">
            <form action="/assets" method="get">
                <h2>検索条件</h2>

                <div>
                    <label for="keyword">資産名</label>

                    <input type="text" id="keyword" name="keyword" value="{{ request('keyword') }}" maxlength="50"
                        placeholder="例:PC">
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
            @forelse ($loanAssetData as $asset)
                <tr>
                    <td>{{ $asset->asset_id }}</td>
                    <td>{{ $asset->asset_name }}</td>
                    <td>{{ $asset->category_name }}</td>

                    <td>
                        @if ($asset->is_borrowed)
                            貸出中
                        @else
                            利用可能
                        @endif
                    </td>

                    <td>{{ $asset->max_loan_days }}日</td>

                    <td>
                        <button type="button" onclick="document.getElementById('borrowModal{{ $asset->asset_id }}').showModal()"
                            @disabled($asset->is_borrowed || $isLocked)>

                            @if ($asset->is_borrowed)
                                貸出中
                            @else
                                借りる
                            @endif
                        </button>

                        {{-- ロック理由だけボタンの外に表示 --}}
                        @if (!$asset->is_borrowed && $isLocked)
                            <p class="disabled-reason">
                                返却期限を7日以上超過しているため借りることができません。
                            </p>
                        @endif


                        <dialog id="borrowModal{{ $asset->asset_id }}">
                            <h2>貸出確認</h2>

                            <p>
                                「{{ $asset->asset_name }}」を借りますか？
                            </p>

                            <form method="post" action="/assets/borrow">
                                @csrf

                                <input type="hidden" name="asset_id" value="{{ $asset->asset_id }}">

                                <button type="submit">借りる</button>

                                <button type="button" class="cancel-button"
                                    onclick="document.getElementById('borrowModal{{ $asset->asset_id }}').close()">
                                    キャンセル
                                </button>
                            </form>
                        </dialog>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">貸出資産はありません。</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-wrapper">
        {{ $loanAssetData->links('pagination::bootstrap-4') }}
    </div>

    {{-- 消耗品一覧 --}}
    <h2>消耗品一覧</h2>

    <table border="1">
        <thead>
            <tr>
                <th>NO.</th>
                <th>品名</th>
                <th>在庫数</th>
                <th>一回の申請上限</th>
                <th>月間の申請上限</th>
                <th>状態</th>
                <th>操作</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($consumableAssetData as $asset)
                <tr>
                    <td>{{ $asset->asset_id }}</td>
                    <td>{{ $asset->asset_name }}</td>
                    <td>{{ $asset->stock }}</td>
                    <td>{{ $asset->max_request_quantity }}</td>
                    <td>{{ $asset->monthly_request_limit }}</td>

                    <td>
                        @if ($asset->stock < $asset->min_stock)
                            <strong>要発注</strong>
                        @else
                            在庫あり
                        @endif
                    </td>

                    <td>
                        <button type="button"
                            onclick="document.getElementById('acquireModal{{ $asset->asset_id }}').showModal()"
                            @disabled($asset->stock <= 0)>
                            取得する
                        </button>

                        <dialog id="acquireModal{{ $asset->asset_id }}">
                            <h2>取得数量入力</h2>

                            <p>
                                「{{ $asset->asset_name }}」の取得数量を入力してください。
                            </p>

                            <form method="post" action="/assets/acquire">
                                @csrf

                                <input type="hidden" name="asset_id" value="{{ $asset->asset_id }}">

                                <div>
                                    <label for="quantity{{ $asset->asset_id }}">
                                        数量
                                    </label>

                                    <input type="number" id="quantity{{ $asset->asset_id}}" name="quantity" min="1"
                                        max="{{ min($asset->stock, $asset->max_request_quantity) }}" value="1" required>
                                </div>

                                <button type="submit">取得する</button>

                                <button type="button" class="cancel-button"
                                    onclick="document.getElementById('acquireModal{{ $asset->asset_id }}').close()">
                                    キャンセル
                                </button>
                            </form>
                        </dialog>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">消耗品はありません。</td>
                </tr>
            @endforelse
        </tbody>

    </table>

    <div class="pagination-wrapper">
        {{ $consumableAssetData->links('pagination::bootstrap-4') }}
    </div>
    </div>

@endsection