<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
    public static function assetData(): LengthAwarePaginator
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
     * Summary of decreaseStock
     * @param mixed $quantity
     * @return int
     */
    public function decreaseStock(int $quantity): int
    {
        return Asset::where('asset_id', $asset->asset_id)
            ->where('asset_type', 'consumable')
            ->where('stock', '>=', $quantity)
            ->decrement('stock', $quantity);
    }

    /**
     * 管理者用の資産登録・在庫管理画面
     * 資産情報を登録する
     */
    public static function registerAsset(array $registerAsset): Asset
    {
        return Asset::create($registerAsset);
    }

    /**
     * 資産IDから貸出カテゴリ情報付きで資産を取得する
     */
    public static function findLoan(int $assetId): ?Asset
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
    public static function findConsumable(int $assetId): ?Asset
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
//    public function deleteAsset(int $id): int
//    {
//        return Asset::where('asset_id', $id)->delete();
//    }

    /**
     * 管理者画面
     * 消耗品の在庫情報を更新する
     * @param array $validated
     * @return bool
     */
    public function updateConsumableStock(array $validated): bool
    {
        $asset = Asset::where('asset_id', $id)
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
    public static function loanAssetData(
        ?string $keyword = null,
        ?string $assetType = null,
        ?string $status = null,
    ): LengthAwarePaginator {

        $query = Asset::leftJoin(
            'loan_categories',
            'assets.category_id',
            '=',
            'loan_categories.category_id'
        )->leftJoin('loan_histories', function ($join) {
            $join->on(
                'loan_histories.asset_id',
                '=',
                'assets.asset_id'
            )
                ->whereNull('loan_histories.return_date');
        })
            ->where('assets.asset_type', 'loan')
            ->select(
                'assets.*',
                'loan_categories.category_name',
                'loan_categories.max_loan_days',

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

        if ($status === 'loan_available') {
            $query->whereNull(
                'loan_histories.loan_history_id'
            );
        }

        if ($status === 'loan_unavailable') {
            $query->wherenotNull(
                'loan_histories.loan_history_id',
            );
        }

        return $query
            ->paginate(10, ['*'], 'loan_page')
            ->withQueryString();
    }

    /**
     * 管理者画面
     * 消耗品を10件ずつ取得する
     */
    public static function consumableAssetData(
        ?string $keyword = null,
        ?string $assetType = null,
        ?string $status = null,
    ): LengthAwarePaginator {
        $query = Asset::leftJoin(
            'loan_categories',
            'assets.category_id',
            '=',
            'loan_categories.category_id'
        )
            ->where('assets.asset_type', 'consumable')
            ->select(
                'assets.*',
                'loan_categories.category_name',
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

        if ($status === 'consumable_available') {
            $query->where(
                'stock',
                '>',
                '0',
            );
        }

        if ($status === 'consumable_need_to_order') {
            $query->where(
                'stock',
                '>',
                'min_stock',
            );
        }

        if ($status === 'consumable_unavailable') {
            $query->where(
                'stock',
                '=',
                '0'
            );
        }

        return $query
            ->paginate(10, ['*'], 'consumable_page')
            ->withQueryString();
    }
}
