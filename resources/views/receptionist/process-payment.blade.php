@extends('template.master')
@section('title', 'Process Payment')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0">Process Payment</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('transaction.payment.store', ['transaction' => $transaction->id]) }}" method="POST" id="paymentForm">
                        @csrf
                        <div class="row g-4">
                            <!-- Reservation Details -->
                            <div class="col-12">
                                <div class="bg-light rounded p-3">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <p class="text-muted mb-1">Guest</p>
                                            <p class="fw-medium mb-0">{{ $transaction->customer->name }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="text-muted mb-1">Room</p>
                                            <p class="fw-medium mb-0">{{ $transaction->room->number }} ({{ $transaction->room->type->name }})</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="text-muted mb-1">Check In</p>
                                            <p class="fw-medium mb-0">{{ $transaction->check_in->format('d M Y') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="text-muted mb-1">Check Out</p>
                                            <p class="fw-medium mb-0">{{ $transaction->check_out->format('d M Y') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="text-muted mb-1">Total Amount</p>
                                            <p class="fw-medium mb-0">₱ {{ number_format($transaction->getTotalPrice(), 2) }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="text-muted mb-1">Amount Paid</p>
                                            <p class="fw-medium mb-0">₱ {{ number_format($transaction->getTotalPayment(), 2) }}</p>
                                        </div>
                                        <div class="col-12">
                                            <p class="text-muted mb-1">Remaining Balance</p>
                                            <p class="fw-medium mb-0">₱ {{ number_format($transaction->getTotalPrice() - $transaction->getTotalPayment(), 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div class="col-md-6">
                                <label class="form-label">Payment Method</label>
                                <select class="form-select @error('payment_method') is-invalid @enderror" 
                                        name="payment_method" 
                                        id="paymentMethod" 
                                        required>
                                    <option value="">Select Payment Method</option>
                                    <option value="cash">Cash</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="debit_card">Debit Card</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                                @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Payment Amount -->
                            <div class="col-md-6">
                                <label class="form-label">Payment Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="text" 
                                           class="form-control @error('payment') is-invalid @enderror" 
                                           name="payment" 
                                           id="payment"
                                           value="{{ old('payment') }}"
                                           placeholder="0.00"
                                           required>
                                </div>
                                @error('payment')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Enter amount (commas and decimals allowed, e.g. 100,000.00)</div>
                            </div>

                            <!-- Credit/Debit Card Fields -->
                            <div id="cardFields" class="d-none">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Card Number</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="cardNumber" 
                                               name="card_number"
                                               placeholder="**** **** **** ****">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Expiry Date</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="expiryDate" 
                                               name="expiry_date"
                                               placeholder="MM/YY">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">CVV</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="cvv" 
                                               name="cvv"
                                               placeholder="***"
                                               maxlength="3">
                                    </div>
                                </div>
                            </div>

                            <!-- Bank Transfer Fields -->
                            <div id="bankFields" class="d-none">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Bank</label>
                                        <select class="form-select" name="bank_name">
                                            <option value="">Select Bank</option>
                                            <option value="bdo">BDO</option>
                                            <option value="bpi">BPI</option>
                                            <option value="metrobank">Metrobank</option>
                                            <option value="unionbank">UnionBank</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Reference Number</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="reference_number"
                                               placeholder="Enter reference number">
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Notes -->
                            <div class="col-12">
                                <label class="form-label">Payment Notes</label>
                                <textarea class="form-control" 
                                          name="payment_notes" 
                                          rows="3" 
                                          placeholder="Enter any additional notes about the payment"></textarea>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save me-2"></i>Process Payment
                                </button>
                                <a href="{{ route('receptionist.payments') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Payment History</h5>
                </div>
                <div class="card-body">
                    @if($transaction->payment->isNotEmpty())
                        <div class="timeline">
                            @foreach($transaction->payment->sortByDesc('created_at') as $payment)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <div class="fw-bold">₱ {{ number_format($payment->amount, 2) }}</div>
                                    <div class="text-muted small">{{ $payment->created_at->format('d M Y H:i') }}</div>
                                    <div class="text-muted small">{{ ucfirst($payment->payment_method ?? 'Not specified') }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No payment history available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing payment form...');
    
    const form = document.getElementById('paymentForm');
    const submitBtn = document.getElementById('submitBtn');
    const paymentMethodSelect = document.getElementById('paymentMethod');
    const paymentInput = document.getElementById('payment');

    // Initialize Cleave.js for currency formatting
    var cleave = new Cleave('#payment', {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
        numeralDecimalMark: '.',
        delimiter: ',',
        numeralPositiveOnly: true,
        numeralDecimalScale: 2,
        prefix: '',
        rawValueTrimPrefix: true
    });

    // Form submission handler with confirmation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Form submission started');

        // Basic validation
        if (!paymentMethodSelect.value) {
            console.log('Payment method not selected');
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please select a payment method'
            });
            return;
        }

        if (!paymentInput.value) {
            console.log('Payment amount not entered');
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please enter a payment amount'
            });
            return;
        }

        // Log form data before submission
        const formData = new FormData(form);
        console.log('Form data before submission:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }

        // Show confirmation dialog
        Swal.fire({
            title: 'Confirm Payment',
            text: 'Are you sure you want to process this payment?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, process payment',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                console.log('Payment confirmed, processing...');
                
                // Clean the payment value before submission
                var cleanValue = paymentInput.value.replace(/,/g, '');
                if (!cleanValue.includes('.')) {
                    cleanValue += '.00';
                }
                paymentInput.value = cleanValue;
                console.log('Cleaned payment value:', cleanValue);
                
                // Show loading state
                Swal.fire({
                    title: 'Processing Payment',
                    text: 'Please wait...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                        console.log('Submitting form to:', form.action);
                        form.submit();
                    }
                });
            } else {
                console.log('Payment cancelled by user');
            }
        });
    });

    // Show/hide payment method specific fields
    paymentMethodSelect.addEventListener('change', function() {
        console.log('Payment method changed to:', this.value);
        const cardFields = document.getElementById('cardFields');
        const bankFields = document.getElementById('bankFields');
        
        cardFields.classList.add('d-none');
        bankFields.classList.add('d-none');
        
        if (this.value === 'credit_card' || this.value === 'debit_card') {
            cardFields.classList.remove('d-none');
        } else if (this.value === 'bank_transfer') {
            bankFields.classList.remove('d-none');
        }
    });

    // Initialize card-related Cleave instances
    if (document.getElementById('cardNumber')) {
        new Cleave('#cardNumber', {
            creditCard: true
        });
    }

    if (document.getElementById('expiryDate')) {
        new Cleave('#expiryDate', {
            date: true,
            datePattern: ['m', 'y']
        });
    }

    if (document.getElementById('cvv')) {
        new Cleave('#cvv', {
            numeral: true,
            numeralIntegerScale: 3,
            numeralPositiveOnly: true,
            stripLeadingZeroes: true
        });
    }
});
</script>

<style>
.timeline {
    position: relative;
    padding-left: 1.5rem;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 0.75rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}
.timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
}
.timeline-marker {
    position: absolute;
    left: -1.5rem;
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
}
.timeline-content {
    padding-left: 0.5rem;
}
</style>
@endpush
@endsection 