<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// 警告メール送信バッチ：毎日午前7時
Schedule::command('app:send-warning-email')
    ->dailyAt('07:00');

// 経理連携CSV出力バッチ：毎月1日の午前1時
Schedule::command('app:generate-accounting-csv')
    ->monthlyOn(1, '01:00');

