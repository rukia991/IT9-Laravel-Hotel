
@extends('template.master')

@section('title', 'Manager Dashboard')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Welcome, Manager!</h4>
                </div>
                <div class="card-body">
                    <p class="lead">This is your dashboard. Here you can:</p>
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item">Oversee hotel operations and staff</li>
                        <li class="list-group-item">Review occupancy and revenue reports</li>
                        <li class="list-group-item">Manage room rates and availability</li>
                        <li class="list-group-item">Approve or review reservations and payments</li>
                    </ul>
                    <div class="alert alert-info">
                        Use the sidebar to access management tools and reports.
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
