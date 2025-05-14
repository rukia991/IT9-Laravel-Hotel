@extends('template.master')
@section('title', 'Payment Management')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">Payment Management</h5>
                </div>
                <div class="col-auto">
                    <div class="btn-group" role="group" aria-label="Payment filters">
                        <button type="button" class="btn btn-outline-primary active" data-filter="all">All Payments</button>
                        <button type="button" class="btn btn-outline-warning" data-filter="pending">Pending</button>
                        <button type="button" class="btn btn-outline-success" data-filter="paid">Paid</button>
                        <button type="button" class="btn btn-outline-info" data-filter="partial">Partial</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Statistics -->
        <div class="card-body border-bottom">
            <div class="row g-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card border-success h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted">Total Paid</div>
                                    <div class="fs-3 fw-bold text-success">{{ str_replace('Rp.', '₱', Helper::convertToRupiah($stats['totalPaid'] ?? 0)) }}</div>
                                </div>
                                <div class="bg-success bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card border-warning h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted">Pending Payments</div>
                                    <div class="fs-3 fw-bold text-warning">{{ str_replace('Rp.', '₱', Helper::convertToRupiah($stats['totalPending'] ?? 0)) }}</div>
                                </div>
                                <div class="bg-warning bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-clock fa-2x text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card border-info h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted">Partial Payments</div>
                                    <div class="fs-3 fw-bold text-info">{{ $stats['partialCount'] ?? 0 }}</div>
                                </div>
                                <div class="bg-info bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-percentage fa-2x text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card border-primary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted">Total Transactions</div>
                                    <div class="fs-3 fw-bold text-primary">{{ $stats['totalTransactions'] ?? 0 }}</div>
                                </div>
                                <div class="bg-primary bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-receipt fa-2x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Transaction ID</th>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions ?? [] as $transaction)
                        @php
                            $totalPrice = $transaction->getTotalPrice();
                            $paidAmount = $transaction->getTotalPayment();
                            $remainingAmount = $totalPrice - $paidAmount;
                            
                            if ($paidAmount >= $totalPrice) {
                                $status = 'paid';
                                $badgeClass = 'bg-success';
                            } elseif ($paidAmount > 0) {
                                $status = 'partial';
                                $badgeClass = 'bg-info';
                            } else {
                                $status = 'pending';
                                $badgeClass = 'bg-warning';
                            }
                        @endphp
                        <tr class="payment-row" data-status="{{ $status }}">
                            <td>#{{ $transaction->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="fw-bold">{{ $transaction->customer->name }}</div>
                                        <div class="text-muted small">{{ $transaction->customer->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold">Room {{ $transaction->room->number }}</div>
                                <div class="text-muted small">{{ $transaction->room->type->name }}</div>
                            </td>
                            <td>{{ Helper::convertToRupiah($totalPrice) }}</td>
                            <td>
                                <div class="fw-bold">{{ Helper::convertToRupiah($paidAmount) }}</div>
                                @if($remainingAmount > 0)
                                <div class="text-muted small">
                                    Due: {{ Helper::convertToRupiah($remainingAmount) }}
                                </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $badgeClass }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    @if($remainingAmount > 0)
                                    <a href="{{ route('receptionist.process-payment', $transaction->id) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-cash-register me-2"></i>Process Payment
                                    </a>
                                    @endif
                                    @if($paidAmount > 0)
                                    <a href="{{ route('payment.invoice', $transaction->id) }}" 
                                       class="btn btn-sm btn-outline-secondary" target="_blank">
                                        <i class="fas fa-file-invoice me-2"></i>View Invoice
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-3">No payment records found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Get all filter buttons and payment rows
    const filterButtons = document.querySelectorAll('[data-filter]');
    const paymentRows = document.querySelectorAll('.payment-row');
    
    // Add click event listener to each button
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Get the filter value
            const filterValue = this.getAttribute('data-filter');
            
            // Show/hide rows based on filter
            paymentRows.forEach(row => {
                if (filterValue === 'all' || row.getAttribute('data-status') === filterValue) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
});
</script>
@endpush
@endsection 