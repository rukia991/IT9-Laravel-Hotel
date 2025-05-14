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
                            <div class="fs-4 fw-bold text-success">{{ count($availableRoomsList) }}</div>
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
                            <div class="fs-4 fw-bold text-warning">{{ count($occupiedRoomsList) }}</div>
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

    <div class="row">
        <!-- Pending Reservations -->
        <div class="col-xl-7 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Pending Reservations</h5>
                        <a href="{{ route('receptionist.reservations') }}" class="btn btn-sm btn-primary">View All</a>
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
                                @forelse($pendingReservationsList as $reservation)
                                <tr>
                                    <td>{{ $reservation->customer->name ?? '-' }}</td>
                                    <td>{{ $reservation->room->number ?? '-' }}</td>
                                    <td>{{ $reservation->check_in }}</td>
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
                                    <td colspan="5" class="text-center">No pending reservations</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-xl-5 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <a href="{{ route('receptionist.new-reservation') }}" class="text-decoration-none">
                                <div class="card bg-success bg-opacity-10">
                                    <div class="card-body d-flex align-items-center p-3">
                                        <i class="fas fa-calendar-plus fa-2x text-success me-3"></i>
                                        <div>
                                            <h6 class="card-title text-success mb-0">New Reservation</h6>
                                            <small class="text-muted">Create a new booking</small>
                                        </div>
                                        <i class="fas fa-chevron-right ms-auto text-success"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-12">
                            <a href="{{ route('receptionist.payments') }}" class="text-decoration-none">
                                <div class="card bg-primary bg-opacity-10">
                                    <div class="card-body d-flex align-items-center p-3">
                                        <i class="fas fa-cash-register fa-2x text-primary me-3"></i>
                                        <div>
                                            <h6 class="card-title text-primary mb-0">Process Payment</h6>
                                            <small class="text-muted">Handle guest payments</small>
                                        </div>
                                        <i class="fas fa-chevron-right ms-auto text-primary"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-12">
                            <a href="{{ route('receptionist.check-in') }}" class="text-decoration-none">
                                <div class="card bg-info bg-opacity-10">
                                    <div class="card-body d-flex align-items-center p-3">
                                        <i class="fas fa-key fa-2x text-info me-3"></i>
                                        <div>
                                            <h6 class="card-title text-info mb-0">Check-in/out</h6>
                                            <small class="text-muted">Process guest arrivals/departures</small>
                                        </div>
                                        <i class="fas fa-chevron-right ms-auto text-info"></i>
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
