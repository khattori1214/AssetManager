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
        $consumableHistory = new ConsumableHistory();
        $loanHistory = new LoanHistory();

        $consumableHistoryData = $consumableHistory->historyData($userId);
        $loanHistoryData = $loanHistory->historyData($userId);
        $pastLoanHistoryData = $loanHistory->pastHistoryData($userId);

        // 返却期限超過
        $overdueCount = $loanHistory->countOverdue($userId);

        return view('histories.index', ['consumableHistoryData' => $consumableHistoryData, 'loanHistoryData' => $loanHistoryData, 'pastLoanHistoryData' => $pastLoanHistoryData, 'overdueCount' => $overdueCount]);

    }


    public function returnAsset(Request $request)
    {
        $loanHistoryId = $request->input('loan_history_id');

        $loanHistory = new LoanHistory();

        $updated = $loanHistory->returnAsset(
            $loanHistoryId,
            Auth::id()
        );

        if ($updated) {
            return redirect('/histories')
                ->with('success', '返却が完了しました。');
        }
        return redirect('/histories')
            ->with('error', '返却処理を実行できませんでした。');
    }

}
