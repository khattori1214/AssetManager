<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use App\Models\LoanHistory;
use Illuminate\Http\Request;
use App\Models\CsvFile;


class AssetManagementController extends Controller
{
    /**
     * 管理者用画面
     * 登録済みの資産一覧を表示する
     */
    public function index()
    {

        $loanAssetData = Asset::loanAssetData();
        $consumableAssetData = Asset::consumableAssetData();

        foreach ($loanAssetData as $asset) {
            $asset->isBorrowed = LoanHistory::isBorrowed($asset);
        }

        $csvData = CsvFile::csvData();

        $currentEmployeeLoans = LoanHistory::currentEmployeeLoans();


        return view('admin.index', [
            'loanAssetData' => $loanAssetData,
            'consumableAssetData' => $consumableAssetData,
            'csvData' => $csvData,
            'currentEmployeeLoans' => $currentEmployeeLoans,
        ]);
    }

    /**
     * 管理者用画面
     * 新しい資産を登録する
     */
    public function store(Request $request)
    {
        $registerAsset = $request->validate([
            'asset_name' => ['required', 'string', 'max:255'],
            'category_id' => ['integer'],
            'asset_type' => ['required', 'in:loan,consumable'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'min_stock' => ['nullable', 'integer', 'min:0'],
            'unit' => ['required', 'string', 'max:50'],
            'max_request_quantity' => ['nullable', 'integer', 'min:1'],
            'monthly_request_limit' => ['nullable', 'integer', 'min:1'],
        ]);
        Asset::registerAsset($registerAsset);

        return redirect('/admin')
            ->with('success', '登録が完了しました。');
    }

    /**
     * 管理者用画面
     * 消耗品の在庫数を更新する
     */
    public function updateStock(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
        ]);

        // $asset = new Asset();
        $asset->updateConsumableStock($validated);

        return redirect('/admin')
            ->with('success', '在庫情報を更新しました。');
    }

    // 経理連携用CSVファイルをダウンロードする
    public function download()
    {
        $csv = CsvFile::latest('generated_at')->firstOrFail();

        $path = storage_path('app/csv/' . $csv->file_name);

        return response()->download($path, $csv->file_name);
    }

}
