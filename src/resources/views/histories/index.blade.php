@extends('layouts.app')

@section('content')
    <h1>利用履歴・返却画面（仮）</h1>

    <h2>現在借りている資産</h2>
    <table border="1">
        <tr>
            <th>NO.</th>
            <th>資産名</th>
            <th>カテゴリ</th>
            <th>種別</th>
            <th>貸出日</th>
            <th>状態</th>
            <th>操作</th>
        </tr>

        @foreach ($loanhistoryData as $history)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $history->asset_name }}</td>
                <td>{{ $history->category_name }}</td>
                <td>{{ $history->asset_type }}</td>
                <td>{{ $history->loan_date }}</td>
                <!-- <td>@if ($history->due_date < today())
                    期限超過
                @else
                        貸出中
                    @endif
                </td> -->

                <td>
                    <!-- 返却ボタン -->
                    <button type="button"
                        onclick="document.getElementById('returnModal{{ $history->loan_history_id }}').showModal()">
                        返却
                    </button>



                    <!-- モーダル -->
                    <dialog id="returnModal{{ $history->loan_history_id }}">
                        <h2>返却確認</h2>

                        <p>
                            「{{ $history->asset_name }}」を返却しますか？
                        </p>

                        <form action="/histories/return" method="POST">
                            @csrf

                            <input type="hidden" name="loan_history_id" value="{{ $history->loan_history_id }}">

                            <button type="submit">はい</button>

                            <button type="button"
                                onclick="document.getElementById('returnModal{{ $history->loan_history_id }}').close()">
                                いいえ
                            </button>
                        </form>
                    </dialog>
                </td>
            </tr>
        @endforeach
    </table>

    <h2>過去に申請した資産・消耗品一覧</h2>
    <table border="1">
        <tr>
            <th>NO.</th>
            <th>資産名</th>
            <th>カテゴリ</th>
            <th>種別</th>
            <th>申請日・貸出日</th>
            <th>返却日・数量</th>
            <th>状態</th>
        </tr>

        @foreach ($pastloanhistoryData as $history)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $history->asset_name }}</td>
                <td>{{ $history->category_name }}</td>
                <td>{{ $history->asset_type }}</td>
                <td>{{ $history->loan_date }}</td>
                <td>{{ $history->return_date }}</td>
                <td>返却済</td>
            </tr>
        @endforeach

        @foreach ($consumablehistoryData as $history)
            <tr>   
                <td>{{ $loop->iteration }}</td>
                <td>{{ $history->asset_name }}</td>
                <td>{{ $history->category_name ?? '-' }}</td>
                <td>{{ $history->asset_type }}</td>
                <td>{{ $history->request_date }}</td>
                <td>{{ $history->quantity }} {{ $history->unit }}</td>
                <td>取得済</td>
            </tr>
        @endforeach
    </table>
@endsection