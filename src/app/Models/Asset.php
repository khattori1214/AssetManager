<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Asset extends Model
{
    //
    protected $table = 'assets';

    public function assetData()
    {
        $getassetData = Asset::join('loan_categories', 'assets.category_id', '=', 'loan_categories.category_id')->paginate(10);
        return $getassetData;
    }

    public function search($keyword)
    {
        $getSearch = Asset::leftjoin('loan_categories', 'assets.category_id', '=', 'loan_categories.category_id')
            ->where('assets.asset_name', 'like', '%' . $keyword . '%')
            ->select('assets.*', 'loan_categories.max_loan_days')
                ->paginate(10);
            return $getSearch;
    }
}
