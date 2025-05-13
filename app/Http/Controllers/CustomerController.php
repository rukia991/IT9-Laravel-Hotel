<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use App\Models\User;
use App\Repositories\Interface\CustomerRepositoryInterface;
use App\Repositories\Interface\ImageRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Transaction;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['reservedRooms']);
    }

    public function index()
    {
        $rooms = Room::where('status', 'available')->get();
        return view('room.index', compact('rooms'));
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

        return redirect('customer')->with('success', 'customer ' . $customer->name . ' udpated!');
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
        $user = auth()->user();
        $transactions = Transaction::with('room')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        return view('customer.reserved_rooms', compact('transactions'));
    }
}
