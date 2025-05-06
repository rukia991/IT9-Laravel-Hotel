<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\RoomRepositoryInterface;
use App\Models\Type;
use App\Models\RoomStatus;

class AdminRoomController extends Controller
{
    public function __construct(
        private RoomRepositoryInterface $roomRepository
    ) {
        $this->middleware(['auth', 'isAdmin']); // Protect admin routes
    }

    /**
     * Display a listing of the rooms.
     */
    public function index(Request $request)
    {
        $rooms = $this->roomRepository->paginateRooms(6); // Custom paginated query
        $types = Type::all(); // For filter dropdown
        $roomStatuses = RoomStatus::all(); // For filter dropdown

        return view('admin.room.index', compact('rooms', 'types', 'roomStatuses'));
    }

    /**
     * Show the form for creating a new room.
     */
    public function create()
    {
        $types = Type::all();
        $statuses = RoomStatus::all();

        return view('admin.room.create', compact('types', 'statuses'));
    }

    /**
     * Store a newly created room in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|unique:rooms',
            'type_id' => 'required|exists:types,id',
            'capacity' => 'required|integer|min:1',
            'price_per_day' => 'required|numeric|min:0',
            'status_id' => 'required|exists:room_statuses,id',
        ]);

        $this->roomRepository->create($validated);

        return redirect()->route('admin.rooms.index')->with('success', 'Room created successfully.');
    }

    /**
     * Show the form for editing the specified room.
     */
    public function edit($id)
    {
        $room = $this->roomRepository->findById($id);
        $types = Type::all();
        $statuses = RoomStatus::all();

        return view('admin.room.edit', compact('room', 'types', 'statuses'));
    }

    /**
     * Update the specified room in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'number' => 'required|unique:rooms,number,' . $id,
            'type_id' => 'required|exists:types,id',
            'capacity' => 'required|integer|min:1',
            'price_per_day' => 'required|numeric|min:0',
            'status_id' => 'required|exists:room_statuses,id',
        ]);

        $this->roomRepository->update($id, $validated);

        return redirect()->route('admin.rooms.index')->with('success', 'Room updated successfully.');
    }

    /**
     * Remove the specified room from storage.
     */
    public function destroy($id)
    {
        $this->roomRepository->delete($id);

        return redirect()->route('admin.rooms.index')->with('success', 'Room deleted successfully.');
    }
}
