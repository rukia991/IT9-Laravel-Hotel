@extends('template.master')
@section('title', 'Manage Reservations')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">Manage Reservations</h5>
                </div>
                <div class="col-auto">
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary active" data-filter="all">All</button>
                        <button type="button" class="btn btn-outline-warning" data-filter="pending">Pending</button>
                        <button type="button" class="btn btn-outline-success" data-filter="approved">Approved</button>
                        <button type="button" class="btn btn-outline-danger" data-filter="rejected">Rejected</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="reservationsTable">
                    <thead class="bg-light">
                        <tr>
                            <th>Booking ID</th>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reservations ?? [] as $reservation)
                        <tr data-status="{{ strtolower($reservation->status) }}">
                            <td>#{{ $reservation->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="fw-bold">{{ $reservation->customer->name }}</div>
                                        <div class="text-muted small">{{ $reservation->customer->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold">Room {{ $reservation->room->number }}</div>
                                <div class="text-muted small">{{ $reservation->room->type->name }}</div>
                            </td>
                            <td>{{ Helper::dateFormat($reservation->check_in) }}</td>
                            <td>{{ Helper::dateFormat($reservation->check_out) }}</td>
                            <td>{{ Helper::convertToRupiah($reservation->getTotalPrice()) }}</td>
                            <td>
                                <span class="badge bg-{{ $reservation->status === 'Pending' ? 'warning' : ($reservation->status === 'Approved' ? 'success' : 'danger') }}">
                                    {{ $reservation->status }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold">{{ Helper::convertToRupiah($reservation->getTotalPayment()) }}</div>
                                <div class="text-muted small">
                                    @if($reservation->getTotalPayment() < $reservation->getTotalPrice())
                                        Due: {{ Helper::convertToRupiah($reservation->getTotalPrice() - $reservation->getTotalPayment()) }}
                                    @else
                                        Fully Paid
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if($reservation->status === 'Pending')
                                        <li>
                                            <a class="dropdown-item text-success" href="#" onclick="approveReservation({{ $reservation->id }})">
                                                <i class="fas fa-check me-2"></i> Approve
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="#" onclick="rejectReservation({{ $reservation->id }})">
                                                <i class="fas fa-times me-2"></i> Reject
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        @endif
                                        @if($reservation->status === 'Approved' && $reservation->getTotalPayment() < $reservation->getTotalPrice())
                                        <li>
                                            <a class="dropdown-item" href="{{ route('receptionist.process-payment', $reservation->id) }}">
                                                <i class="fas fa-cash-register me-2"></i> Process Payment
                                            </a>
                                        </li>
                                        @endif
                                        <li>
                                            <a class="dropdown-item" href="{{ route('receptionist.reservation-details', $reservation->id) }}">
                                                <i class="fas fa-eye me-2"></i> View Details
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-3">No reservations found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(isset($reservations) && $reservations->hasPages())
        <div class="card-footer bg-white">
            {{ $reservations->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#reservationsTable').DataTable({
        "order": [[0, "desc"]],
        "pageLength": 25
    });

    // Filter functionality
    $('.btn-group .btn').click(function() {
        $('.btn-group .btn').removeClass('active');
        $(this).addClass('active');
        
        var filter = $(this).data('filter');
        if (filter === 'all') {
            $('tbody tr').show();
        } else {
            $('tbody tr').hide();
            $('tbody tr[data-status="' + filter + '"]').show();
        }
    });
});

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