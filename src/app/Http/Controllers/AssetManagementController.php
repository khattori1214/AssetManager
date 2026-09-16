<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateLoanAssetRequest;
use App\Models\Asset;
use App\Models\User;
use App\Models\LoanHistory;
use Illuminate\Http\Request;
use App\Models\CsvFile;
use App\Http\Requests\StoreAssetRequest;


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
            $asset->is_borrowed = LoanHistory::isBorrowed($asset);
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
    public function store(StoreAssetRequest $request)
    {
        $validated = $request->validated();
        Asset::registerAsset($validated);
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

        $asset->updateConsumableStock($validated);

        return redirect('/admin')
            ->with('success', '在庫情報を更新しました。');
    }

    /**
     * 管理者用画面
     * 貸出資産・消耗品を削除する
     */
    public function destroy(int $id)
    {
        $asset = Asset::findOrFail($id);
        if (LoanHistory::isBorrowed($asset)){
            return redirect('/admin')
            ->with('error', '資産の削除に失敗しました。');
        }

        $asset->delete();
        return redirect('/admin')
            ->with('success', '該当の資産を削除しました。');
    }

    /**
     * 管理者用画面
     * 貸出資産を編集する
     */
    public function update(UpdateLoanAssetRequest $request, int $id)
    {
        $validated = $request->validated();
        $asset = Asset::where('asset_id', $id)
            ->where('asset_type', 'loan')
            ->findOrFail($id);
        $asset->update($validated);
        return redirect('/admin')->with('success', '該当資産の編集が完了しました。');
    }

    // 経理連携用CSVファイルをダウンロードする
    public function download()
    {
        $csv = CsvFile::latest('generated_at')->firstOrFail();

        $path = storage_path('app/csv/' . $csv->file_name);

        return response()->download($path, $csv->file_name);
    }

}
