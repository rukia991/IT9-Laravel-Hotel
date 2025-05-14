@extends('template.master')
@section('title', 'Process Payment')

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
                    <form action="{{ route('transaction.payment.store', $transaction->id) }}" method="POST" id="paymentForm">
                        @csrf
                        <div class="row g-4">
                            <!-- Reservation Details -->
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading fw-bold mb-1">Reservation Details</h6>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Guest:</strong> {{ $transaction->customer->name }}</p>
                                            <p class="mb-1"><strong>Room:</strong> {{ $transaction->room->number }} ({{ $transaction->room->type->name }})</p>
                                            <p class="mb-1"><strong>Check In:</strong> {{ Helper::dateFormat($transaction->check_in) }}</p>
                                            <p class="mb-1"><strong>Check Out:</strong> {{ Helper::dateFormat($transaction->check_out) }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Total Amount:</strong> {{ Helper::convertToRupiah($transaction->getTotalPrice()) }}</p>
                                            <p class="mb-1"><strong>Amount Paid:</strong> {{ Helper::convertToRupiah($transaction->getTotalPayment()) }}</p>
                                            <p class="mb-1"><strong>Remaining Balance:</strong> {{ Helper::convertToRupiah($transaction->getTotalPrice() - $transaction->getTotalPayment()) }}</p>
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
                                           required>
                                    <input type="hidden" name="payment_numeric" id="payment_numeric">
                                </div>
                                @error('payment')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Credit/Debit Card Fields (Initially Hidden) -->
                            <div id="cardFields" class="d-none">
                                <div class="row g-3">
                                    <!-- Card Number -->
                                    <div class="col-12">
                                        <label class="form-label">Card Number</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="cardNumber" 
                                               name="card_number"
                                               placeholder="**** **** **** ****">
                                    </div>

                                    <!-- Card Holder -->
                                    <div class="col-12">
                                        <label class="form-label">Card Holder Name</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="card_holder"
                                               placeholder="Name on card">
                                    </div>

                                    <!-- Expiry Date and CVV -->
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

                            <!-- Bank Transfer Fields (Initially Hidden) -->
                            <div id="bankFields" class="d-none">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Bank Name</label>
                                        <select class="form-select" name="bank_name">
                                            <option value="">Select Bank</option>
                                            <option value="bca">BCA</option>
                                            <option value="mandiri">Mandiri</option>
                                            <option value="bni">BNI</option>
                                            <option value="bri">BRI</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Reference Number</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="reference_number"
                                               placeholder="Transfer reference number">
                                    </div>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="col-12">
                                <label class="form-label">Payment Notes</label>
                                <textarea class="form-control" name="notes" rows="3">{{ old('notes') }}</textarea>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
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
                                    <div class="fw-bold">{{ Helper::convertToRupiah($payment->amount) }}</div>
                                    <div class="text-muted small">{{ $payment->created_at->format('d M Y H:i') }}</div>
                                    <div class="text-muted small">{{ ucfirst($payment->payment_method) }}</div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Cleave.js for currency formatting
    var cleave = new Cleave('#payment', {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
        numeralDecimalMark: '.',
        delimiter: ','
    });

    // Initialize Cleave.js for card number formatting
    var cardNumberCleave = new Cleave('#cardNumber', {
        creditCard: true
    });

    // Initialize Cleave.js for expiry date formatting
    var expiryDateCleave = new Cleave('#expiryDate', {
        date: true,
        datePattern: ['m', 'y']
    });

    // Initialize Cleave.js for CVV
    var cvvCleave = new Cleave('#cvv', {
        numeral: true,
        numeralPositiveOnly: true,
        stripLeadingZeroes: false
    });

    // Handle payment method change
    document.getElementById('paymentMethod').addEventListener('change', function() {
        const cardFields = document.getElementById('cardFields');
        const bankFields = document.getElementById('bankFields');
        
        // Hide all payment specific fields first
        cardFields.classList.add('d-none');
        bankFields.classList.add('d-none');
        
        // Show relevant fields based on selection
        switch(this.value) {
            case 'credit_card':
            case 'debit_card':
                cardFields.classList.remove('d-none');
                break;
            case 'bank_transfer':
                bankFields.classList.remove('d-none');
                break;
        }
    });

    // Update hidden numeric field before form submission
    document.getElementById('paymentForm').addEventListener('submit', function() {
        var numericValue = cleave.getRawValue();
        document.getElementById('payment_numeric').value = numericValue;
    });
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