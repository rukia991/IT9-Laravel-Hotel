@extends('template.master')
@section('title', 'Customer')
@section('content')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="row g-0">
            <div class="col-md-5">
                @if($room->image && count($room->image) > 0)
                    <img src="{{ $room->image[0]->getRoomImage() }}" class="img-fluid rounded-start" alt="Room Image">
                @else
                    <img src="/img/default-room.jpg" class="img-fluid rounded-start" alt="No Image">
                @endif
            </div>
            <div class="col-md-7">
                <div class="card-body">
                    <h5 class="card-title">Room #{{ $room->number }}</h5>
                    <p class="card-text">Type: {{ $room->type->name ?? 'N/A' }}</p>
                    <p class="card-text">Status: {{ $room->roomStatus->name ?? 'N/A' }}</p>
                    @if(isset($customer))
                        <p class="card-text"><strong>Occupied by:</strong> {{ $customer->name }}</p>
                    @else
                        <p class="card-text text-success">Available for booking</p>
                        <form action="{{ route('booking.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="room_id" value="{{ $room->id }}">
                            <button type="submit" class="btn btn-primary">Book this room</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
