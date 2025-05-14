<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Transaction;
use App\Models\Customer;
use App\Models\RoomStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Debug incoming request data
            Log::info('Booking Request Data:', $request->all());

            // Validate the request
            $request->validate([
                'room_id' => 'required|exists:rooms,id',
                'check_in' => 'required|date|after_or_equal:today',
                'check_out' => 'required|date|after:check_in',
                'guests' => 'required|integer|min:1',
                'special_requests' => 'nullable|string|max:500'
            ]);

            // Get the room
            $room = Room::findOrFail($request->room_id);

            // Calculate total nights and price
            $checkIn = Carbon::parse($request->check_in);
            $checkOut = Carbon::parse($request->check_out);
            $nights = $checkIn->diffInDays($checkOut);
            $subtotal = $room->price * $nights;
            $taxes = $subtotal * 0.12;
            $totalPrice = $subtotal + $taxes;

            // Check if number of guests exceeds room capacity
            if ($request->guests > $room->capacity) {
                return back()
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
                return back()
                    ->with('error', 'Sorry, this room is not available for the selected dates.')
                    ->withInput();
            }

            // Get or create customer from the authenticated user
            $user = auth()->user();
            
            if (!$user) {
                return back()
                    ->with('error', 'You must be logged in to make a booking.')
                    ->withInput();
            }
            
            // Find existing customer or create a new one
            $customer = Customer::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $user->name,
                    'address' => $request->address ?? 'Please update your address',
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
                    'check_in' => $checkIn->startOfDay(),
                    'check_out' => $checkOut->startOfDay(),
                    'status' => 'Pending',
                    'number_of_guests' => $request->guests,
                    'special_requests' => $request->special_requests,
                    'total_price' => $totalPrice
                ]);

                // Get the 'Expected Arrival' status
                $expectedArrivalStatus = RoomStatus::where('name', 'Expected Arrival')
                    ->orWhere('name', 'Booked')
                    ->first();
                
                if (!$expectedArrivalStatus) {
                    throw new \Exception('Required room status not found');
                }

                // Update room status
                $room->update([
                    'status' => 'booked',
                    'room_status_id' => $expectedArrivalStatus->id
                ]);

                DB::commit();

                // Debug log
                Log::info('Booking successful', [
                    'transaction_id' => $transaction->id,
                    'user_id' => $user->id,
                    'room_id' => $request->room_id
                ]);

                // Redirect with absolute URL
                return redirect()->to('/IT9-Laravel-Hotel/customer/reservations')
                    ->with('success', 'Room booking request submitted successfully! Please wait for confirmation.');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Booking creation failed: ' . $e->getMessage());
                Log::error($e->getTraceAsString());
                
                return back()
                    ->with('error', 'An error occurred while processing your booking. Please try again.')
                    ->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Booking validation failed: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return back()
                ->with('error', 'Invalid booking data provided. Please check your input and try again.')
                ->withInput();
        }
    }
} 