<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $table = 'assets';

    protected $primaryKey = 'asset_id';

    protected $fillable = [
        'asset_name',
        'category_id',
        'asset_type',
        'stock',
        'min_stock',
        'unit',
        'max_request_quantity',
        'monthly_request_limit',
    ];

    /**
     * 資産一覧・申請画面
     * 資産と貸出カテゴリを結合して一覧を取得する
     */
    public function assetData()
    {
        return Asset::leftJoin(
            'loan_categories',
            'assets.category_id',
            '=',
            'loan_categories.category_id'
        )
            ->select(
                'assets.*',
                'loan_categories.category_name',
                'loan_categories.max_loan_days'
            )
            ->paginate(10);
    }


    /**
     * 資産一覧・申請画面
     * 消耗品の在庫を減算する
     */
    public function decreaseStock($assetId, $quantity)
    {
        return Asset::where('asset_id', $assetId)
            ->where('asset_type', 'consumable')
            ->where('stock', '>=', $quantity)
            ->decrement('stock', $quantity);
    }

    /**
     * 管理者用の資産登録・在庫管理画面
     * 資産情報を登録する
     */
    public function registerAsset($registerAsset)
    {
        return Asset::create($registerAsset);
    }

    /**
     * 資産IDから貸出カテゴリ情報付きで資産を取得する
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

    /**
     * 消耗品情報を取得する
     */
    public function findConsumable($assetId)
    {
        return Asset::where('assets.asset_id', $assetId)
            ->where('assets.asset_type', 'consumable')
            ->select(
                'assets.*',
                'max_request_quantity',
                'monthly_request_limit',
            )
            ->first();
    }

    /**
     * 管理者画面
     * 指定した資産を削除する
     */
    public function deleteAsset($assetId)
    {
        return Asset::where('asset_id', $assetId)->delete();
    }

    /**
     * 管理者画面
     * 消耗品の在庫情報を更新する
     */
    public function updateConsumableStock($assetId, array $validated)
    {
        $asset = Asset::where('asset_id', $assetId)
            ->where('asset_type', 'consumable')
            ->firstOrFail();

        return $asset->update([
            'stock' => $validated['stock'],
            'min_stock' => $validated['min_stock'],
        ]);
    }

    /**
     * 管理者画面
     * 貸出資産を10件ずつ取得する
     */
    public function loanAssetData(
        $keyword = null,
        $assetType = null
    ) {

        $query = Asset::leftJoin(
            'loan_categories',
            'assets.category_id',
            '=',
            'loan_categories.category_id'
        )
            ->where('assets.asset_type', 'loan')
            ->select(
                'assets.*',
                'loan_categories.category_name',
                'loan_categories.max_loan_days'
            );

        if (!empty($keyword)) {
            $query->where(
                'assets.asset_name',
                'like',
                '%' . $keyword . '%'
            );
        }

        if ($assetType === 'consumable') {
            $query->whereRaw('1 = 0');
        }

        return $query
            ->paginate(10, ['*'], 'loan_page')
            ->withQueryString();
    }

    /**
     * 管理者画面
     * 消耗品を10件ずつ取得する
     */
    public function consumableAssetData(
        $keyword = null,
        $assetType = null
    ) {

        $query = Asset::leftJoin(
            'loan_categories',
            'assets.category_id',
            '=',
            'loan_categories.category_id'
        )
            ->where('assets.asset_type', 'consumable')
            ->select(
                'assets.*',
                'loan_categories.category_name'
            );

        if (!empty($keyword)) {
            $query->where(
                'assets.asset_name',
                'like',
                '%' . $keyword . '%'
            );
        }

        if ($assetType === 'loan') {
            $query->whereRaw('1 = 0');
        }

        return $query
            ->paginate(10, ['*'], 'consumable_page')
            ->withQueryString();
    }
}