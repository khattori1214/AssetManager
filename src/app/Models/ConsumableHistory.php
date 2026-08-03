<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumableHistory extends Model
{

    protected $table = 'consumable_histories';

    protected $primaryKey = 'consumable_history_id';

    protected $fillable = [
        'user_id',
        'asset_id',
        'request_date',
        'quantity',
    ];

    //利用履歴・返却画面で使用
    public function historyData()
    {
        $consumablehistories = ConsumableHistory::get();
        return $consumablehistories;
    }

    /**
     * 資産一覧・申請画面
     * 消耗品取得履歴を登録する
     */
    public function registerHistory(
        int $userId,
        int $assetId,
        int $quantity
    ) {
        return ConsumableHistory::create([
            'user_id' => $userId,
            'asset_id' => $assetId,
            'request_date' => today(),
            'quantity' => $quantity,
        ]);
    }

}
