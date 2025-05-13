 <style>
    #wrapper {
        display: flex;
        transition: all 0.3s ease-in-out;
    }
    
    #sidebar-wrapper {
        width: 200px; /* Adjust the width as needed */
        background-color: #f8f9fa;
        min-height: 100vh;
        transition: transform 0.3s ease-in-out;
        transform: translateX(0); /* Default position */
    }
    
    #wrapper.toggled #sidebar-wrapper {
        transform: translateX(-100%); /* Hide the sidebar completely */
    }
    
    .content {
        flex: 1;
        transition: margin-left 0.3s ease-in-out;
        margin-left: 100px; /* Match the sidebar width */
    }
    
    #wrapper.toggled .content {
        margin-left: 0; /* Reset margin when sidebar is hidden */
    }
    
    #menu-toggle {
        margin-bottom: 1rem;
    }
 </style>

<script>
$(function() {
    $(document).on("click", "#menu-toggle", function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
});
</script>

<div class="sidebar">
    <h5>Quick Filters</h5>
    <form>
        <label class="form-label" for="priceRange">Price Range</label>
        <input type="range" class="form-range" min="1000" max="5000" id="priceRange">
        <label class="form-label" for="roomType">Room Type</label>
        <select class="form-select" id="roomType">
            <option selected>All Types</option>
            <option>Standard Room</option>
            <option>Superior Room</option>
            <option>Deluxe Room</option>
            <option>Junior Suite Room</option>
            <option>Suite Room</option>
            <option>Presidential Suite</option>
            <option>Single Room</option>
            <option>Twin Room</option>
            <option>Deluxe Room</option>
            <option>Double Room</option>
            <option>Family Room/Triple Room</option>
            <option>Smoking/Non Smoking Room</option>
            <option>Accessible Room/Disabled Room</option>
            
        </select>
        <label class="form-label" for="amenities">Amenities</label>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="wifi">
            <label class="form-check-label" for="wifi">WiFi</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="pool">
            <label class="form-check-label" for="pool">Pool</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="breakfast">
            <label class="form-check-label" for="breakfast">Breakfast</label>
        </div>
    </form>
    <hr>
    <h5>Navigation</h5>
    <nav class="nav flex-column">
        <a class="nav-link {{ request()->routeIs('customer.index') ? 'active' : '' }}" href="{{ route('customer.index') }}">
            <i class="fas fa-home me-2"></i>Home
        </a>
        <a class="nav-link {{ request()->routeIs('customer.reservations') ? 'active' : '' }}" href="{{ route('customer.reservations') }}">
            <i class="fas fa-calendar-alt me-2"></i>My Reservations
        </a>
        <a class="nav-link" href="#">
            <i class="fas fa-tag me-2"></i>Special Offers
        </a>
        <a class="nav-link" href="#">
            <i class="fas fa-building me-2"></i>Facilities
        </a>
        <a class="nav-link" href="#">
            <i class="fas fa-envelope me-2"></i>Contact Us
        </a>
    </nav>
    <hr>
    <div class="alert alert-info mt-3 mb-0 p-2 text-center">
        <strong>Promo:</strong> 10% off for early bookings!
    </div>
</div>