@extends('layouts.app')

@section('content')
    <h1>資産一覧・申請画面</h1>

    <!-- 検索機能 -->
    <div class="box">
        <form action="/assets/search" method="get">
            <h3>検索条件</h3>
            <div>
                <label>資産名</label>
                <input type="text" name="keyword" value="{{request('keyword')}}" placeholder="例: PC">
            </div>

            <!-- <div>
                <label>カテゴリ</label>
                <input type="text" name="max" value="{{request('max')}}" placeholder="例: 2000">
            </div> -->

            <div><button type="submit" id="searchBtn">検索</button></div>
        </form>
    </div>

    <!-- 貸出資産一覧 -->
    <table border="1">
        <tr>
            <th>NO.</th>
            <th>資産名</th>
            <th>カテゴリ</th>
            <th>状態</th>
            <th>最大貸出期間</th>
            <th>操作</th>
        </tr>


        @foreach ($assetData as $asset)
            <tr>
                <td scope="col" class="px6 py-2">{{$asset->asset_id}}</td>
                <td scope="col" class="px6 py-2">{{$asset->asset_name}}</td>
                <td scope="col" class="px6 py-2">{{$asset->category_id}}</td>
                <td scope="col" class="px6 py-2">{{$asset->max_loan_days}}</td>
            </tr>
        @endforeach
    </table>

    <div>
        {{ $assetData->links() }}
    </div>
    <!-- 消耗品一覧 -->
    <table border="1">
        <tr>
            <th>NO.</th>
            <th>品名</th>
            <th>カテゴリ</th>
            <th>在庫数</th>
            <th>状態</th>
            <th>操作</th>
        </tr>

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


@endsection