@extends('layouts.app')

@section('content')

    <div class="content-area">

        @if (($overdueCount ?? 0) > 0)
            <div class="error-message">
                【警告】返却期限を過ぎている資産があります
                （{{ $overdueCount }}件）。
            </div>
        @endif

        <a href="/histories" class="{{ request()->is('histories*') ? 'active' : '' }}">
            <h1>利用履歴・返却画面</h1>
        </a>

        <h2>現在借りている資産</h2>
        <table border="1">
            <tr>
                <th>NO.</th>
                <th>資産名</th>
                <th>カテゴリ</th>
                <th>種別</th>
                <th>貸出日</th>
                <th>返却期限</th>
                <th>状態</th>
                <th>操作</th>
            </tr>

            @forelse ($loanhistoryData as $history)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $history->asset_name }}</td>
                    <td>{{ $history->category_name }}</td>
                    <td>貸出資産</td>
                    <td>{{ $history->loan_date }}</td>
                    <td>{{ $history->due_date }}</td>

                    <td>
                        @if ($history->due_date < today())
                            期限超過
                        @else
                            貸出中
                        @endif
                    </td>

                    <td>
                        <!-- 返却ボタン -->
                        <button type="button"
                            onclick="document.getElementById('returnModal{{ $history->loan_history_id }}').showModal()">
                            返却する
                        </button>



                        <!-- モーダル -->
                        <dialog id="returnModal{{ $history->loan_history_id }}">
                            <h2>返却確認</h2>

                            <p>
                                「{{ $history->asset_name }}」を返却しますが、よろしいですか？
                            </p>

                            <form action="/histories/return" method="POST">
                                @csrf

                                <input type="hidden" name="loan_history_id" value="{{ $history->loan_history_id }}">

                                <button type="submit">はい</button>

                                <button type="button"
                                    onclick="document.getElementById('returnModal{{ $history->loan_history_id }}').close()">
                                    キャンセル
                                </button>
                            </form>
                        </dialog>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">
                        現在貸出中の資産はありません。
                    </td>
                </tr>
            @endforelse
        </table>

        <div class="pagination-wrapper">
            {{ $loanhistoryData->links('pagination::bootstrap-4') }}
        </div>

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

            @if (
                    $pastloanhistoryData->count() === 0 &&
                    $consumablehistoryData->count() === 0
                )
                <tr>
                    <td colspan="7">
                        利用履歴はありません。
                    </td>
                </tr>
            @endif


            @foreach ($pastloanhistoryData as $history)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $history->asset_name }}</td>
                    <td>{{ $history->category_name }}</td>
                    <td>貸出資産</td>
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
                    <td>消耗品</td>
                    <td>{{ $history->request_date }}</td>
                    <td>{{ $history->quantity }} {{ $history->unit }}</td>
                    <td>取得済</td>
                </tr>
            @endforeach
        </table>

        <div class="pagination-wrapper">
            {{ $pastloanhistoryData->links('pagination::bootstrap-4') }}
        </div>


        <div class="pagination-wrapper">
            {{ $consumablehistoryData->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection