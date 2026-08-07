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
            ->paginate(10, ['*'], 'consumable_history_page')
            ->withQueryString();
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


    /**
     * 資産一覧・申請画面
     * ログインユーザーが当月に申請済みか判定する
     */
    public function alreadyRequestedThisMonth($userId)
    {
        return ConsumableHistory::where('user_id', $userId)
            ->whereYear('request_date', now()->year())
            ->whereMonth('request_date', now()->month())
            ->exists();
    }

    /**
     * 経理連携CSV出力バッチ
     * 指定期間の消耗品申請データを取得する
     */
    public function csvData($targetPeriodStart, $targetPeriodEnd)
    {
        return ConsumableHistory::join(
            'users',
            'consumable_histories.user_id',
            '=',
            'users.user_id'
        )
            ->join(
                'assets',
                'consumable_histories.asset_id',
                '=',
                'assets.asset_id'
            )
            ->whereBetween(
                'consumable_histories.request_date',
                [$targetPeriodStart, $targetPeriodEnd]
            )
            ->select(
                'consumable_histories.request_date',
                'users.employee_no',
                'users.user_name',
                'assets.asset_name',
                'consumable_histories.quantity'
            )
            ->orderBy('consumable_histories.request_date')
            ->get();
    }
}