<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Asset extends Model
{
    //
    protected $table = 'assets';

    protected $fillable = [
        'asset_name',
        'category_id',
        'stock',
        'asset_name',
        'asset_type',
        'min_stock',
    ];

    /**
     * 資産一覧・申請画面
     * 資産と貸出カテゴリを結合して一覧を取得する
     */
    public function assetData()
    {
        $getassetData = Asset::join('loan_categories', 'assets.category_id', '=', 'loan_categories.category_id')->paginate(10);
        return $getassetData;
    }


    /**
     * 資産一覧・申請画面-検索機能-
     * 入力された資産名で資産を部分一致検索する
     */
    public function search($keyword)
    {
        $getSearch = Asset::leftjoin('loan_categories', 'assets.category_id', '=', 'loan_categories.category_id')
            ->where('assets.asset_name', 'like', '%' . $keyword . '%')
            ->select('assets.*', 'loan_categories.max_loan_days')
            ->paginate(10);
        return $getSearch;
    }

    /**
     * 資産一覧・申請画面-消耗品減算機能-
     */
    public function decreaseStock($assetId, $quantity)
    {
        return Asset::where('asset_id', $assetId)
            ->where('asset_type', 'consumable')
            ->where('stock', '>', $quantity)
            ->decrement('stock', $quantity);
    }


    /**
     * 管理者用の資産登録・在庫管理画面
     * Controllerから受け取った資産情報をassetsテーブルへ登録する
     */

    public function registerAsset($registerAsset)
    {
        $registerAssetData = Asset::create($registerAsset);
        return $registerAssetData;
    }
}
