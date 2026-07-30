<?php

namespace App\Http\Controllers;

use App\Models\ConsumableHistory;
use App\Models\LoanHistory;
use Illuminate\Http\Request;


class UsageHistoryController extends Controller
{
    //利用履歴・返却画面
    public function index()
    {
        $consumablehistory = new ConsumableHistory();
        $loanhistory = new LoanHistory();
        $consumablehistoryData = $consumablehistory->historyData();
        $loanhistoryData = $loanhistory->historyData();

        $pastloanhistoryData = $loanhistory->pasthistoryData();
        return view('histories.index', ['consumablehistoryData' => $consumablehistoryData, 'loanhistoryData' => $loanhistoryData, 'pastloanhistoryData' => $pastloanhistoryData]);
    }


    public function returnAsset(Request $request)
    {
        $loanHistoryId = $request->input('loan_history_id');
        $loanhistory = new LoanHistory();

        $loanhistory->returnAsset($loanHistoryId, Auth::id());
        return redirect('/histories');
    }
}
