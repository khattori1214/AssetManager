<?php

namespace App\Http\Controllers;

use App\Models\Asset;
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
        $assetModel = new Asset();
        $assetManagementData = $assetModel->assetData();

        // $csvModel= new CsvFile();
        // $csvData= $csvModel->csvData();

        return view('admin.index', [
            'assetManagementData' => $assetManagementData,
            // 'csvData'=>$csvData,
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
            'category_id' => ['required', 'integer'],
            'asset_type' => ['required', 'in:loan,consumable'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'min_stock' => ['nullable', 'integer', 'min:0'],
        ]);

        $assetModel = new Asset();
        $assetModel->registerAsset($registerAsset);

        return redirect('/admin');
    }

    /**
     * 管理者用画面
     * 指定した資産を削除する
     */
    public function destroy($id)
    {
        Asset::where('asset_id', $id)->delete();

        return redirect('/admin');
    }

    /**
     * 管理者用画面
     * 消耗品の在庫数を更新する
     */
    public function updateStock(Request $request, $id)
    {
        $validated = $request->validate([
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        Asset::where('asset_id', $id)->update([
            'stock' => $validated['stock'],
        ]);

        return redirect('/admin');
    }

    /**
     * 管理者用画面
     * 編集対象の資産情報を取得する
     */
    public function edit($id)
    {
        $asset = Asset::where('asset_id', $id)->firstOrFail();

        return view('admin.edit', [
            'asset' => $asset,
        ]);
    }

    /**
     * 管理者用画面
     * 資産情報を更新する
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'asset_name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer'],
            'asset_type' => ['required', 'in:loan,consumable'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'min_stock' => ['nullable', 'integer', 'min:0'],
        ]);

        Asset::where('asset_id', $id)->update($validated);

        return redirect('/admin');
    }

    /**
 * 管理者画面
 * CSVファイルをダウンロードする
 */
// public function download($id)
// {

// }


}
