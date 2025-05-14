@extends('template.master')
@section('title', 'Check-in Management')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row g-4">
        <!-- Today's Check-ins -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0"><i class="fas fa-calendar-day me-2"></i>Today's Check-ins</h5>
                            <p class="mb-0 opacity-75 small">{{ now()->format('l, F j, Y') }}</p>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-white text-primary">{{ $todayCheckins->count() }} Guest(s)</span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Guest</th>
                                    <th>Room</th>
                                    <th>Check-in Time</th>
                                    <th>Duration</th>
                                    <th>Payment Status</th>
                                    <th class="pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($todayCheckins as $checkin)
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle p-2 me-3">
                                                <i class="fas fa-user text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $checkin->customer->name }}</div>
                                                <div class="text-muted small">{{ $checkin->customer->phone }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">Room {{ $checkin->room->number }}</div>
                                        <div class="text-muted small">{{ $checkin->room->type->name }}</div>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($checkin->check_in)->format('h:i A') }}</td>
                                    <td>{{ $checkin->duration }} night(s)</td>
                                    <td>
                                        @php
                                            $totalPrice = $checkin->getTotalPrice();
                                            $paidAmount = $checkin->getTotalPayment();
                                            $isPaid = $paidAmount >= $totalPrice;
                                        @endphp
                                        @if($isPaid)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                                <i class="fas fa-check-circle me-1"></i>Paid
                                            </span>
                                        @else
                                            <span class="badge bg-orange-subtle text-orange border border-orange-subtle px-3 py-2" 
                                                  style="background-color: #fff3e6 !important; color: #ff8c00 !important; border-color: #ffd9b3 !important;">
                                                <i class="fas fa-hourglass-half me-1"></i>Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="pe-3">
                                        <div class="btn-group">
                                            @if(!$isPaid)
                                                <a href="{{ route('receptionist.process-payment', $checkin->id) }}" 
                                                   class="btn btn-warning btn-sm">
                                                    <i class="fas fa-cash-register me-1"></i>Process Payment
                                                </a>
                                            @endif
                                            <button type="button" 
                                                    class="btn btn-success btn-sm"
                                                    onclick="processCheckIn({{ $checkin->id }})">
                                                <i class="fas fa-check-circle me-1"></i>Check In
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-info-circle me-2"></i>No check-ins scheduled for today
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Check-ins -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Upcoming Check-ins</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Guest</th>
                                    <th>Room</th>
                                    <th>Check-in Date</th>
                                    <th>Duration</th>
                                    <th>Payment Status</th>
                                    <th class="pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($upcomingCheckins as $checkin)
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle p-2 me-3">
                                                <i class="fas fa-user text-info"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $checkin->customer->name }}</div>
                                                <div class="text-muted small">{{ $checkin->customer->phone }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">Room {{ $checkin->room->number }}</div>
                                        <div class="text-muted small">{{ $checkin->room->type->name }}</div>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($checkin->check_in)->format('M d, Y') }}</td>
                                    <td>{{ $checkin->duration }} night(s)</td>
                                    <td>
                                        @php
                                            $totalPrice = $checkin->getTotalPrice();
                                            $paidAmount = $checkin->getTotalPayment();
                                            $isPaid = $paidAmount >= $totalPrice;
                                        @endphp
                                        @if($isPaid)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                                <i class="fas fa-check-circle me-1"></i>Paid
                                            </span>
                                        @else
                                            <span class="badge bg-orange-subtle text-orange border border-orange-subtle px-3 py-2" 
                                                  style="background-color: #fff3e6 !important; color: #ff8c00 !important; border-color: #ffd9b3 !important;">
                                                <i class="fas fa-hourglass-half me-1"></i>Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="pe-3">
                                        @if(!$isPaid)
                                            <a href="{{ route('receptionist.process-payment', $checkin->id) }}" 
                                               class="btn btn-warning btn-sm">
                                                <i class="fas fa-cash-register me-1"></i>Process Payment
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-info-circle me-2"></i>No upcoming check-ins
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Check-in Confirmation Modal -->
<div class="modal fade" id="checkInModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Confirm Check-in</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="bg-success bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                        <i class="fas fa-hotel fa-2x text-success"></i>
                    </div>
                    <h5>Are you sure you want to check in this guest?</h5>
                    <p class="text-muted">This will mark the room as occupied and update the reservation status.</p>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <form id="checkInForm" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success px-4">Confirm Check-in</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function processCheckIn(transactionId) {
    const modal = new bootstrap.Modal(document.getElementById('checkInModal'));
    const form = document.getElementById('checkInForm');
    form.action = `/receptionist/check-in/${transactionId}/process`;
    modal.show();
}
</script>
@endpush
@endsection 