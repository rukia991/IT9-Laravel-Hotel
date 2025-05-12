@extends('template.master')

@section('title', 'Available Rooms')

@section('content')
<div class="container mt-4">
    <h1 class="text-center mb-4">Available Rooms</h1>

    <div class="row">
        @forelse ($rooms as $room)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <img src="{{ $room->image }}" 
                         onerror="this.onerror=null; this.src='https://placehold.co/300x200?text=No+Image';" 
                         class="card-img-top" 
                         alt="{{ $room->name }}" 
                         style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title">{{ $room->name }}</h5>
                        <p class="card-text">{{ Str::limit($room->description, 100) }}</p>
                        <div class="price fw-bold mb-2">₱{{ number_format($room->price, 2) }}</div>
                        <a href="{{ route('customer.room.show', $room->id) }}" class="btn btn-primary w-100">View Room</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    No rooms available at the moment.
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($rooms->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $rooms->links() }}
        </div>
    @endif
</div>
@endsection