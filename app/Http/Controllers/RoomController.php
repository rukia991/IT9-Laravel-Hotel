<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Models\Room;
use App\Models\RoomStatus;
use App\Models\RoomType;
use App\Models\Transaction;
use App\Models\Type;
use App\Repositories\Interface\ImageRepositoryInterface;
use App\Repositories\Interface\RoomRepositoryInterface;
use App\Repositories\Interface\RoomStatusRepositoryInterface;
use App\Repositories\Interface\TypeRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;


class RoomController extends Controller
{
    public function __construct(
        private RoomRepositoryInterface $roomRepository,
        private TypeRepositoryInterface $typeRepository,
        private RoomStatusRepositoryInterface $roomStatusRepository
    ) {
    }

    /**
     * Display a listing of the rooms.
     */
    public function index()
    {
        $rooms = $this->roomRepository->paginateRooms(10);
        $roomStatuses = RoomStatus::all(); 
        $types = Type::all();
    
        $role = auth()->user()->role;
    
        if (strtolower($role) === 'super' || strtolower($role) === 'admin') {
            return view('room.index', compact('rooms', 'roomStatuses', 'types'));
        }
    
        return view('customer.index', compact('rooms'));
    }
    
    /**
     * Show the form for creating a new room.
     */
    public function create()
    {
        $types = Type::all();
        $roomStatuses = RoomStatus::all();

        $view = view('room.create', compact('types', 'roomStatuses'))->render();

        return response()->json(['view' => $view]);
    }

    /**
     * Store a newly created room in storage.
     */
    public function store(StoreRoomRequest $request)
    {
        $room = Room::create($request->validated());

        return response()->json(['message' => 'Room ' . $room->number . ' created']);
    }

    /**
     * Display the specified room.
     */
    public function show(Room $room)
    {
        // Load necessary relationships
        $room->load(['type', 'facilities', 'images']);

        // Get room availability for the next 30 days
        $today = now();
        $thirtyDaysFromNow = now()->addDays(30);
        
        $existingBookings = Transaction::where('room_id', $room->id)
            ->whereIn('status', ['Approved', 'Pending'])
            ->where('check_out', '>=', $today)
            ->where('check_in', '<=', $thirtyDaysFromNow)
            ->get(['check_in', 'check_out', 'status']);

        return view('customer.room_details', compact('room', 'existingBookings'));
    }

    /**
     * Show the form for editing the specified room.
     */
    public function edit(Room $room)
    {
        $types = Type::all();
        $roomStatuses = RoomStatus::all();

        $view = view('room.edit', compact('room', 'types', 'roomStatuses'))->render();

        return response()->json(['view' => $view]);
    }

    /**
     * Update the specified room in storage.
     */
    public function update(Room $room, StoreRoomRequest $request)
    {
        $room->update($request->validated());

        return response()->json(['message' => 'Room ' . $room->number . ' updated!']);
    }

    /**
     * Remove the specified room from storage.
     */
    public function destroy(Room $room, ImageRepositoryInterface $imageRepository)
    {
        try {
            $room->delete();

            $path = public_path('img/room/' . $room->number);

            if (is_dir($path)) {
                $imageRepository->destroy($path);
            }

            return response()->json(['message' => 'Room number ' . $room->number . ' deleted!']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Room ' . $room->number . ' cannot be deleted! Error Code: ' . $e->getCode(),
            ], 500);
        }
    }

    public function receptionistRooms()
    {
        // Get all rooms with their relationships
        $rooms = Room::with(['type', 'currentTransaction.customer', 'facilities'])
            ->orderBy('number')
            ->get();

        // Get current transactions to determine room status
        $currentTransactions = Transaction::where('status', 'Approved')
            ->where('check_in', '<=', now())
            ->where('check_out', '>', now())
            ->pluck('room_id');

        // Get pending transactions
        $pendingTransactions = Transaction::where('status', 'Pending')
            ->where('check_in', '>', now())
            ->pluck('room_id');

        // Calculate statistics
        $stats = [
            'totalRooms' => $rooms->count(),
            'availableRooms' => $rooms->whereNotIn('id', $currentTransactions)->whereNotIn('id', $pendingTransactions)->count(),
            'occupiedRooms' => $rooms->whereIn('id', $currentTransactions)->count(),
            'pendingRooms' => $rooms->whereIn('id', $pendingTransactions)->count(),
        ];

        // Add room status and additional info for each room
        foreach ($rooms as $room) {
            if ($room->currentTransaction) {
                $room->status = 'occupied';
                $room->guest_info = $room->currentTransaction->customer;
                $room->check_out_date = $room->currentTransaction->check_out;
            } elseif ($pendingTransactions->contains($room->id)) {
                $room->status = 'reserved';
                $room->guest_info = null;
                $room->check_out_date = null;
            } else {
                $room->status = 'available';
                $room->guest_info = null;
                $room->check_out_date = null;
            }
        }

        return view('receptionist.rooms', [
            'rooms' => $rooms,
            'stats' => $stats
        ]);
    }
}