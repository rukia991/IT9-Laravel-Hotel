@extends('template.master')

@section('title', 'Receptionist Dashboard')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-start border-5 border-primary shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fs-6 text-muted">Pending Reservations</div>
                            <div class="fs-4 fw-bold text-primary">{{ $pendingReservations ?? 0 }}</div>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-clock fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-start border-5 border-success shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fs-6 text-muted">Available Rooms</div>
                            <div class="fs-4 fw-bold text-success">{{ $availableRooms ?? 0 }}</div>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-door-open fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-start border-5 border-warning shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fs-6 text-muted">Occupied Rooms</div>
                            <div class="fs-4 fw-bold text-warning">{{ $occupiedRooms ?? 0 }}</div>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-door-closed fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-start border-5 border-info shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fs-6 text-muted">Today's Check-ins</div>
                            <div class="fs-4 fw-bold text-info">{{ $todayCheckins ?? 0 }}</div>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-calendar-check fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="row">
        <!-- Pending Reservations -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Pending Reservations</h5>
                        <button class="btn btn-sm btn-primary" onclick="window.location.href='{{ route('receptionist.reservations') }}'">
                            View All
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Guest</th>
                                    <th>Room</th>
                                    <th>Check In</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingReservationsList ?? [] as $reservation)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="ms-2">
                                                <div class="fw-bold">{{ $reservation->customer->name }}</div>
                                                <div class="text-muted small">{{ $reservation->customer->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Room {{ $reservation->room->number }}</td>
                                    <td>{{ Helper::dateFormat($reservation->check_in) }}</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-success me-1" onclick="approveReservation({{ $reservation->id }})">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="rejectReservation({{ $reservation->id }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3">No pending reservations</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Room Status -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Room Status</h5>
                        <button class="btn btn-sm btn-primary" onclick="window.location.href='{{ route('receptionist.rooms') }}'">
                            View All
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($roomsList ?? [] as $room)
                        <div class="col-md-6">
                            <div class="card h-100 {{ $room->status === 'Available' ? 'border-success' : 'border-danger' }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="card-title mb-1">Room {{ $room->number }}</h6>
                                        <span class="badge {{ $room->status === 'Available' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $room->status }}
                                        </span>
                                    </div>
                                    <p class="card-text small mb-0">{{ $room->type->name }}</p>
                                    <p class="card-text"><small class="text-muted">{{ Helper::convertToRupiah($room->price) }} / night</small></p>
                                    @if($room->status !== 'Available')
                                    <small class="text-muted">
                                        Occupied until: {{ Helper::dateFormat($room->occupied_until) }}
                                    </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <p class="text-center mb-0">No rooms available</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('receptionist.new-reservation') }}" class="text-decoration-none">
                                <div class="card bg-primary bg-opacity-10 h-100">
                                    <div class="card-body text-center py-4">
                                        <i class="fas fa-calendar-plus fa-3x text-primary mb-3"></i>
                                        <h5 class="card-title text-primary">New Reservation</h5>
                                        <p class="card-text text-muted mb-0">Create a new room reservation</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('receptionist.payments') }}" class="text-decoration-none">
                                <div class="card bg-success bg-opacity-10 h-100">
                                    <div class="card-body text-center py-4">
                                        <i class="fas fa-cash-register fa-3x text-success mb-3"></i>
                                        <h5 class="card-title text-success">Process Payment</h5>
                                        <p class="card-text text-muted mb-0">Handle guest payments</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('receptionist.check-in') }}" class="text-decoration-none">
                                <div class="card bg-info bg-opacity-10 h-100">
                                    <div class="card-body text-center py-4">
                                        <i class="fas fa-key fa-3x text-info mb-3"></i>
                                        <h5 class="card-title text-info">Check-in/out</h5>
                                        <p class="card-text text-muted mb-0">Process guest check-in/out</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function approveReservation(id) {
    if (confirm('Are you sure you want to approve this reservation?')) {
        window.location.href = `/receptionist/reservations/${id}/approve`;
    }
}

function rejectReservation(id) {
    if (confirm('Are you sure you want to reject this reservation?')) {
        window.location.href = `/receptionist/reservations/${id}/reject`;
    }
}
</script>
@endpush
@endsection
