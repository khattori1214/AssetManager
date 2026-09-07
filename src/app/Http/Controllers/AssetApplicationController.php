<?php

namespace App\Http\Controllers;
use App\Models\Asset;
use App\Models\LoanHistory;
use Illuminate\Http\Request;
use App\Models\ConsumableHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AssetApplicationController extends Controller
{

    /**
     * 資産一覧・申請画面の初期表示
     */
    public function index(Request $request)
    {

        $keyword = $request->input('keyword');
        $assetType = $request->input('asset_type');
        $status = $request->input('status');

        $loanAssetData = Asset::loanAssetData($keyword, $assetType,$status);
        $consumableAssetData = Asset::consumableAssetData($keyword, $assetType,$status);

        $user=Auth::user();
        $overdueCount = LoanHistory::countOverdue($user);
        $isLocked = LoanHistory::isLoanLocked($user);

        foreach ($loanAssetData as $asset) {
            $asset->is_borrowed =
                LoanHistory::isBorrowed($asset);
        }

        return view('assets.index', [
            'loanAssetData' => $loanAssetData,
            'consumableAssetData' => $consumableAssetData,
            'isLocked' => $isLocked,
            'overdueCount' => $overdueCount,
        ]);
    }


    public function acquire(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);
        $assetId=$validated['asset_id'];
        $asset = Asset::findConsumable($assetId);
        $user=Auth::user();
        $quantity = $validated['quantity'];

        if (!$asset) {
            return back()->with('error', __('messages.asset.asset_not_found'));
        }

        // 最大申請数チェック
        if ($validated['quantity'] > $asset->max_request_quantity) {
            return back()->with(
                'error',
                '1回の最大申請数を超えています。'
            );
        }

        // 月1回制限
        $requestedCount = ConsumableHistory::requestedCountThisMonth(
            $user,
            $asset
        );

        if ($requestedCount >= $asset->monthly_request_limit) {
            return back()->with(
                'error',
                'この消耗品は今月すでに申請済みです。'
            );
        }

        // 在庫不足
        if ($asset->stock < $validated['quantity']) {
            return back()->with(
                'error',
                '在庫数が不足しています。'
            );
        }

        DB::transaction(function () use ($asset, $user, $quantity ) {

            ConsumableHistory::registerHistory(
                $user,
                $asset,
                $quantity,
            );

            $asset->decreaseStock(
                $quantity,
            );
        });

        return back()->with(
            'success',
            '取得申請が完了しました。'
        );
    }
    /**
     * 貸出資産の貸出処理
     */
    public function borrow(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'integer'],
        ]);

        $user=Auth::user();
        $userId = $user->user_id;
        $assetId=$validated['asset_id'];
        $asset = Asset::findLoan($assetId);

        if (!$asset) {
            return back()->with('error', __('messages.asset.asset_not_found'));
        }

        // 7日以上超過している場合は貸出不可
        if (LoanHistory::isLoanLocked($userId)) {
            return back()->with(
                'error',
                '選択した資産は、すでに貸出中です。'
            );
        }

        // すでに貸出中
        if (LoanHistory::isBorrowed($asset)) {
            return back()->with(
                'error',
                __('messages.asset.already_borrowed')
            );
        }

        $dueDate = now()->addDays($asset->max_loan_days);

        loanHistory::borrow(
            $user,
            $assetId,
            $dueDate
        );

        return back()->with(
            'success',
            '貸出申請が完了しました。'
        );
    }

}
