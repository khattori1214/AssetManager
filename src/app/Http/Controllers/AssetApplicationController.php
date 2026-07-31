<?php

namespace App\Http\Controllers;
use App\Models\Asset;
use Illuminate\Http\Request;

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
        $asset = new Asset();
        $assetData = $asset->search($keyword);

        return view('assets.index', ['assetData' => $assetData, 'keyword' => $keyword]);
    }

    /**
     * 消耗品の減算処理
     */
    public function acquire(Request $request)
    {
        $acquire = new Asset();
        $assetId=$request->input('asset_id');
        $quantity=$request->input('quantity');
        $acquireData = $acquire->decreaseStock($assetId,$quantity);
        return redirect('/assets');
    }
}
