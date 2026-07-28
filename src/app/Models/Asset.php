<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Asset extends Model
{
    //
    protected $table = 'assets';

    public function assetData()
    {
        $getassetData = Asset::join('loan_categories', 'assets.category_id', '=', 'loan_categories.category_id')->get();
        return $getassetData;
    }
}
