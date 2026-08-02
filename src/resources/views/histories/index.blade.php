@extends('layouts.app')

@section('content')
    <h1>利用履歴・返却画面（仮）</h1>

    <h2>現在借りている資産</h2>
    <table border="1">
        <tr>
            <th>ユーザーID</th>
            <th>資産ID</th>
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
            <th>ユーザーID</th>
            <th>資産ID</th>
        </tr>

        @foreach ($pastloanhistoryData as $history)
            <tr>
                <td>{{ $history->user_id }}</td>
                <td>{{ $history->asset_id }}</td>
            </tr>
        @endforeach
    </table>
@endsection