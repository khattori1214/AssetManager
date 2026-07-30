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
    
    public function historyData(){
        $loanhistories=LoanHistory::get();
        return $loanhistories;
    }

    public function pasthistoryData(){
        $loanhistories=LoanHistory::get();
        return $loanhistories;
    }


    public function returnAsset($loanHistoryId){
        $returnhistories=LoanHistory::where('loan_history_id', $loanHistoryId)
        ->wherenull('return_date')
        ->update(['return_date'=>now()]);
        return $returnhistories;
    }
}
