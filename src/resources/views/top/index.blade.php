@extends('layouts.app')

@section('content')

    <div class="content-area">

        @if ($overdueCount > 0)
            <a href="/histories" class="overdue-alert-link">
                <div class="error-message">
                    【警告】返却期限を過ぎている資産があります
                    （{{ $overdueCount }}件）。返却手続きへ
                </div>
        @endif

            <div class="menu-list">

                <a href="/assets" class="menu-card">
                    <span>資産一覧・申請画面</span>
                    <span>貸出資産の貸出申請や、消耗品の取得申請を行います。</span>
                </a>

                <a href="/histories" class="menu-card">
                    <span>利用履歴・返却画面</span>
                    <span>現在借りている資産や過去の利用履歴を確認し、貸出中の資産を返却します。</span>
                </a>

                @if(Auth::user()->role_id === 1)
                    <a href="/admin" class="menu-card">
                        <span>資産登録・在庫管理画面</span>
                        <span>【管理者のみ】貸出資産・消耗品の登録、消耗品の在庫更新、経理連携用CSVのダウンロードを行います。</span>
                    </a>
                @endif
            </div>
    </div>

@endsection