<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Repositories\Interface\TransactionRepositoryInterface;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository
    ) {
    }

    public function index(Request $request)
    {
        $transactions = $this->transactionRepository->getTransaction($request);
        $transactionsExpired = $this->transactionRepository->getTransactionExpired($request);

        return view('transaction.index', [
            'transactions' => $transactions,
            'transactionsExpired' => $transactionsExpired,
        ]);
    }

    public function receptionistReservations()
    {
        // Get all reservations with relationships
        $reservations = Transaction::with(['customer.user', 'room.type'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get counts for dashboard
        $pendingReservationsList = Transaction::with(['customer', 'room'])
            ->where('status', 'Pending')
            ->orderBy('check_in')
            ->take(5)
            ->get();

        $pendingReservations = Transaction::where('status', 'Pending')->count();
        $todayCheckins = Transaction::whereDate('check_in', Carbon::today())
            ->where('status', 'Approved')
            ->count();

        return view('receptionist.reservations', [
            'reservations' => $reservations,
            'pendingReservations' => $pendingReservations,
            'pendingReservationsList' => $pendingReservationsList,
            'todayCheckins' => $todayCheckins
        ]);
    }

    public function approveReservation(Transaction $transaction)
    {
        // Check if room is still available for the dates
        $conflictingBookings = Transaction::where('room_id', $transaction->room_id)
            ->where('status', 'Approved')
            ->where(function ($query) use ($transaction) {
                $query->whereBetween('check_in', [$transaction->check_in, $transaction->check_out])
                    ->orWhereBetween('check_out', [$transaction->check_in, $transaction->check_out]);
            })
            ->exists();

        if ($conflictingBookings) {
            return redirect()
                ->route('receptionist.reservations')
                ->with('error', 'Cannot approve reservation. Room is already booked for these dates.');
        }

        $transaction->update([
            'status' => 'Approved',
            'approved_at' => now()
        ]);
        
        // Update room status
        $transaction->room->update(['status' => 'Reserved']);
        
        // Redirect to payment processing
        return redirect()
            ->route('receptionist.process-payment', $transaction->id)
            ->with('success', 'Reservation approved. Please process the payment.');
    }

    public function rejectReservation(Transaction $transaction)
    {
        $transaction->update([
            'status' => 'Rejected',
            'rejected_at' => now()
        ]);
        
        return redirect()
            ->route('receptionist.reservations')
            ->with('success', 'Reservation has been rejected');
    }

    public function reservationDetails(Transaction $transaction)
    {
        $transaction->load(['customer.user', 'room.type', 'payment']);
        
        return view('receptionist.reservation-details', [
            'transaction' => $transaction
        ]);
    }

    public function checkInList()
    {
        $todayCheckins = Transaction::with(['customer.user', 'room.type'])
            ->whereDate('check_in', today())
            ->where('status', 'Approved')
            ->get();

        $upcomingCheckins = Transaction::with(['customer.user', 'room.type'])
            ->whereDate('check_in', '>', today())
            ->where('status', 'Approved')
            ->orderBy('check_in')
            ->take(5)
            ->get();

        return view('receptionist.check-in', compact('todayCheckins', 'upcomingCheckins'));
    }

    public function processCheckIn(Transaction $transaction)
    {
        try {
            // Verify the transaction is approved and due for check-in
            if ($transaction->status !== 'Approved') {
                return back()->with('error', 'Only approved reservations can be checked in.');
            }

            if ($transaction->check_in > today()) {
                return back()->with('error', 'This reservation is not due for check-in yet.');
            }

            // Update transaction status
            $transaction->update([
                'status' => 'Checked-in',
                'checked_in_at' => now()
            ]);

            // Update room status
            $transaction->room->update(['status' => 'Occupied']);

            return redirect()
                ->route('receptionist.check-in')
                ->with('success', 'Guest has been successfully checked in.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while processing check-in. Please try again.');
        }
    }
}
