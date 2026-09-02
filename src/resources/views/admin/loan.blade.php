<!-- @extends('layouts.app')

@section('content')

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
            </tr>

            @forelse ($showEmployeesHistories as $history)
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


@endsection -->