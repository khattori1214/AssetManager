@extends('layouts.app')

@section('content')

<h1>資産登録・在庫管理画面（仮）</h1>
<button type="button"
    onclick="document.getElementById('loanRegisterModal').showModal()">
    貸出資産を新規登録
</button>

<button type="button"
    onclick="document.getElementById('consumableRegisterModal').showModal()">
    消耗品を新規登録
</button>
<h2>貸出資産一覧</h2>

<table border="1">
    <tr>
        <th>NO.</th>
        <th>資産名</th>
        <th>カテゴリ</th>
        <th>状態</th>
        <th>最大貸出期間</th>
        <th>操作</th>
    </tr>


    @foreach ($assetManagementData as $asset)
    @if($asset->asset_type == 'loan')
    <tr>
        <td>{{ $asset->asset_id }}</td>
        <td>{{ $asset->asset_name }}</td>
        <td>{{ $asset->category_name }}</td>
        <td>利用可能</td>
        <td>{{ $asset->max_loan_days }}日</td>
        <td>
            <a href="/admin/assets/{{ $asset->asset_id }}/edit">編集</a>

            <form action="/admin/assets/{{ $asset->asset_id }}" method="post" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit">削除</button>
            </form>
        </td>
    </tr>
    @endif
    @endforeach
</table>

<div>
    {{ $assetManagementData->links() }}
</div>
<!-- 消耗品一覧 -->
<table border="1">


    @foreach ($assetManagementData as $asset)
    @if($asset->asset_type == 'consumable')
    <tr>
        <td>{{ $asset->asset_id }}</td>
        <td>{{ $asset->asset_name }}</td>
        <td>{{ $asset->category_name }}</td>
        <td>{{ $asset->stock }}</td>

        <td>
            @if($asset->stock <= $asset->min_stock)
                要発注
                @else
                在庫あり
                @endif
        </td>

        <td>
            <a href="/admin/assets/{{ $asset->asset_id }}/edit">編集</a>

            <form action="/admin/assets/{{ $asset->asset_id }}/stock" method="post" style="display:inline;">
                @csrf
                @method('PATCH')

                <input type="number"
                    name="stock"
                    value="{{ $asset->stock }}"
                    min="0">

                <button type="submit">在庫更新</button>
            </form>

            <form action="/admin/assets/{{ $asset->asset_id }}" method="post" style="display:inline;">
                @csrf
                @method('DELETE')

                <button type="submit">削除</button>
            </form>

        </td>
    </tr>
    @endif
    @endforeach
</table>



<dialog id="loanRegisterModal">
    <h2>貸出資産登録</h2>

    <form action="/admin/assets" method="post">
        @csrf

        <input type="hidden" name="asset_type" value="loan">

        <div>
            <label>資産名</label>
            <input type="text" name="asset_name" required>
        </div>

        <div>
            <label>カテゴリID</label>
            <input type="number" name="category_id" required>
        </div>

        <button type="submit">登録</button>

        <button type="button"
            onclick="document.getElementById('loanRegisterModal').close()">
            閉じる
        </button>
    </form>
</dialog>

<dialog id="consumableRegisterModal">
    <h2>消耗品登録</h2>

    <form action="/admin/assets" method="post">
        @csrf

        <input type="hidden" name="asset_type" value="consumable">

        <div>
            <label>品名</label>
            <input type="text" name="asset_name" required>
        </div>

        <div>
            <label>カテゴリID</label>
            <input type="number" name="category_id" required>
        </div>

        <div>
            <label>在庫数</label>
            <input type="number" name="stock" min="0" required>
        </div>

        <div>
            <label>最低在庫数</label>
            <input type="number" name="min_stock" min="0" required>
        </div>

        <button type="submit">登録</button>

        <button type="button"
            onclick="document.getElementById('consumableRegisterModal').close()">
            閉じる
        </button>
    </form>
</dialog>


@endsection