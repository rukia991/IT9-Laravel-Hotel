<?php

namespace App\Repositories\Implementation;

use App\Models\Payment;
use App\Repositories\Interface\PaymentRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function store($request, $transaction, string $status)
    {
        DB::beginTransaction();
        try {
            // Log the incoming data
            Log::info('Payment data:', [
                'user_id' => Auth()->user()->id,
                'transaction_id' => $transaction->id,
                'payment' => $request->payment,
                'downPayment' => $request->downPayment,
                'status' => $status
            ]);

            $payment = Payment::create([
                'user_id' => Auth()->user()->id,
                'transaction_id' => $transaction->id,
                'amount' => empty($request->downPayment) ? $request->payment : $request->downPayment,
                'status' => $status,
            ]);

            // Log the created payment
            Log::info('Payment created:', $payment->toArray());

            // Calculate total amount paid including this payment
            $totalPaid = $transaction->payment()->sum('amount') + $payment->amount;
            
            // If total paid equals or exceeds the total price, mark as Paid
            if ($totalPaid >= $transaction->getTotalPrice()) {
                $transaction->update(['status' => 'Paid']);
            }

            DB::commit();
            return $payment;
        } catch (\Exception $e) {
            DB::rollback();
            // Log the error
            Log::error('Payment creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
