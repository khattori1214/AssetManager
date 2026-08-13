<?php

namespace App\Http\Controllers;

use App\Models\LoanHistory;
use Illuminate\Support\Facades\Auth;

class TopController extends Controller
{
    public function index()
    {
        $loanHistory = new LoanHistory();
        $userId = Auth::id();
        $overdueCount = $loanHistory->countOverdue($userId);
        return view('top.index', ['overdueCount' => $overdueCount]);
    }


}
