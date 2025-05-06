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
        $transaction = Transaction::where([
            ['check_in', '<=', Carbon::now()],
            ['check_out', '>=', Carbon::now()],
            ['room_id', $room->id]
        ])->first();

        $customer = $transaction?->customer ?? [];

        return view('room.show', compact('customer', 'room'));
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
}