@extends('template.master')

@section('title', 'My Reservations')

@section('content')

<style>
    .reservation-card {
        transition: transform 0.2s;
    }
    .reservation-card:hover {
        transform: translateY(-5px);
    }
    .status-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 5px 10px;
        border-radius: 15px;
    }
    .room-image {
        height: 200px;
        object-fit: cover;
    }
</style>

<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        @include('template.include._customer_sidebar')
    </div>

    <!-- Page Content -->
    <div class="content">
        <div class="container mt-4">
            <h1 class="mb-4">My Reservations</h1>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($transactions->isEmpty())
                <div class="alert alert-info">
                    <h4 class="alert-heading">No Reservations Yet!</h4>
                    <p>You haven't made any room reservations yet. Start exploring our available rooms and book your perfect stay!</p>
                    <hr>
                    <a href="{{ route('customer.index') }}" class="btn btn-primary">Browse Rooms</a>
                </div>
            @else
                <div class="row">
                    @foreach($transactions as $transaction)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card reservation-card h-100 shadow-sm">
                                @if($transaction->room->images->first())
                                    <img src="{{ asset('storage/images/' . $transaction->room->images->first()->path) }}"
                                         class="card-img-top room-image"
                                         alt="Room Image">
                                @else
                                    <img src="https://placehold.co/600x400?text=No+Image"
                                         class="card-img-top room-image"
                                         alt="No Image Available">
                                @endif

                                <div class="status-badge badge bg-{{ $transaction->status === 'Pending' ? 'warning' : ($transaction->status === 'Approved' ? 'success' : 'danger') }}">
                                    {{ $transaction->status }}
                                </div>

                                <div class="card-body">
                                    <h5 class="card-title">{{ $transaction->room->type->name }} Room</h5>
                                    <p class="card-text">Room #{{ $transaction->room->number }}</p>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-alt"></i> Check-in: {{ $transaction->check_in->format('M d, Y') }}
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-check"></i> Check-out: {{ $transaction->check_out->format('M d, Y') }}
                                        </small>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-users"></i> Guests: {{ $transaction->number_of_guests }}
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-money-bill-wave"></i> Total: ₱{{ number_format($transaction->total_price, 2) }}
                                        </small>
                                    </div>

                                    @if($transaction->special_requests)
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-comment"></i> Special Requests:
                                                <br>
                                                {{ $transaction->special_requests }}
                                            </small>
                                        </div>
                                    @endif

                                    @if($transaction->status === 'Pending')
                                        <div class="alert alert-warning" role="alert">
                                            <small>
                                                <i class="fas fa-clock"></i> Waiting for confirmation. We'll notify you once your booking is approved.
                                            </small>
                                        </div>
                                    @endif
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