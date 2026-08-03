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
                <td>{{ $history->user_id }}</td>
                <td>{{ $history->asset_id }}</td>
                <td>
                    <form action="/histories/return" method="POST">
                        @csrf
                        <input type="hidden" name="loan_history_id" value="{{ $history->loan_history_id }}">
                        <button type="submit">返却</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>


    <h2>過去に申請した資産</h2>
    <table border="1">
        <tr>
            <th>NO.</th>
            <th>資産名</th>
            <th>カテゴリ</th>
            <th>種別</th>
            <th>貸出日</th>
            <th>返却日</th>
            <th>状態</th>
        </tr>

        @foreach ($pastloanhistoryData as $history)
            <tr>
                <td>{{ $history->user_id }}</td>
                <td>{{ $history->asset_id }}</td>
            </tr>
        @endforeach
    </table>
<<<<<<< HEAD
=======

>>>>>>> e704250 (feat: 資産申請・利用履歴機能を実装)
@endsection