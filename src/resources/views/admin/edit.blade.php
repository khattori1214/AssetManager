<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>資産編集</title>
</head>

<body>
  <h1>資産編集</h1>

  <form action="/admin/assets/{{ $asset->asset_id }}" method="post">
    @csrf
    @method('PUT')

    <div>
      <label>資産名</label>
      <input
        type="text"
        name="asset_name"
        value="{{ old('asset_name', $asset->asset_name) }}"
        required>
    </div>

    <div>
      <label>カテゴリID</label>
      <input
        type="number"
        name="category_id"
        value="{{ old('category_id', $asset->category_id) }}"
        required>
    </div>

    <div>
      <label>資産種別</label>

      <select name="asset_type" required>
        <option
          value="loan"
          @selected(old('asset_type', $asset->asset_type) === 'loan')
          >
          貸出資産
        </option>

        <option
          value="consumable"
          @selected(old('asset_type', $asset->asset_type) === 'consumable')
          >
          消耗品
        </option>
      </select>
    </div>

    <div>
      <label>在庫数</label>
      <input
        type="number"
        name="stock"
        min="0"
        value="{{ old('stock', $asset->stock) }}">
    </div>

    <div>
      <label>最低在庫数</label>
      <input
        type="number"
        name="min_stock"
        min="0"
        value="{{ old('min_stock', $asset->min_stock) }}">
    </div>

    <button type="submit">更新</button>

    <a href="/admin">戻る</a>
  </form>
  <h2>経理連携CSV一覧</h2>

  <table border="1">
    <tr>
      <th>対象月</th>
      <th>ファイル名</th>
      <th>操作</th>
    </tr>

    @foreach ($csvData as $csv)
    <tr>
      <td>{{ $csv->target_month }}</td>
      <td>{{ $csv->file_name }}</td>
      <td>
        ダウンロード
      </td>
    </tr>
    @endforeach

  </table>

</body>

</html>