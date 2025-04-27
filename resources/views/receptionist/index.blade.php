
@extends('template.master')

@section('title', 'Receptionist Dashboard')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Welcome, Receptionist!</h4>
                </div>
                <div class="card-body">
                    <p class="lead">This is your dashboard. Here you can:</p>
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item">View and manage room reservations</li>
                        <li class="list-group-item">Check in and check out guests</li>
                        <li class="list-group-item">Access customer information</li>
                        <li class="list-group-item">View payment and billing details</li>
                    </ul>
                    <div class="alert alert-info">
                        Use the sidebar to navigate through your available actions.
                    </div>
                </div>
                <div class="card-footer text-end">
                    <small class="text-muted">Hotel Management System &copy; {{ date('Y') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
