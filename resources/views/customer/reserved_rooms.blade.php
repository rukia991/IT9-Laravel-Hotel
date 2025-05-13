@extends('template.master')

@section('title', 'My Reservations')

@section('content')
<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        @include('template.include._customer_sidebar')
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="container mt-4">
            <h2 class="mb-4">My Reservations</h2>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($transactions->isEmpty())
                <div class="alert alert-info">
                    You don't have any reservations yet.
                </div>
            @else
                <div class="row">
                    @foreach($transactions as $transaction)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Room {{ $transaction->room->number }}</h5>
                                    <span class="badge {{ 
                                        $transaction->status == 'Approved' ? 'bg-success' : 
                                        ($transaction->status == 'Pending' ? 'bg-warning' : 
                                        ($transaction->status == 'Rejected' ? 'bg-danger' : 'bg-secondary')) 
                                    }} fs-6">
                                        {{ $transaction->status }}
                                    </span>
                                </div>
                                
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            @if($transaction->room && $transaction->room->images->first())
                                                <img src="{{ asset('storage/images/' . $transaction->room->images->first()->path) }}" 
                                                     onerror="this.onerror=null; this.src='https://placehold.co/600x400?text=Room+{{ $transaction->room->number }}';"
                                                     class="img-fluid rounded" 
                                                     alt="Room Image">
                                            @else
                                                <img src="https://placehold.co/600x400?text=Room+{{ $transaction->room->number }}"
                                                     class="img-fluid rounded"
                                                     alt="Default Room Image">
                                            @endif
                                        </div>
                                        <div class="col-md-8">
                                            <h6 class="card-subtitle mb-2 text-muted">{{ $transaction->room->type->name }}</h6>
                                            
                                            <div class="mb-3">
                                                <p class="mb-1"><strong>Check In:</strong> {{ \Carbon\Carbon::parse($transaction->check_in)->format('M d, Y') }}</p>
                                                <p class="mb-1"><strong>Check Out:</strong> {{ \Carbon\Carbon::parse($transaction->check_out)->format('M d, Y') }}</p>
                                                <p class="mb-1"><strong>Guests:</strong> {{ $transaction->number_of_guests }}</p>
                                                <p class="mb-0"><strong>Total Nights:</strong> 
                                                    {{ \Carbon\Carbon::parse($transaction->check_in)->diffInDays($transaction->check_out) }}
                                                </p>
                                            </div>

                                            @if($transaction->special_requests)
                                                <div class="mb-3">
                                                    <strong>Special Requests:</strong><br>
                                                    <p class="mb-0 text-muted">{{ $transaction->special_requests }}</p>
                                                </div>
                                            @endif

                                            @if($transaction->status == 'Approved')
                                                <div class="alert alert-success mb-3">
                                                    <i class="fas fa-check-circle"></i> Your reservation has been approved!
                                                    @if($transaction->approved_at)
                                                        <br>
                                                        <small>Approved on {{ \Carbon\Carbon::parse($transaction->approved_at)->format('M d, Y h:i A') }}</small>
                                                    @endif
                                                </div>
                                            @elseif($transaction->status == 'Rejected')
                                                <div class="alert alert-danger mb-3">
                                                    <i class="fas fa-times-circle"></i> Your reservation was rejected.
                                                    @if($transaction->rejected_at)
                                                        <br>
                                                        <small>Rejected on {{ \Carbon\Carbon::parse($transaction->rejected_at)->format('M d, Y h:i A') }}</small>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="alert alert-warning mb-3">
                                                    <i class="fas fa-clock"></i> Your reservation is pending approval.
                                                    <br>
                                                    <small>Submitted on {{ $transaction->created_at->format('M d, Y h:i A') }}</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });
</script>
@endpush

@endsection 