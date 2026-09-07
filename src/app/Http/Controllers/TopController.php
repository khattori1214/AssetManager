<?php

namespace App\Http\Controllers;

use App\Models\LoanHistory;
use Illuminate\Support\Facades\Auth;

class TopController extends Controller
{
    public function index()
    {
        $loanHistory = new LoanHistory();
        $user=Auth::user();
        
        $overdueCount = $loanHistory->countOverdue($user);
        return view('top.index', ['overdueCount' => $overdueCount]);
    }


}
