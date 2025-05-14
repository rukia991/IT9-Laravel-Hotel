<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Transaction;
use App\Helpers\Helper;
use App\Repositories\Interface\PaymentRepositoryInterface;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository
    ) {
    }

    public function index()
    {
        $payments = Payment::orderBy('id', 'DESC')->paginate(5);

        return view('payment.index', ['payments' => $payments]);
    }

    public function create(Transaction $transaction)
    {
        return view('transaction.payment.create', [
            'transaction' => $transaction,
        ]);
    }

    public function store(Transaction $transaction, Request $request)
    {
        $insufficient = $transaction->getTotalPrice() - $transaction->getTotalPayment();
        
        // Get the payment amount from either the numeric or original input
        $paymentAmount = $request->payment_numeric ?? str_replace(',', '.', str_replace('.', '', $request->payment));
        
        $request->merge(['payment' => $paymentAmount]);
        
        $request->validate([
            'payment' => 'required|numeric|lte:'.$insufficient,
        ]);

        $this->paymentRepository->store($request, $transaction, 'Payment');

        return redirect()->route('transaction.index')->with('success', 'Transaction room '.$transaction->room->number.' success, '.Helper::convertToRupiah($request->payment).' paid');
    }

    public function invoice(Payment $payment)
    {
        return view('payment.invoice', [
            'payment' => $payment,
        ]);
    }

    public function receptionistPayments()
    {
        // Get approved transactions that need payment
        $transactions = Transaction::with(['customer', 'room.type'])
            ->where('status', 'Approved')
            ->get();

        // Calculate statistics
        $stats = [
            'totalPaid' => Payment::sum('amount'),
            'totalPending' => $transactions->sum(function ($transaction) {
                return $transaction->getTotalPrice() - $transaction->getTotalPayment();
            }),
            'partialCount' => $transactions->filter(function ($transaction) {
                $paid = $transaction->getTotalPayment();
                return $paid > 0 && $paid < $transaction->getTotalPrice();
            })->count(),
            'totalTransactions' => $transactions->count()
        ];

        return view('receptionist.payments', [
            'transactions' => $transactions,
            'stats' => $stats
        ]);
    }

    public function processPayment(Transaction $transaction)
    {
        return view('receptionist.process-payment', [
            'transaction' => $transaction->load(['customer', 'room', 'payment'])
        ]);
    }
}
