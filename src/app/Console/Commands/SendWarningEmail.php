<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\LoanHistory;
use App\Mail\WarningMail;
use Illuminate\Support\Facades\Mail;

#[Signature('app:send-warning-email')]
#[Description('Command description')]
class SendWarningEmail extends Command
{

    /**
     * Execute the console command.
     */

    public function handle()
    {
        $targetPeriodStart = now()->submonth()->startofMonth();
        $targetPeriodEnd = now()->submonth()->endOfMonth();

        $loanhistory = new LoanHistory();
        $users = $loanhistory->overdueUsers();
        foreach ($users as $user) {
            Mail::to($user->email)
                ->send(new WarningMail($user));
        }

    }
}
