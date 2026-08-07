<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\CsvFile;
use App\Models\ConsumableHistory;

#[Signature('app:generate-accounting-csv')]
#[Description('Command description')]
class GenerateAccountingCsv extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetPeriodStart = now()->submonth()->startofMonth();
        $targetPeriodEnd = now()->submonth()->endOfMonth();
        $consumablehistory = new ConsumableHistory();
        // $csvData = $consumablehistory->historyData($targetPeriodStart,$targetPeriodEnd);

        $csvData = $consumablehistory->csvData($targetPeriodStart, $targetPeriodEnd);
        $fileName = 'accounting_' . now()->format('Ym') . '.csv';
        $csvContent = fopen(storage_path('app/csv/' . $fileName), 'w');
        foreach ($csvData as $csv) {
            fputcsv($csvContent, [
                $csv->request_date,
                $csv->employee_no,
                $csv->user_name,
                $csv->asset_name,
                $csv->quantity,
            ]);
        }
        fclose($csvContent);

        CsvFile::create([
            'file_name' => $fileName,
            'target_period_start' => $targetPeriodStart,
            'record_count' => $csvData->count(),
            'generated_at' => now(),
        ]);
        return Command::SUCCESS;
    }
}

