<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\CollectedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;



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
    public static function countOverdue(User $user): int
    {
        $overdueCount = LoanHistory::where('user_id', $userId)
            ->wherenull('return_date')
            ->where('due_date', '<', today())
            ->count();
        return $overdueCount;
    }

    /**
     * ログインユーザーが現在借りている資産を取得する
     */
    public static function historyData(User $user): LengthAwarePaginator
    {
        return LoanHistory::join(
            'assets',
            'loan_histories.asset_id',
            '=',
            'assets.asset_id'
        )
            ->join(
                'loan_categories',
                'assets.category_id',
                '=',
                'loan_categories.category_id'
            )
            ->where('loan_histories.user_id', $userId)
            ->whereNull('loan_histories.return_date')
            ->select(
                'loan_histories.*',
                'assets.asset_name',
                'assets.asset_type',
                'loan_categories.category_name'
            )
            ->paginate(10, ['*'], 'current_loan_page')
            ->withQueryString();
    }

    /**
     * ログインユーザーが過去に借りた資産を取得する
     */
    public static function pastHistoryData(User $user): LengthAwarePaginator
    {
        return LoanHistory::join(
            'assets',
            'loan_histories.asset_id',
            '=',
            'assets.asset_id'
        )
            ->join(
                'loan_categories',
                'assets.category_id',
                '=',
                'loan_categories.category_id'
            )
            ->where('loan_histories.user_id', $userId)
            ->whereNotNull('loan_histories.return_date')
            ->orderByDesc('loan_histories.return_date')
            ->select(
                'loan_histories.*',
                'assets.asset_name',
                'assets.asset_type',
                'loan_categories.category_name'
            )
            ->paginate(10, ['*'], 'past_loan_page')
            ->withQueryString();
    }

    /**
     * 指定した貸出履歴の返却日を現在日時に更新する
     */
    public function returnAsset(User $user): int
    {
        return LoanHistory::where('loan_history_id', $loanHistoryId)
            ->where('user_id', $userId)
            ->whereNull('return_date')
            ->update([
                'return_date' => now(),
            ]);

    }

    // 貸与資産貸出処理
    public static function borrow(User $user, Asset $asset, CarbonInterface $dueDate): LoanHistory
    {
        $borrowResister = LoanHistory::create([
            'user_id' => $userId,
            'asset_id' => $assetId,
            'due_date' => $dueDate,
            'loan_date' => now()
        ]);
        return $borrowResister;
    }

    /**
     * 指定した資産が貸出中か判定する
     */
    public static function isBorrowed(Asset $asset): bool
    {
        return LoanHistory::where('asset_id', $assetId)
            ->whereNull('return_date')
            ->exists();
    }
    /**
     * 期限超過警告メール
     */
    public static function overdueUsers(): Collection
    {
        return LoanHistory::join('users', 'users.user_id', '=', 'loan_histories.user_id')
            ->join('assets', 'assets.asset_id', '=', 'loan_histories.asset_id')
            ->wherenull('return_date')
            ->where('due_date', '<', today())
            ->select('users.email', 'users.user_name', 'loan_histories.due_date', 'assets.asset_name')
            ->get();
    }

    /**
     * 7日以上返却期限を超過している貸出があるか
     */
    public static function isLoanLocked(User $user): bool
    {
        return LoanHistory::where('user_id', $userId)
            ->wherenull('return_date')
            ->where('due_date', '<=', today()->subDays(7))
            ->exists();
    }

    // 全社員の貸出履歴を表示する
    public static function currentEmployeeLoans():Collection
    {
        return LoanHistory::join(
            'assets',
            'loan_histories.asset_id',
            '=',
            'assets.asset_id'
        )
            ->join(
                'loan_categories',
                'assets.category_id',
                '=',
                'loan_categories.category_id'
            )
            ->join(
                'users',
                'loan_histories.user_id',
                '=',
                'users.user_id'
            )
            ->whereNull('loan_histories.return_date')
            ->select(
                'users.user_id',
                'users.user_name',
                'assets.asset_name',
                'loan_categories.category_name',
                'loan_histories.loan_date',
                'loan_histories.due_date',
                'loan_histories.return_date',
            )
            ->get();
    }
}
