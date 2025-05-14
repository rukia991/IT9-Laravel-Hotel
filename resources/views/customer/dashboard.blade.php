@extends('template.master')
@section('title', 'My Dashboard')

@section('content')
<div class="container mt-4">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Welcome back, {{ auth()->user()->name }}!</h2>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="loyalty-points text-center p-3 border rounded">
                                <i class="fas fa-star text-warning mb-2" style="font-size: 2em;"></i>
                                <h4>Loyalty Points</h4>
                                <h3 class="text-primary">{{ $loyaltyPoints ?? 0 }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="member-status text-center p-3 border rounded">
                                <i class="fas fa-crown text-warning mb-2" style="font-size: 2em;"></i>
                                <h4>Member Status</h4>
                                <h3 class="text-success">{{ $memberStatus ?? 'Regular' }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="total-stays text-center p-3 border rounded">
                                <i class="fas fa-bed text-info mb-2" style="font-size: 2em;"></i>
                                <h4>Total Stays</h4>
                                <h3>{{ $totalStays ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Reservations -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Current Reservations</h3>
                </div>
                <div class="card-body">
                    @if($currentReservations && $currentReservations->count() > 0)
                        @foreach($currentReservations as $reservation)
                            <div class="reservation-card mb-3 p-3 border rounded">
                                <div class="row">
                                    <div class="col-md-3">
                                        @if($reservation->room->images->first())
                                            <img src="{{ asset('storage/images/' . $reservation->room->images->first()->path) }}"
                                                 class="img-fluid rounded"
                                                 alt="Room Image">
                                        @else
                                            <img src="https://placehold.co/300x200?text=No+Image"
                                                 class="img-fluid rounded"
                                                 alt="No Image">
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <h4>{{ $reservation->room->number }} - {{ $reservation->room->type->name }}</h4>
                                        <p><strong>Check-in:</strong> {{ $reservation->check_in->format('M d, Y') }}</p>
                                        <p><strong>Check-out:</strong> {{ $reservation->check_out->format('M d, Y') }}</p>
                                        <p><strong>Guests:</strong> {{ $reservation->number_of_guests }}</p>
                                        <p><strong>Status:</strong> 
                                            <span class="badge bg-{{ $reservation->status == 'Approved' ? 'success' : 
                                                                    ($reservation->status == 'Pending' ? 'warning' : 'danger') }}">
                                                {{ $reservation->status }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <h4 class="text-primary">₱{{ number_format($reservation->room->price, 2) }}</h4>
                                        <p class="text-muted">per night</p>
                                        @if($reservation->status == 'Approved')
                                            <a href="#" class="btn btn-outline-primary mb-2">View Details</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-bed text-muted mb-3" style="font-size: 3em;"></i>
                            <h4>No Current Reservations</h4>
                            <p>Looking for a comfortable stay? Check out our available rooms!</p>
                            <a href="{{ route('customer.index') }}" class="btn btn-primary">Browse Rooms</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Special Offers -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title mb-0">Special Offers</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(isset($specialOffers) && count($specialOffers) > 0)
                            @foreach($specialOffers as $offer)
                                <div class="col-md-4 mb-3">
                                    <div class="offer-card p-3 border rounded">
                                        <div class="ribbon ribbon-top-right"><span>{{ $offer->discount }}% OFF</span></div>
                                        <h4>{{ $offer->title }}</h4>
                                        <p>{{ $offer->description }}</p>
                                        <p class="text-muted">Valid until: {{ $offer->valid_until->format('M d, Y') }}</p>
                                        <a href="#" class="btn btn-outline-success">View Offer</a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12 text-center py-4">
                                <i class="fas fa-gift text-muted mb-3" style="font-size: 3em;"></i>
                                <h4>No Special Offers Available</h4>
                                <p>Check back later for exciting deals!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Past Stays -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h3 class="card-title mb-0">Past Stays</h3>
                </div>
                <div class="card-body">
                    @if(isset($pastStays) && $pastStays->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Room</th>
                                        <th>Dates</th>
                                        <th>Nights</th>
                                        <th>Total Paid</th>
                                        <th>Points Earned</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pastStays as $stay)
                                        <tr>
                                            <td>{{ $stay->room->number }} - {{ $stay->room->type->name }}</td>
                                            <td>{{ $stay->check_in->format('M d, Y') }} - {{ $stay->check_out->format('M d, Y') }}</td>
                                            <td>{{ $stay->check_in->diffInDays($stay->check_out) }}</td>
                                            <td>₱{{ number_format($stay->total_paid, 2) }}</td>
                                            <td>{{ $stay->points_earned }}</td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-outline-primary">View Details</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-history text-muted mb-3" style="font-size: 3em;"></i>
                            <h4>No Past Stays</h4>
                            <p>Your stay history will appear here after your first check-out.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.loyalty-points, .member-status, .total-stays {
    transition: transform 0.2s;
}

.loyalty-points:hover, .member-status:hover, .total-stays:hover {
    transform: translateY(-5px);
}

.reservation-card {
    transition: box-shadow 0.3s;
}

.reservation-card:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.offer-card {
    position: relative;
    overflow: hidden;
}

.ribbon {
    position: absolute;
    right: -5px;
    top: -5px;
    z-index: 1;
    overflow: hidden;
    width: 75px;
    height: 75px;
    text-align: right;
}

.ribbon span {
    font-size: 10px;
    font-weight: bold;
    color: #FFF;
    text-transform: uppercase;
    text-align: center;
    line-height: 20px;
    transform: rotate(45deg);
    -webkit-transform: rotate(45deg);
    width: 100px;
    display: block;
    background: #79A70A;
    background: linear-gradient(#9BC90D 0%, #79A70A 100%);
    box-shadow: 0 3px 10px -5px rgba(0, 0, 0, 1);
    position: absolute;
    top: 19px;
    right: -21px;
}

.ribbon span::before {
    content: "";
    position: absolute;
    left: 0px;
    top: 100%;
    z-index: -1;
    border-left: 3px solid #79A70A;
    border-right: 3px solid transparent;
    border-bottom: 3px solid transparent;
    border-top: 3px solid #79A70A;
}

.ribbon span::after {
    content: "";
    position: absolute;
    right: 0px;
    top: 100%;
    z-index: -1;
    border-left: 3px solid transparent;
    border-right: 3px solid #79A70A;
    border-bottom: 3px solid transparent;
    border-top: 3px solid #79A70A;
}
</style>
@endpush
@endsection 