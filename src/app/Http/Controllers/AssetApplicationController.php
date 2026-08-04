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
    public function index()
    {
        $asset = new Asset();
        $assetData = $asset->assetData();
        return view('assets.index', ['assetData' => $assetData]);
    }


    /**
     * 資産名による検索処理
     */
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        $assetType = $request->input('asset_type');
        $asset = new Asset();
        $assetData = $asset->search($keyword, $assetType);

        return view('assets.index', ['assetData' => $assetData, 'keyword' => $keyword, 'assetType' => $assetType]);
    }

    /**
     * 消耗品の減算処理
     */
    public function acquire(Request $request)
    {
        $acquire = new Asset();
        $history = new ConsumableHistory();

        $validated = $request->validate([
            'asset_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:1'],
            [
                'quantity.required' =>
                    __('messages.asset.quantity_required'),
            ]
        ]);

        try {
            DB::transaction(function () use ($validated, $acquire, $history) {

                $history->registerHistory(
                    Auth::id(),
                    $validated['asset_id'],
                    $validated['quantity']
                );

                $acquireData = $acquire->decreaseStock(
                    $validated['asset_id'],
                    $validated['quantity']
                );

                if ($acquireData === 0) {
                   throw new \RuntimeException(
                        'insufficient_stock');
                }

            });
            return redirect('/assets')
                ->with('success',
                __('messages.asset.acquire_success'));
        } catch (\RuntimeException $e) {
            return redirect('/assets')->with(
                'error',
                __('messages.asset.insufficient_stock')
            );

        } catch (\Throwable $e) {
            return redirect('/assets')->with(
                'error',
                __('messages.asset.processing_failed')
            );
        }
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
        $borrow = new LoanHistory();

        $asset = $assetModel->findAsset($assetId);

        if (!$asset) {
            return redirect('/assets')
                ->with(
                'error',
                __('messages.asset.asset_not_found')
            );
        }

        $maxLoanDays = $asset->max_loan_days;
        $dueDate = now()->addDays($maxLoanDays);

        try {
            DB::transaction(function () use ($borrow, $userId, $assetId, $dueDate) {
                if ($borrow->isBorrowed($assetId)) {
                    throw new  \RuntimeException(
                        'already_borrowed');
                }

                $borrow->borrow(
                    $userId,
                    $assetId,
                    $dueDate
                );
            });

 return redirect('/assets')->with(
                'success',
                __('messages.asset.borrow_success')
            );

        } catch (\RuntimeException $e) {
            return redirect('/assets')->with(
                'error',
                __('messages.asset.already_borrowed')
            );

        } catch (\Throwable $e) {
            return redirect('/assets')->with(
                'error',
                __('messages.asset.processing_failed')
            );
        }
    }
}
