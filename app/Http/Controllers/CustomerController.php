<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use App\Models\User;
use App\Models\Room;
use App\Models\Transaction;
use App\Models\LoyaltyPoint;
use App\Models\SpecialOffer;
use App\Repositories\Interface\CustomerRepositoryInterface;
use App\Repositories\Interface\ImageRepositoryInterface;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['dashboard', 'reservedRooms', 'profile']);
    }

    public function index()
    {
        $rooms = Room::with(['type', 'images'])
            ->where('status', 'available')
            ->paginate(9);
        
        return view('customer.index', compact('rooms'));
    }

    public function dashboard()
    {
        $user = auth()->user();
        $customer = Customer::where('user_id', $user->id)->firstOrFail();

        // Get current reservations
        $currentReservations = Transaction::with(['room.type', 'room.images'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['Pending', 'Approved'])
            ->where('check_out', '>=', now())
            ->orderBy('check_in')
            ->get();

        // Get past stays
        $pastStays = Transaction::with(['room.type'])
            ->where('user_id', $user->id)
            ->where('status', 'Completed')
            ->orderBy('check_out', 'desc')
            ->get();

        // Calculate loyalty points and member status
        $loyaltyPoints = $this->calculateLoyaltyPoints($user->id);
        $memberStatus = $this->determineMemberStatus($loyaltyPoints);
        $totalStays = $pastStays->count();

        // Get special offers
        $specialOffers = SpecialOffer::where('valid_until', '>', now())
            ->where(function($query) use ($memberStatus) {
                $query->where('member_status', '<=', $memberStatus)
                      ->orWhereNull('member_status');
            })
            ->orderBy('discount', 'desc')
            ->get();

        return view('customer.dashboard', compact(
            'currentReservations',
            'pastStays',
            'loyaltyPoints',
            'memberStatus',
            'totalStays',
            'specialOffers'
        ));
    }

    private function calculateLoyaltyPoints($userId)
    {
        // Get all completed stays
        $completedStays = Transaction::where('user_id', $userId)
            ->where('status', 'Completed')
            ->get();

        $totalPoints = 0;

        foreach ($completedStays as $stay) {
            // Calculate nights
            $nights = $stay->check_in->diffInDays($stay->check_out);
            
            // Base points (10 points per night)
            $basePoints = $nights * 10;
            
            // Bonus points for room type
            $roomTypeBonus = match($stay->room->type->name) {
                'Deluxe' => 5,
                'Suite' => 10,
                'Presidential Suite' => 20,
                default => 0
            };

            // Calculate total points for this stay
            $stayPoints = $basePoints + ($nights * $roomTypeBonus);
            
            $totalPoints += $stayPoints;
        }

        return $totalPoints;
    }

    private function determineMemberStatus($points)
    {
        return match(true) {
            $points >= 1000 => 'Platinum',
            $points >= 500 => 'Gold',
            $points >= 200 => 'Silver',
            default => 'Regular'
        };
    }
    
    public function create()
    {
        return view('customer.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        $customer = $this->customerRepository->store($request);
        return redirect('customer')->with('success', 'Customer ' . $customer->name . ' created');
    }

    public function show(Customer $customer)
    {
        return view('customer.show', ['customer' => $customer]);
    }

    public function edit(Customer $customer)
    {
        return view('customer.edit', ['customer' => $customer]);
    }

    public function update(Customer $customer, StoreCustomerRequest $request)
    {
        $customer->update($request->all());
        return redirect('customer')->with('success', 'customer ' . $customer->name . ' updated!');
    }

    public function destroy(Customer $customer, ImageRepositoryInterface $imageRepository)
    {
        try {
            $user = User::find($customer->user->id);
            $avatar_path = public_path('img/user/' . $user->name . '-' . $user->id);

            $customer->delete();
            $user->delete();

            if (is_dir($avatar_path)) {
                $imageRepository->destroy($avatar_path);
            }

            return redirect('customer')->with('success', 'Customer ' . $customer->name . ' deleted!');
        } catch (\Exception $e) {
            $errorMessage = '';
            if ($e->errorInfo[0] == '23000') {
                $errorMessage = 'Data still connected to other tables';
            }
            return redirect('customer')->with('failed', 'Customer ' . $customer->name . ' cannot be deleted! ' . $errorMessage);
        }
    }

    public function reservedRooms()
    {
        try {
            $user = auth()->user();
            Log::info('Loading reservations for user', ['user_id' => $user->id]);

            $transactions = Transaction::with(['room.type', 'room.images'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(9);

            Log::info('Found transactions', [
                'count' => $transactions->count(),
                'total' => $transactions->total()
            ]);

            return view('customer.reservations', compact('transactions'));

        } catch (\Exception $e) {
            Log::error('Error in reservedRooms: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return back()->with('error', 'Unable to load reservations. Please try again.');
        }
    }

    public function profile()
    {
        $user = auth()->user();
        $customer = Customer::where('user_id', $user->id)->firstOrFail();
        $loyaltyPoints = $this->calculateLoyaltyPoints($user->id);
        $memberStatus = $this->determineMemberStatus($loyaltyPoints);

        return view('customer.profile', compact('customer', 'loyaltyPoints', 'memberStatus'));
    }

    public function receptionistDashboard()
    {
        $pendingReservationsList = \App\Models\Transaction::with(['customer', 'room'])
            ->where('status', 'Pending')
            ->orderBy('check_in')
            ->take(5)
            ->get();

        $pendingReservations = \App\Models\Transaction::where('status', 'Pending')->count();

        // Get all room IDs that are currently reserved (pending or approved) for today or in the future
        $reservedRoomIds = \App\Models\Transaction::whereIn('status', ['Pending', 'Approved'])
            ->where('check_out', '>=', Carbon::today())
            ->pluck('room_id')
            ->unique();

        // Occupied rooms
        $occupiedRoomIds = \App\Models\Transaction::where('status', 'Approved')
            ->where('check_in', '<=', Carbon::today())
            ->where('check_out', '>', Carbon::today())
            ->pluck('room_id')
            ->unique();

        $occupiedRoomsList = \App\Models\Room::with('type')
            ->whereIn('id', $occupiedRoomIds)
            ->get();

        // Available rooms
        $availableRoomsList = \App\Models\Room::with('type')
            ->whereNotIn('id', $reservedRoomIds)
            ->get();

        $todayCheckins = \App\Models\Transaction::whereDate('check_in', today())
            ->where('status', 'Approved')
            ->count();
        $roomsList = \App\Models\Room::with('type')->get();

        return view('receptionist.index', compact(
            'pendingReservations',
            'pendingReservationsList',
            'occupiedRoomsList',
            'availableRoomsList',
            'todayCheckins',
            'roomsList'
        ));
    }
}
