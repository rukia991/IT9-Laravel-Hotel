@extends('template.master')
@section('title', 'Room Details')
@section('content')

<style>
    .room-gallery img {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 8px;
    }
    
    .thumbnail-gallery {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }
    
    .thumbnail-gallery img {
        width: 100px;
        height: 70px;
        object-fit: cover;
        border-radius: 4px;
        cursor: pointer;
        transition: opacity 0.3s;
    }
    
    .thumbnail-gallery img:hover {
        opacity: 0.8;
    }
    
    .amenity-icon {
        font-size: 1.5rem;
        margin-right: 10px;
    }
    
    .booking-card {
        position: sticky;
        top: 20px;
    }
    
    .room-features li {
        margin-bottom: 10px;
    }
    
    .price-tag {
        font-size: 1.8rem;
        color: #2c3e50;
    }
    
    .availability-badge {
        font-size: 0.9rem;
        padding: 5px 10px;
        border-radius: 15px;
    }
</style>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('customer.index') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $room->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Left Column - Room Details -->
        <div class="col-lg-8">
            <!-- Main Image Gallery -->
            <div class="room-gallery">
                <img src="{{ $room->image }}" 
                     onerror="this.onerror=null; this.src='https://placehold.co/800x400?text=No+Image';"
                     alt="{{ $room->name }}" 
                     class="main-image shadow">
                
                <div class="thumbnail-gallery">
                    <!-- Add more room images here -->
                    <img src="https://picsum.photos/id/1018/100/70" alt="Room View 1">
                    <img src="https://picsum.photos/id/1019/100/70" alt="Room View 2">
                    <img src="https://picsum.photos/id/1020/100/70" alt="Room View 3">
                    <img src="https://picsum.photos/id/1021/100/70" alt="Room View 4">
                </div>
            </div>

            <!-- Room Information -->
            <div class="mt-4">
                <h1 class="display-5">{{ $room->name }}</h1>
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-success availability-badge me-2">Available</span>
                    <span class="text-muted">• Max Occupancy: 2 Adults, 1 Child</span>
                </div>
                
                <p class="lead">{{ $room->description }}</p>
                
                <!-- Room Features -->
                <h3 class="mt-4">Room Features</h3>
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-unstyled room-features">
                            <li><i class="fas fa-wifi amenity-icon"></i> Free High-Speed WiFi</li>
                            <li><i class="fas fa-tv amenity-icon"></i> 50" Smart TV</li>
                            <li><i class="fas fa-snowflake amenity-icon"></i> Air Conditioning</li>
                            <li><i class="fas fa-coffee amenity-icon"></i> Coffee Maker</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled room-features">
                            <li><i class="fas fa-bath amenity-icon"></i> Private Bathroom</li>
                            <li><i class="fas fa-door-closed amenity-icon"></i> In-Room Safe</li>
                            <li><i class="fas fa-concierge-bell amenity-icon"></i> Room Service</li>
                            <li><i class="fas fa-bed amenity-icon"></i> King Size Bed</li>
                        </ul>
                    </div>
                </div>

                <!-- Room Policies -->
                <h3 class="mt-4">Room Policies</h3>
                <div class="card">
                    <div class="card-body">
                        <ul class="mb-0">
                            <li>Check-in: 2:00 PM</li>
                            <li>Check-out: 12:00 PM</li>
                            <li>No smoking</li>
                            <li>Pets not allowed</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Booking Card -->
        <div class="col-lg-4">
            <div class="card booking-card shadow">
                <div class="card-body">
                    <h3 class="price-tag mb-3">₱{{ number_format($room->price, 2) }}<small class="text-muted"> / night</small></h3>
                    
                    @guest
                        <div class="alert alert-info">
                            Please <a href="{{ route('login') }}">login</a> or <a href="{{ route('register') }}">register</a> to book this room.
                        </div>
                    @endguest

                    @auth
                        <form id="bookingForm" action="{{ route('booking.store') }}" method="POST">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show mb-3">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show mb-3">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show mb-3">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @csrf
                            <input type="hidden" name="room_id" value="{{ $room->id }}">
                            
                            <div class="mb-3">
                                <label for="check_in" class="form-label">Check-in Date</label>
                                <input type="date" class="form-control @error('check_in') is-invalid @enderror" 
                                       id="check_in" name="check_in" required min="{{ date('Y-m-d') }}"
                                       value="{{ old('check_in') }}">
                                @error('check_in')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="check_out" class="form-label">Check-out Date</label>
                                <input type="date" class="form-control @error('check_out') is-invalid @enderror" 
                                       id="check_out" name="check_out" required
                                       value="{{ old('check_out') }}">
                                @error('check_out')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="guests" class="form-label">Number of Guests</label>
                                <select class="form-select @error('guests') is-invalid @enderror" 
                                        id="guests" name="guests" required>
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

                            <div class="mb-3">
                                <label for="special_requests" class="form-label">Special Requests</label>
                                <textarea class="form-control @error('special_requests') is-invalid @enderror" 
                                          id="special_requests" name="special_requests" 
                                          rows="3" placeholder="Any special requests?">{{ old('special_requests') }}</textarea>
                                @error('special_requests')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Price Summary -->
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h5>Price Summary</h5>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Room Rate</span>
                                        <span id="roomRate">₱{{ number_format($room->price, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Number of Nights</span>
                                        <span id="numberOfNights">0</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal</span>
                                        <span id="subtotal">₱0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Taxes & Fees (12%)</span>
                                        <span id="taxes">₱0.00</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between fw-bold">
                                        <span>Total</span>
                                        <span id="total">₱0.00</span>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100" id="bookNowBtn">Book Now</button>
                        </form>
                    @endauth

                    <p class="text-muted small mt-3 mb-0 text-center">
                        <i class="fas fa-lock"></i> Secure booking - confirmation is immediate
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('bookingForm');
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    const bookNowBtn = document.getElementById('bookNowBtn');
    const roomPrice = {{ $room->price }};

    // Set minimum date for check-in to today
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const todayStr = today.toISOString().split('T')[0];
    checkInInput.min = todayStr;

    // Set minimum date for check-out based on check-in
    function updateCheckOutMin() {
        const checkInDate = new Date(checkInInput.value);
        if (!isNaN(checkInDate.getTime())) {
            const minCheckOutDate = new Date(checkInDate);
            minCheckOutDate.setDate(minCheckOutDate.getDate() + 1);
            checkOutInput.min = minCheckOutDate.toISOString().split('T')[0];
            
            // If check-out date is before new minimum, update it
            const checkOutDate = new Date(checkOutInput.value);
            if (!isNaN(checkOutDate.getTime()) && checkOutDate <= checkInDate) {
                checkOutInput.value = minCheckOutDate.toISOString().split('T')[0];
            }
        }
        updatePriceSummary();
        validateDates();
    }

    checkInInput.addEventListener('change', updateCheckOutMin);
    checkOutInput.addEventListener('change', function() {
        updatePriceSummary();
        validateDates();
    });

    function validateDates() {
        const checkIn = new Date(checkInInput.value);
        const checkOut = new Date(checkOutInput.value);
        
        if (isNaN(checkIn.getTime()) || isNaN(checkOut.getTime())) {
            bookNowBtn.disabled = true;
            return;
        }

        checkIn.setHours(0, 0, 0, 0);
        checkOut.setHours(0, 0, 0, 0);
        const isValid = checkIn >= today && checkOut > checkIn;
        
        bookNowBtn.disabled = !isValid;
        
        if (!isValid && checkInInput.value && checkOutInput.value) {
            if (checkIn < today) {
                checkInInput.setCustomValidity('Check-in date cannot be in the past');
                checkInInput.reportValidity();
            } else {
                checkOutInput.setCustomValidity('Check-out date must be after check-in date');
                checkOutInput.reportValidity();
            }
        } else {
            checkInInput.setCustomValidity('');
            checkOutInput.setCustomValidity('');
        }
    }

    function updatePriceSummary() {
        const checkIn = new Date(checkInInput.value);
        const checkOut = new Date(checkOutInput.value);
        
        if (!isNaN(checkIn.getTime()) && !isNaN(checkOut.getTime())) {
            checkIn.setHours(0, 0, 0, 0);
            checkOut.setHours(0, 0, 0, 0);
            const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
            
            if (nights > 0) {
                const subtotal = roomPrice * nights;
                const taxes = subtotal * 0.12;
                const total = subtotal + taxes;

                document.getElementById('numberOfNights').textContent = nights;
                document.getElementById('subtotal').textContent = '₱' + subtotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                document.getElementById('taxes').textContent = '₱' + taxes.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                document.getElementById('total').textContent = '₱' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            } else {
                resetPriceSummary();
            }
        } else {
            resetPriceSummary();
        }
    }

    function resetPriceSummary() {
        document.getElementById('numberOfNights').textContent = '0';
        document.getElementById('subtotal').textContent = '₱0.00';
        document.getElementById('taxes').textContent = '₱0.00';
        document.getElementById('total').textContent = '₱0.00';
    }

    // Form validation
    form.addEventListener('submit', function(e) {
        if (!checkInInput.value || !checkOutInput.value) {
            e.preventDefault();
            alert('Please select both check-in and check-out dates');
            return;
        }

        const checkIn = new Date(checkInInput.value);
        const checkOut = new Date(checkOutInput.value);
        
        if (isNaN(checkIn.getTime()) || isNaN(checkOut.getTime())) {
            e.preventDefault();
            alert('Please enter valid dates');
            return;
        }

        checkIn.setHours(0, 0, 0, 0);
        checkOut.setHours(0, 0, 0, 0);
        
        if (checkIn < today) {
            e.preventDefault();
            alert('Check-in date cannot be in the past');
            return;
        }
        
        if (checkOut <= checkIn) {
            e.preventDefault();
            alert('Check-out date must be after check-in date');
            return;
        }

        // Disable submit button to prevent double submission
        bookNowBtn.disabled = true;
        bookNowBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
    });

    // Initialize
    updateCheckOutMin();
    validateDates();
});
</script>
@endpush

@endsection
