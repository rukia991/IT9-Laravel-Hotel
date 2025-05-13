<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Transaction;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'room_id' => 'required|exists:rooms,id',
                'check_in' => 'required|date|after_or_equal:today',
                'check_out' => 'required|date|after:check_in',
                'guests' => 'required|integer|min:1|max:4',
                'special_requests' => 'nullable|string|max:500'
            ]);

            // Get the room
            $room = Room::findOrFail($request->room_id);

            // Check if number of guests exceeds room capacity
            if ($request->guests > $room->capacity) {
                return redirect()->back()
                    ->with('error', 'Number of guests exceeds room capacity.')
                    ->withInput();
            }

            // Check if room is available for the selected dates
            $isRoomAvailable = !Transaction::where('room_id', $request->room_id)
                ->whereIn('status', ['Approved', 'Pending'])
                ->where(function ($query) use ($request) {
                    $query->whereBetween('check_in', [$request->check_in, $request->check_out])
                        ->orWhereBetween('check_out', [$request->check_in, $request->check_out])
                        ->orWhere(function ($q) use ($request) {
                            $q->where('check_in', '<=', $request->check_in)
                              ->where('check_out', '>=', $request->check_out);
                        });
                })
                ->exists();

            if (!$isRoomAvailable) {
                return redirect()->back()
                    ->with('error', 'Sorry, this room is not available for the selected dates.')
                    ->withInput();
            }

            // Get or create customer from the authenticated user
            $user = auth()->user();
            
            if (!$user) {
                return redirect()->back()
                    ->with('error', 'You must be logged in to make a booking.')
                    ->withInput();
            }
            
            // Find existing customer or create a new one
            $customer = Customer::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $user->name,
                    'address' => $request->address ?? 'Please update your address',
                    'gender' => $request->gender ?? 'Not specified',
                    'job' => $request->job ?? 'Not specified',
                    'birthdate' => $request->birthdate ?? now()->subYears(18),
                ]
            );

            DB::beginTransaction();
            try {
                // Create a new transaction/booking
                $transaction = Transaction::create([
                    'user_id' => $user->id,
                    'customer_id' => $customer->id,
                    'room_id' => $request->room_id,
                    'check_in' => Carbon::parse($request->check_in)->startOfDay(),
                    'check_out' => Carbon::parse($request->check_out)->startOfDay(),
                    'status' => 'Pending',
                    'number_of_guests' => $request->guests,
                    'special_requests' => $request->special_requests
                ]);

                // Get the 'Expected Arrival' status ID
                $expectedArrivalStatus = \App\Models\RoomStatus::where('name', 'Expected Arrival')->first();
                
                if (!$expectedArrivalStatus) {
                    // If Expected Arrival status doesn't exist, use Vacant status
                    $expectedArrivalStatus = \App\Models\RoomStatus::where('name', 'Vacant')->first();
                    
                    if (!$expectedArrivalStatus) {
                        \Log::error('Required room statuses not found. Neither "Expected Arrival" nor "Vacant" status exists in the database.');
                        throw new \Exception('Required room status not found. Please ensure room statuses are properly configured.');
                    }
                    
                    \Log::warning('Using "Vacant" status as fallback for "Expected Arrival"');
                }

                // Update room status
                $room->room_status_id = $expectedArrivalStatus->id;
                $room->save();

                DB::commit();

                return redirect()->route('customer.reservations')
                    ->with('success', 'Room booking request submitted successfully! Please wait for confirmation from our staff.');
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Booking creation failed: ' . $e->getMessage());
                \Log::error('Stack trace: ' . $e->getTraceAsString());
                
                $errorMessage = 'An error occurred while processing your booking.';
                
                // Provide more specific error messages based on the exception
                if ($e->getMessage() === 'Required room status not found') {
                    $errorMessage = 'System configuration error: Room status not properly set up. Please contact support.';
                } elseif ($e instanceof \Illuminate\Database\QueryException) {
                    $errorMessage = 'Database error occurred. Please try again later.';
                }
                
                return redirect()->back()
                    ->with('error', $errorMessage)
                    ->withInput();
            }
        } catch (\Exception $e) {
            \Log::error('Booking validation failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Invalid booking data provided. Please check your input and try again.')
                ->withInput();
        }
    }
} 