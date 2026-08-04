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

    /**
     * 利用履歴・返却画面
     * ログインユーザーの消耗品取得履歴を取得する
     */
    public function historyData($userId)
    {
        return ConsumableHistory::join(
            'assets',
            'consumable_histories.asset_id',
            '=',
            'assets.asset_id'
        )
            ->leftJoin(
                'loan_categories',
                'loan_categories.category_id',
                '=',
                'assets.category_id'
            )
            ->where('consumable_histories.user_id', $userId)
            ->orderByDesc('consumable_histories.request_date')
            ->select(
                'consumable_histories.*',
                'assets.asset_name',
                'assets.asset_type',
                'assets.unit',
                'loan_categories.category_name'
            )
            ->paginate(10, ['*'], 'consumable_page');
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