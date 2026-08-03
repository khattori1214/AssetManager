<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class LoanHistory extends Model
{
    protected $table = 'loan_histories';
    protected $primaryKey = 'loan_history_id';

    protected $fillable = [
        'user_id',
        'asset_id',
        'loan_date',
        'due_date',
        'return_date',
    ];


    /**
     * トップ画面
     * ログインユーザーの期限超過・未返却件数を取得する
     */
    public function countOverdue(int $userId): int
    {
        $overdueCount = LoanHistory::where('user_id', $userId)
            ->wherenull('return_date')
            ->where('due_date', '<', today())
            ->count();
        return $overdueCount;
    }

    /**
     * 利用履歴・返却画面
     * ログインユーザーが現在借りている資産を取得する
     */
    public function historyData()
    {
        $loanhistories = LoanHistory::get();
        return $loanhistories;
    }

    /**
     * 利用履歴・返却画面
     * ログインユーザーが過去に借りた資産を取得する
     */
    public function pasthistoryData()
    {
        $loanhistories = LoanHistory::get();
        return $loanhistories;
    }

    /**
     * 利用履歴・返却画面
     * 指定した貸出履歴の返却日を現在日時に更新する
     */
    public function returnAsset($loanHistoryId)
    {
        $returnhistories = LoanHistory::where('loan_history_id', $loanHistoryId)
            ->wherenull('return_date')
            ->update(['return_date' => now()]);
        return $returnhistories;
    }

    // 貸与資産貸出処理
    public function borrow($userId, $assetId, $dueDate)
    {
        $borrowResister = LoanHistory::create([
            'user_id' => $userId,
            'asset_id' => $assetId,
            'due_date' => $dueDate,
            'loan_date' => now()
        ]);
        return $borrowResister;
    }

    // 貸出中へ更新する
    public function isBorrowed($assetId)
    {
        return LoanHistory::where('asset_id', $assetId)
            ->wherenull('return_date')
            ->exists();
    }
}
