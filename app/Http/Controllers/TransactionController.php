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
        $reservations = Transaction::with(['customer.user', 'room.type', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get counts for dashboard
        $pendingReservations = Transaction::where('status', 'Pending')->count();
        $todayCheckins = Transaction::whereDate('check_in', Carbon::today())
            ->where('status', 'Approved')
            ->count();

        return view('receptionist.reservations', [
            'reservations' => $reservations,
            'pendingReservations' => $pendingReservations,
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
        
        return redirect()
            ->route('receptionist.reservations')
            ->with('success', 'Reservation has been approved successfully');
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
        $todayCheckins = Transaction::with(['customer.user', 'room'])
            ->whereDate('check_in', today())
            ->where('status', 'Approved')
            ->get();

        $upcomingCheckins = Transaction::with(['customer.user', 'room'])
            ->whereDate('check_in', '>', today())
            ->where('status', 'Approved')
            ->orderBy('check_in')
            ->take(5)
            ->get();

        return view('receptionist.check-in', [
            'todayCheckins' => $todayCheckins,
            'upcomingCheckins' => $upcomingCheckins
        ]);
    }
}
