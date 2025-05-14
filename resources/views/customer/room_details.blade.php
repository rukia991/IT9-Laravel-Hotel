@extends('template.master')
@section('title', 'Room Details')

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('customer.index') }}">Rooms</a></li>
            <li class="breadcrumb-item active">{{ $room->type->name }} - Room {{ $room->number }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Left Column - Room Images and Details -->
        <div class="col-lg-8">
            <!-- Room Images Gallery -->
            <div id="roomGallery" class="carousel slide mb-4" data-bs-ride="carousel">
                <div class="carousel-inner rounded shadow">
                    @if($room->images->count() > 0)
                        @foreach($room->images as $index => $image)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <img src="{{ asset('storage/images/' . $image->path) }}"
                                     class="d-block w-100"
                                     alt="Room Image"
                                     style="height: 400px; object-fit: cover;">
                            </div>
                        @endforeach
                    @else
                        <div class="carousel-item active">
                            <img src="https://placehold.co/800x400?text=No+Image+Available"
                                 class="d-block w-100"
                                 alt="No Image Available"
                                 style="height: 400px; object-fit: cover;">
                        </div>
                    @endif
                </div>
                @if($room->images->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#roomGallery" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#roomGallery" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                @endif
            </div>

            <!-- Room Details -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="card-title">{{ $room->type->name }} - Room {{ $room->number }}</h2>
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-{{ $room->status === 'available' ? 'success' : 'danger' }} me-2">
                            {{ ucfirst($room->status) }}
                        </span>
                        <span class="text-muted">• Max Occupancy: {{ $room->capacity }} persons</span>
                    </div>
                    
                    <h4 class="mt-4">Room Features</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li><i class="fas fa-users text-primary"></i> Capacity: {{ $room->capacity }} persons</li>
                                <li><i class="fas fa-eye text-primary"></i> View: {{ $room->view }}</li>
                                @foreach($room->facilities->take(3) as $facility)
                                    <li><i class="fas fa-check text-success"></i> {{ $facility->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                @foreach($room->facilities->skip(3) as $facility)
                                    <li><i class="fas fa-check text-success"></i> {{ $facility->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Room Policies -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title">Room Policies</h4>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-clock text-info"></i> Check-in: 2:00 PM</li>
                        <li><i class="fas fa-clock text-info"></i> Check-out: 12:00 PM</li>
                        <li><i class="fas fa-smoking-ban text-danger"></i> No smoking</li>
                        <li><i class="fas fa-paw text-warning"></i> No pets allowed</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Right Column - Booking Form -->
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-body">
                    <h3 class="text-primary mb-3">
                        ₱{{ number_format($room->price, 2) }}
                        <small class="text-muted">/night</small>
                    </h3>

                    @guest
                        <div class="alert alert-info">
                            Please <a href="{{ route('login') }}">login</a> or <a href="{{ route('register') }}">register</a> to book this room.
                        </div>
                    @else
                        <form action="{{ route('booking.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="room_id" value="{{ $room->id }}">

                            <!-- Check-in Date -->
                            <div class="mb-3">
                                <label for="check_in" class="form-label">Check-in Date</label>
                                <input type="date" 
                                       class="form-control @error('check_in') is-invalid @enderror"
                                       id="check_in"
                                       name="check_in"
                                       min="{{ date('Y-m-d') }}"
                                       value="{{ old('check_in') }}"
                                       required>
                                @error('check_in')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Check-out Date -->
                            <div class="mb-3">
                                <label for="check_out" class="form-label">Check-out Date</label>
                                <input type="date"
                                       class="form-control @error('check_out') is-invalid @enderror"
                                       id="check_out"
                                       name="check_out"
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                       value="{{ old('check_out') }}"
                                       required>
                                @error('check_out')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Number of Guests -->
                            <div class="mb-3">
                                <label for="guests" class="form-label">Number of Guests</label>
                                <select class="form-select @error('guests') is-invalid @enderror"
                                        id="guests"
                                        name="guests"
                                        required>
                                    @for($i = 1; $i <= $room->capacity; $i++)
                                        <option value="{{ $i }}" {{ old('guests') == $i ? 'selected' : '' }}>
                                            {{ $i }} {{ $i == 1 ? 'Guest' : 'Guests' }}
                                        </option>
                                    @endfor
                                </select>
                                @error('guests')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Special Requests -->
                            <div class="mb-3">
                                <label for="special_requests" class="form-label">Special Requests (Optional)</label>
                                <textarea class="form-control @error('special_requests') is-invalid @enderror"
                                          id="special_requests"
                                          name="special_requests"
                                          rows="3"
                                          placeholder="Any special requests or preferences?">{{ old('special_requests') }}</textarea>
                                @error('special_requests')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Price Calculation -->
                            <div id="price-calculation" class="alert alert-info mb-3" style="display: none;">
                                <p class="mb-1">Stay Duration: <span id="total-nights">0</span> nights</p>
                                <p class="mb-0">Total Price: ₱<span id="total-price">0</span></p>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                Book Now
                            </button>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    
    // Update minimum check-out date when check-in date changes
    checkInInput.addEventListener('change', function() {
        const checkInDate = new Date(this.value);
        const minCheckOutDate = new Date(checkInDate);
        minCheckOutDate.setDate(minCheckOutDate.getDate() + 1);
        
        checkOutInput.min = minCheckOutDate.toISOString().split('T')[0];
        
        if (checkOutInput.value && new Date(checkOutInput.value) <= checkInDate) {
            checkOutInput.value = minCheckOutDate.toISOString().split('T')[0];
        }
        
        updatePriceCalculation();
    });
    
    // Update price calculation when check-out date changes
    checkOutInput.addEventListener('change', updatePriceCalculation);
    
    function updatePriceCalculation() {
        const checkIn = new Date(checkInInput.value);
        const checkOut = new Date(checkOutInput.value);
        
        if (checkIn && checkOut && checkOut > checkIn) {
            const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
            const pricePerNight = {{ $room->price }};
            const totalPrice = nights * pricePerNight;
            
            document.getElementById('total-nights').textContent = nights;
            document.getElementById('total-price').textContent = totalPrice.toLocaleString();
            document.getElementById('price-calculation').style.display = 'block';
        } else {
            document.getElementById('price-calculation').style.display = 'none';
        }
    }
});
</script>
@endpush

@push('styles')
<style>
.sticky-top {
    z-index: 1020;
}

.room-features li {
    margin-bottom: 10px;
}

.room-features i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

.carousel-inner {
    border-radius: 0.5rem;
    overflow: hidden;
}

.carousel-item img {
    width: 100%;
    height: 400px;
    object-fit: cover;
}

.badge {
    padding: 0.5em 1em;
    font-size: 0.9em;
}
</style>
@endpush
@endsection 