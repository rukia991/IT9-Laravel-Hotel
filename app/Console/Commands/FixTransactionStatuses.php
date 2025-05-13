<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use Illuminate\Console\Command;

class FixTransactionStatuses extends Command
{
    protected $signature = 'transactions:fix-statuses';
    protected $description = 'Fix transaction statuses based on payment records';

    public function handle()
    {
        $this->info('Starting to fix transaction statuses...');
        
        $transactions = Transaction::with('payment')->get();
        $fixed = 0;

        foreach ($transactions as $transaction) {
            $totalPaid = $transaction->getTotalPayment();
            $totalPrice = $transaction->getTotalPrice();

            if ($totalPaid >= $totalPrice && $transaction->status === 'Pending') {
                $transaction->update(['status' => 'Paid']);
                $fixed++;
            }
        }

        $this->info("Fixed {$fixed} transaction(s).");
    }
} 