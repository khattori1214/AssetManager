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
    public function search($keyword, $assetType)
    {
        $query = Asset::leftjoin('loan_categories', 'assets.category_id', '=', 'loan_categories.category_id')
            ->select('assets.*', 'loan_categories.category_name', 'loan_categories.max_loan_days');

        if (!empty($keyword)) {
            $query->where('assets.asset_name', 'like', '%' . $keyword . '%');
        }

        if (!empty($assetType)) {
            $query->where('assets.asset_type', $assetType);
        }

        return $query->paginate(10)->withQueryString();
    }

    /**
     * 資産一覧・申請画面-消耗品減算機能-
     */
    public function decreaseStock($assetId, $quantity)
    {
        return Asset::where('asset_id', $assetId)
            ->where('asset_type', 'consumable')
<<<<<<< HEAD
            ->where('stock', '>', $quantity)
=======
            ->where('stock', '>=', $quantity)
>>>>>>> e704250 (feat: 資産申請・利用履歴機能を実装)
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

    /**
     * 資産IDからカテゴリ情報付きで資産を取得する
     */
    public function findAsset($assetId)
    {
        return Asset::join(
            'loan_categories',
            'assets.category_id',
            '=',
            'loan_categories.category_id'
        )
            ->where('assets.asset_id', $assetId)
            ->where('assets.asset_type', 'loan')
            ->select(
                'assets.*',
                'loan_categories.max_loan_days'
            )
            ->first();
    }

}
