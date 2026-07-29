<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>資産一覧・申請画面（仮）</h1>

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
            <th></th>
        </tr>

        <tr>
            @foreach ($assetData as $asset)
                <td scope="col" class="px6 py-2">{{$asset->asset_id}}</td>
                <td scope="col" class="px6 py-2">{{$asset->asset_name}}</td>
                <td scope="col" class="px6 py-2">{{$asset->category_id}}</td>
                <td scope="col" class="px6 py-2">{{$asset->stock}}</td>
            @endforeach
        </tr>
    </table>

</body>

</html>