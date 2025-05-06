<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class CustomerRoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('images')->paginate(6);
        return view('customer.index', compact('rooms'));
    }
}
