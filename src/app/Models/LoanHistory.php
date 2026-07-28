<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class LoanHistory extends Model
{
    protected $table = 'loan_histories';
    protected $primaryKey = 'loan_history_id';

    public function countOverdue(int $userId): int
    {
        $overdueCount = LoanHistory::where('user_id', $userId)
            ->wherenull('return_date')
            ->where('due_date', '<', today())
            ->count();
        return $overdueCount;
    }

}
