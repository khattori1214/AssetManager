<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumableHistory extends Model
{

    protected $table = 'consumable_histories';

    //利用履歴・返却画面で使用
    public function historyData()
    {
        $consumablehistories = ConsumableHistory::get();
        return $consumablehistories;
    }

    
}
