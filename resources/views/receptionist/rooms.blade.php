@extends('template.master')
@section('title', 'Room Status')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">Room Status</h5>
                </div>
                <div class="col-auto">
                    <div class="btn-group" role="group" aria-label="Room filters">
                        <button type="button" class="btn btn-outline-primary active" data-filter="all">All Rooms</button>
                        <button type="button" class="btn btn-outline-success" data-filter="available">Available</button>
                        <button type="button" class="btn btn-outline-danger" data-filter="occupied">Occupied</button>
                        <button type="button" class="btn btn-outline-warning" data-filter="reserved">Reserved</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Stats Row -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted">Available Rooms</div>
                                    <div class="fs-3 fw-bold text-success">{{ $stats['availableRooms'] }}</div>
                                </div>
                                <div class="bg-success bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-door-open fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-danger">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted">Occupied Rooms</div>
                                    <div class="fs-3 fw-bold text-danger">{{ $stats['occupiedRooms'] }}</div>
                                </div>
                                <div class="bg-danger bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-door-closed fa-2x text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted">Reserved Rooms</div>
                                    <div class="fs-3 fw-bold text-warning">{{ $stats['pendingRooms'] }}</div>
                                </div>
                                <div class="bg-warning bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-clock fa-2x text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted">Total Rooms</div>
                                    <div class="fs-3 fw-bold text-primary">{{ $stats['totalRooms'] }}</div>
                                </div>
                                <div class="bg-primary bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-hotel fa-2x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rooms Grid -->
            <div class="row g-4" id="roomsContainer">
                @forelse($rooms ?? [] as $room)
                <div class="col-xl-3 col-lg-4 col-md-6 room-card" data-status="{{ $room->status }}">
                    <div class="card h-100 {{ $room->status === 'available' ? 'border-success' : ($room->status === 'reserved' ? 'border-warning' : 'border-danger') }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Room {{ $room->number }}</h5>
                                <span class="badge {{ $room->status === 'available' ? 'bg-success' : ($room->status === 'reserved' ? 'bg-warning' : 'bg-danger') }}">
                                    {{ ucfirst($room->status) }}
                                </span>
                            </div>
                            <div class="mb-3">
                                <div class="fw-bold text-muted mb-1">{{ $room->type->name }}</div>
                                <div class="fs-5 fw-bold">{{ Helper::convertToRupiah($room->price) }} / night</div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-bed text-muted me-2"></i>
                                    <span>{{ $room->capacity }} Guests</span>
                                </div>
                                @if($room->facilities->isNotEmpty())
                                    @foreach($room->facilities as $facility)
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="{{ $facility->icon ?? 'fas fa-check' }} text-muted me-2"></i>
                                        <span>{{ $facility->name }}</span>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                            @if($room->status !== 'available')
                            <div class="alert alert-secondary mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-clock text-muted me-2"></i>
                                    <span>{{ $room->status === 'reserved' ? 'Reserved until:' : 'Occupied until:' }}</span>
                                </div>
                                <div class="fw-bold">{{ Helper::dateFormat($room->check_out_date) }}</div>
                                @if($room->status === 'occupied' && $room->guest_info)
                                <div class="mt-2">
                                    <strong>Guest:</strong> {{ $room->guest_info->name }}
                                </div>
                                @endif
                            </div>
                            @endif
                            <div class="d-grid gap-2">
                                @if($room->status === 'available')
                                <a href="{{ route('receptionist.new-reservation', ['room' => $room->id]) }}" class="btn btn-success">
                                    <i class="fas fa-calendar-plus me-2"></i>New Reservation
                                </a>
                                @endif
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#roomDetails{{ $room->id }}">
                                    <i class="fas fa-info-circle me-2"></i>View Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Room Details Modal -->
                <div class="modal fade" id="roomDetails{{ $room->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Room {{ $room->number }} Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3">Room Information</h6>
                                        <table class="table">
                                            <tr>
                                                <td>Room Type</td>
                                                <td>{{ $room->type->name }}</td>
                                            </tr>
                                            <tr>
                                                <td>Price per Night</td>
                                                <td>{{ Helper::convertToRupiah($room->price) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Capacity</td>
                                                <td>{{ $room->capacity }} Guests</td>
                                            </tr>
                                            <tr>
                                                <td>Status</td>
                                                <td>
                                                    <span class="badge {{ $room->status === 'available' ? 'bg-success' : ($room->status === 'reserved' ? 'bg-warning' : 'bg-danger') }}">
                                                        {{ ucfirst($room->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3">Amenities</h6>
                                        <ul class="list-group">
                                            @if($room->facilities->isNotEmpty())
                                                @foreach($room->facilities as $facility)
                                                <li class="list-group-item">
                                                    <i class="{{ $facility->icon ?? 'fas fa-check' }} me-2"></i> {{ $facility->name }}
                                                </li>
                                                @endforeach
                                            @else
                                                <li class="list-group-item">No amenities listed</li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                                @if($room->status !== 'available')
                                <div class="mt-4">
                                    <h6 class="fw-bold mb-3">{{ $room->status === 'reserved' ? 'Reservation Details' : 'Current Guest Information' }}</h6>
                                    <div class="alert alert-secondary">
                                        <p class="mb-2">
                                            <strong>{{ $room->status === 'reserved' ? 'Reserved' : 'Occupied' }} Until:</strong>
                                            {{ Helper::dateFormat($room->check_out_date) }}
                                        </p>
                                        @if($room->status === 'occupied' && $room->guest_info)
                                        <p class="mb-0">
                                            <strong>Guest Name:</strong>
                                            {{ $room->guest_info->name }}
                                        </p>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                @if($room->status === 'available')
                                <a href="{{ route('receptionist.new-reservation', ['room' => $room->id]) }}" class="btn btn-success">
                                    <i class="fas fa-calendar-plus me-2"></i>New Reservation
                                </a>
                                @endif
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>No rooms available
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Get all filter buttons
    const filterButtons = document.querySelectorAll('[data-filter]');
    
    // Add click event listener to each button
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Get the filter value
            const filterValue = this.getAttribute('data-filter');
            
            // Get all room cards
            const roomCards = document.querySelectorAll('.room-card');
            
            // Show/hide cards based on filter
            roomCards.forEach(card => {
                if (filterValue === 'all' || card.getAttribute('data-status') === filterValue) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>
@endpush
@endsection 