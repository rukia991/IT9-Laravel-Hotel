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
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary active" data-filter="all">All</button>
                        <button type="button" class="btn btn-outline-success" data-filter="available">Available</button>
                        <button type="button" class="btn btn-outline-danger" data-filter="occupied">Occupied</button>
                        <button type="button" class="btn btn-outline-warning" data-filter="maintenance">Maintenance</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-4">
                @forelse($rooms ?? [] as $room)
                <div class="col-xl-3 col-lg-4 col-md-6" data-status="{{ strtolower($room->status) }}">
                    <div class="card h-100 {{ $room->status === 'Available' ? 'border-success' : ($room->status === 'Maintenance' ? 'border-warning' : 'border-danger') }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Room {{ $room->number }}</h5>
                                <span class="badge {{ $room->status === 'Available' ? 'bg-success' : ($room->status === 'Maintenance' ? 'bg-warning' : 'bg-danger') }}">
                                    {{ $room->status }}
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
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-ruler-combined text-muted me-2"></i>
                                    <span>{{ $room->size }} m²</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-wifi text-muted me-2"></i>
                                    <span>Free WiFi</span>
                                </div>
                            </div>
                            @if($room->status !== 'Available')
                            <div class="alert alert-secondary mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-clock text-muted me-2"></i>
                                    <span>{{ $room->status === 'Maintenance' ? 'Under maintenance until:' : 'Occupied until:' }}</span>
                                </div>
                                <div class="fw-bold">{{ Helper::dateFormat($room->occupied_until) }}</div>
                            </div>
                            @endif
                            <div class="d-grid gap-2">
                                @if($room->status === 'Available')
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
                                                <td>Size</td>
                                                <td>{{ $room->size }} m²</td>
                                            </tr>
                                            <tr>
                                                <td>Status</td>
                                                <td>
                                                    <span class="badge {{ $room->status === 'Available' ? 'bg-success' : ($room->status === 'Maintenance' ? 'bg-warning' : 'bg-danger') }}">
                                                        {{ $room->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3">Amenities</h6>
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <i class="fas fa-wifi me-2"></i> Free WiFi
                                            </li>
                                            <li class="list-group-item">
                                                <i class="fas fa-tv me-2"></i> Smart TV
                                            </li>
                                            <li class="list-group-item">
                                                <i class="fas fa-snowflake me-2"></i> Air Conditioning
                                            </li>
                                            <li class="list-group-item">
                                                <i class="fas fa-coffee me-2"></i> Coffee Maker
                                            </li>
                                            <li class="list-group-item">
                                                <i class="fas fa-bath me-2"></i> Private Bathroom
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                @if($room->status !== 'Available')
                                <div class="mt-4">
                                    <h6 class="fw-bold mb-3">Current Occupancy</h6>
                                    <div class="alert alert-secondary">
                                        <p class="mb-2">
                                            <strong>{{ $room->status === 'Maintenance' ? 'Under Maintenance' : 'Occupied' }} Until:</strong>
                                            {{ Helper::dateFormat($room->occupied_until) }}
                                        </p>
                                        @if($room->status !== 'Maintenance')
                                        <p class="mb-0">
                                            <strong>Current Guest:</strong>
                                            {{ $room->currentGuest->name ?? 'N/A' }}
                                        </p>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                @if($room->status === 'Available')
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
$(document).ready(function() {
    // Filter functionality
    $('.btn-group .btn').click(function() {
        $('.btn-group .btn').removeClass('active');
        $(this).addClass('active');
        
        var filter = $(this).data('filter');
        if (filter === 'all') {
            $('[data-status]').show();
        } else {
            $('[data-status]').hide();
            $('[data-status="' + filter + '"]').show();
        }
    });
});
</script>
@endpush
@endsection 