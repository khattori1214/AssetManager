<?php

namespace App\Http\Controllers;

use App\Models\ConsumableHistory;
use App\Models\LoanHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UsageHistoryController extends Controller
{
    //利用履歴・返却画面一覧表示
    public function index()
    {
        $userId = Auth::id();
        $consumablehistory = new ConsumableHistory();
        $loanhistory = new LoanHistory();

        $consumablehistoryData = $consumablehistory->historyData($userId);
        $loanhistoryData = $loanhistory->historyData($userId);
        $pastloanhistoryData = $loanhistory->pasthistoryData($userId);

        // 返却期限超過
        $overdueCount = $loanhistory->countOverdue($userId);

        return view('histories.index', ['consumablehistoryData' => $consumablehistoryData, 'loanhistoryData' => $loanhistoryData, 'pastloanhistoryData' => $pastloanhistoryData, 'overdueCount' => $overdueCount]);

    }

   
    public function returnAsset(Request $request)
    {
        $loanHistoryId = $request->input('loan_history_id');

        $loanhistory = new LoanHistory();

        $loanhistory->returnAsset($loanHistoryId, Auth::id());


        $updated = $loanhistory->returnAsset(
            $loanHistoryId,
            Auth::id()
        );

        if ($updated) {
            return redirect('/histories')
                ->with('error', '返却が完了しました。');
        }
        return redirect('/histories')
            ->with('error', '返却処理を実行できませんでした。');
    }

}
