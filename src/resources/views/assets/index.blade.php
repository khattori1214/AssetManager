<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>資産一覧・申請画面（仮）</h1>

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

        <tr>
            @foreach ($assetData as $asset)
                <td scope="col" class="px6 py-2">{{$asset->asset_id}}</td>
                <td scope="col" class="px6 py-2">{{$asset->asset_name}}</td>
                <td scope="col" class="px6 py-2">{{$asset->category_id}}</td>
                <td scope="col" class="px6 py-2">{{$asset->max_loan_days}}</td>
                
            @endforeach
        </tr>
    </table>

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