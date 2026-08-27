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

        $assetModel = new Asset();
        $loanHistory = new LoanHistory();


        $loanAssetData = $assetModel->loanAssetData($keyword, $assetType);
        $consumableAssetData = $assetModel->consumableAssetData($keyword, $assetType);

        $overdueCount = $loanHistory->countOverdue(Auth::id());
        $isLocked = $loanHistory->isLoanLocked(Auth::id());

        foreach ($loanAssetData as $asset) {
            $asset->is_borrowed =
                $loanHistory->isBorrowed($asset->asset_id);
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
        $assetModel = new Asset();
        $history = new ConsumableHistory();

        $validated = $request->validate([
            'asset_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $asset = $assetModel->findConsumable($validated['asset_id']);

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
        $requestedCount = $history->requestedCountThisMonth(
            Auth::id(),
            $validated['asset_id']
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

        DB::transaction(function () use ($history, $assetModel, $validated) {

            $history->registerHistory(
                Auth::id(),
                $validated['asset_id'],
                $validated['quantity']
            );

            $assetModel->decreaseStock(
                $validated['asset_id'],
                $validated['quantity']
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

        $assetId = $validated['asset_id'];
        $userId = Auth::id();

        $assetModel = new Asset();
        $loanHistory = new LoanHistory();

        $asset = $assetModel->findAsset($assetId);

        if (!$asset) {
            return back()->with('error', __('messages.asset.asset_not_found'));
        }

        // 7日以上超過している場合は貸出不可
        if ($loanHistory->isLoanLocked($userId)) {
            return back()->with(
                'error',
                '選択した資産は、すでに貸出中です。'
            );
        }

        // すでに貸出中
        if ($loanHistory->isBorrowed($assetId)) {
            return back()->with(
                'error',
                __('messages.asset.already_borrowed')
            );
        }

        $dueDate = now()->addDays($asset->max_loan_days);

        $loanHistory->borrow(
            $userId,
            $assetId,
            $dueDate
        );

        return back()->with(
            'success',
            '貸出申請が完了しました。'
        );
    }

}
