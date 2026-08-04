<?php

namespace App\Http\Controllers;

use App\Models\ConsumableHistory;
use App\Models\LoanHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UsageHistoryController extends Controller
{
    //利用履歴・返却画面
    public function index()
    {
        $userId = Auth::id();
        $consumablehistory = new ConsumableHistory();
        $loanhistory = new LoanHistory();

        $consumablehistoryData = $consumablehistory->historyData($userId);
        $loanhistoryData = $loanhistory->historyData($userId);
        $pastloanhistoryData = $loanhistory->pasthistoryData($userId);
        $historyData = $consumablehistory->historyData($userId);
        // 返却期限超過
        $overdueCount = $loanhistory->countOverdue($userId);

        return view('histories.index', ['consumablehistoryData' => $consumablehistoryData, 'loanhistoryData' => $loanhistoryData, 'pastloanhistoryData' => $pastloanhistoryData, 'historyData' => $historyData, 'overdueCount' => $overdueCount]);

    }


    public function returnAsset(Request $request)
    {
        $loanHistoryId = $request->input('loan_history_id');
        $loanhistory = new LoanHistory();

        $loanhistory->returnAsset($loanHistoryId, Auth::id());
        return redirect('/histories');
    }

}
