<style>
    .dropend:hover .dropdown-menu {
        display: block;
        margin-top: 0;
    }

    #sidebar-wrapper .dropdown-menu.show {
        top: -60px !important;
        left: 80px !important;
    }
</style>
<div class="" id="sidebar-wrapper">
    <div class="d-flex flex-column" style="width: 4.5rem; border-top-right-radius:0.5rem; border-bottom-right-radius:0.5rem;">
        <ul class="nav nav-pills nav-flush flex-column mb-auto text-center">
            <!-- Dashboard -->
            <li class="mb-2 bg-white rounded cursor-pointer">
                <a href="{{ route('receptionist.index') }}" class="nav-link py-3 border-bottom myBtn
                    {{ Route::currentRouteName() == 'receptionist.index' ? 'active' : '' }}" 
                    data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                    <i class="fas fa-home"></i>
                </a>
            </li>

            <!-- Room Status -->
            <li class="mb-2 bg-white rounded cursor-pointer">
                <a href="{{ route('receptionist.rooms') }}" class="nav-link py-3 border-bottom myBtn
                    {{ Route::currentRouteName() == 'receptionist.rooms' ? 'active' : '' }}" 
                    data-bs-toggle="tooltip" data-bs-placement="right" title="Room Status">
                    <i class="fas fa-door-open"></i>
                </a>
            </li>

            <!-- Reservations -->
            <li class="mb-2 bg-white rounded cursor-pointer">
                <a href="{{ route('receptionist.reservations') }}" class="nav-link py-3 border-bottom myBtn
                    {{ Route::currentRouteName() == 'receptionist.reservations' ? 'active' : '' }}" 
                    data-bs-toggle="tooltip" data-bs-placement="right" title="Reservations">
                    <i class="fas fa-calendar-alt"></i>
                </a>
            </li>

            <!-- Check-in/out -->
            <li class="mb-2 bg-white rounded cursor-pointer">
                <a href="{{ route('receptionist.check-in') }}" class="nav-link py-3 border-bottom myBtn
                    {{ Route::currentRouteName() == 'receptionist.check-in' ? 'active' : '' }}" 
                    data-bs-toggle="tooltip" data-bs-placement="right" title="Check-in/out">
                    <i class="fas fa-key"></i>
                </a>
            </li>

            <!-- Payments -->
            <li class="mb-2 bg-white rounded cursor-pointer">
                <a href="{{ route('receptionist.payments') }}" class="nav-link py-3 border-bottom myBtn
                    {{ Route::currentRouteName() == 'receptionist.payments' ? 'active' : '' }}" 
                    data-bs-toggle="tooltip" data-bs-placement="right" title="Payments">
                    <i class="fas fa-cash-register"></i>
                </a>
            </li>
        </ul>
    </div>
</div>

@push('scripts')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush
