<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Transaction;
use App\Helpers\Helper;
use App\Repositories\Interface\PaymentRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        // Debug incoming request
        Log::info('Payment store method called', [
            'request_data' => $request->all(),
            'transaction_id' => $transaction->id,
            'user_id' => auth()->id()
        ]);

        // For cash payments, round up to the next whole number
        $maxAmount = $transaction->getTotalPrice() - $transaction->getTotalPayment();
        if ($request->payment_method === 'cash') {
            $maxAmount = ceil($maxAmount);
        }
        $insufficient = number_format($maxAmount, 2, '.', '');
        
        // Clean the payment amount by removing commas and ensuring proper decimal format
        $cleanPayment = str_replace(',', '', $request->payment);
        // If no decimal point, add .00
        if (strpos($cleanPayment, '.') === false) {
            $cleanPayment .= '.00';
        }
        
        Log::info('Payment amount after cleaning', [
            'original_payment' => $request->payment,
            'clean_payment' => $cleanPayment,
            'max_amount' => $maxAmount
        ]);

        // Create a new request with cleaned payment
        $validatedData = $request->all();
        $validatedData['payment'] = $cleanPayment;
        
        $request->merge(['payment' => $cleanPayment]);

        try {
            // Base validation rules
            $rules = [
                'payment_method' => 'required|in:cash,credit_card,debit_card,bank_transfer',
                'payment' => [
                    'required',
                    'numeric',
                    'regex:/^\d+\.?\d{0,2}$/',
                    'min:0',
                    'max:'.$insufficient
                ]
            ];

            // Add conditional validation rules based on payment method
            if (in_array($request->payment_method, ['credit_card', 'debit_card'])) {
                $rules['card_number'] = 'required';
                $rules['expiry_date'] = 'required';
                $rules['cvv'] = 'required|numeric|digits:3';
            } elseif ($request->payment_method === 'bank_transfer') {
                $rules['bank_name'] = 'required';
                $rules['reference_number'] = 'required';
            }

            $messages = [
                'payment.numeric' => 'The payment amount must be a number.',
                'payment.regex' => 'The payment amount cannot have more than 2 decimal places.',
                'payment.min' => 'The payment amount must be at least 0.',
                'payment.max' => $request->payment_method === 'cash' 
                    ? 'For cash payments, the amount cannot exceed ₱'.number_format($insufficient, 2).'.'
                    : 'The payment amount cannot exceed the remaining balance of ₱'.number_format($insufficient, 2).'.'
            ];

            $request->validate($rules, $messages);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Payment validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $payment = $this->paymentRepository->store($request, $transaction, 'Payment');
            
            Log::info('Payment created successfully', [
                'payment_id' => $payment->id,
                'amount' => $payment->price,
                'transaction_id' => $transaction->id
            ]);

            // For cash payments, record any excess as a note
            if ($request->payment_method === 'cash') {
                $actualPayment = min((float)$cleanPayment, $transaction->getTotalPrice() - $transaction->getTotalPayment());
                $excess = (float)$cleanPayment - $actualPayment;
                
                if ($excess > 0) {
                    $details = $this->getPaymentDetails($request);
                    $detailsArray = json_decode($details, true) ?: [];
                    $detailsArray['cash_excess'] = number_format($excess, 2);
                    $details = json_encode($detailsArray);
                } else {
                    $details = $this->getPaymentDetails($request);
                }
            } else {
                $details = $this->getPaymentDetails($request);
            }

            // Store payment method details
            $payment->update([
                'payment_method' => $request->payment_method,
                'payment_details' => $details
            ]);

            DB::commit();

            return redirect()
                ->route('receptionist.payments')
                ->with('success', 'Payment processed successfully. Amount: ₱'.number_format($cleanPayment, 2));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Payment processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()
                ->back()
                ->with('error', 'Failed to process payment. Please try again. Error: ' . $e->getMessage());
        }
    }

    private function getPaymentDetails(Request $request)
    {
        $details = [];

        switch ($request->payment_method) {
            case 'credit_card':
            case 'debit_card':
                $details = [
                    'card_number' => substr($request->card_number, -4), // Store only last 4 digits
                    'expiry_date' => $request->expiry_date
                ];
                break;
            case 'bank_transfer':
                $details = [
                    'bank_name' => $request->bank_name,
                    'reference_number' => $request->reference_number
                ];
                break;
        }

        if ($request->payment_notes) {
            $details['notes'] = $request->payment_notes;
        }

        return json_encode($details);
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
