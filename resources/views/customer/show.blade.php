@extends('template.master')
@section('title', 'Room Details')
@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="row g-0">
            <div class="col-md-5">
                <img src="{{ $room->image }}" class="img-fluid rounded-start" alt="Room Image">
            </div>
            <div class="col-md-7">
                <div class="card-body">
                    <h5 class="card-title">Room #{{ $room->number }}</h5>
                    <p class="card-text">Type: {{ $room->type->name ?? 'N/A' }}</p>
                    <p class="card-text">Price: ₱{{ number_format($room->price, 2) }}</p>
                    <p class="card-text">Description: {{ $room->description }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection