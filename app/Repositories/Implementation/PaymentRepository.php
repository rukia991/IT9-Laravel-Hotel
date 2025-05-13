<?php

namespace App\Repositories\Implementation;

use App\Models\Payment;
use App\Repositories\Interface\PaymentRepositoryInterface;
use Illuminate\Support\Facades\DB;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function store($request, $transaction, string $status)
    {
        DB::beginTransaction();
        try {
            $payment = Payment::create([
                'user_id' => Auth()->user()->id,
                'transaction_id' => $transaction->id,
                'price' => empty($request->downPayment) ? $request->payment : $request->downPayment,
                'status' => $status,
            ]);

            // Calculate total amount paid including this payment
            $totalPaid = $transaction->payment()->sum('price') + $payment->price;
            
            // If total paid equals or exceeds the total price, mark as Paid
            if ($totalPaid >= $transaction->getTotalPrice()) {
                $transaction->update(['status' => 'Paid']);
            }

            DB::commit();
            return $payment;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
