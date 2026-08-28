<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 警告メール送信バッチ：毎日10時40分
Schedule::command('app:send-warning-email')
    ->dailyAt('10:37');

// 経理連携CSV出力バッチ：毎日10時40分
Schedule::command('app:generate-accounting-csv')
    ->dailyAt('10:37');
// Schedule::command('app:generate-accounting-csv')
//     ->monthlyOn(1, '00:00');

// 警告メール送信バッチ：毎日1時
// Schedule::command('app:send-warning-email')
//     ->dailyAt('01:00');

// 経理連携CSV出力バッチ：毎月1日の0時
// Schedule::command('app:generate-accounting-csv')
//     ->monthlyOn(1, '00:00');

