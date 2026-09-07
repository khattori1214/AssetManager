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
        $user = Auth::user();
        $userId=$user->user_id;
        $consumableHistoryData = ConsumableHistory::historyData($user);
        $loanHistoryData = LoanHistory::historyData($user);
        $pastLoanHistoryData = LoanHistory::pastHistoryData($user);

        // 返却期限超過
        $overdueCount = LoanHistory::countOverdue($user);

        return view('histories.index', ['consumableHistoryData' => $consumableHistoryData, 'loanHistoryData' => $loanHistoryData, 'pastLoanHistoryData' => $pastLoanHistoryData, 'overdueCount' => $overdueCount]);

    }


    public function returnAsset(Request $request)
    {
        $loanHistoryId = $request->input('loan_history_id');
        
        $userId = Auth::id();
        $user = Auth::user();

        $updated = loanHistory::returnAsset(
            $user
        );

        if ($updated) {
            return redirect('/histories')
                ->with('success', '返却が完了しました。');
        }
        return redirect('/histories')
            ->with('error', '返却処理を実行できませんでした。');
    }

}


