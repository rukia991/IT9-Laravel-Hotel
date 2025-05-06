@extends('template.master')
@section('title', 'Customer')
@section('content')

@php
    // Sample static data (remove this if you already get data from controller)
    $recommendedRooms = [
        (object)[
            'id' => 1,
            'image' => 'https://picsum.photos/id/1018/1200/600',
            'name' => 'Deluxe Suite',
            'description' => 'A spacious suite with a beautiful view and modern amenities.',
            'price' => 3500,
        ],
        (object)[
            'id' => 2,
            'image' => 'https://picsum.photos/id/1025/1200/600',
            'name' => 'Standard Room',
            'description' => 'Comfortable and affordable, perfect for short stays.',
            'price' => 2200,
        ],
        (object)[
            'id' => 3,
            'image' => 'https://picsum.photos/id/1035/1200/600',
            'name' => 'Family Room',
            'description' => 'Spacious room ideal for families, with extra beds and amenities.',
            'price' => 4000,
        ],
    ];
@endphp

<style>
    .carousel-inner {
        aspect-ratio: 16 / 9;
        width: 100%;
        max-height: 500px;
        min-height: 300px;
    }
    .carousel-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .carousel-caption {
        background: rgba(0, 0, 0, 0.5);
        border-radius: 1rem;
        padding: 1.5rem;
        left: 10%;
        right: 10%;
        bottom: 18%;
    }
    .carousel-caption h5,
    .carousel-caption p,
    .carousel-caption .price {
        color: #fff;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
    }
    .carousel-caption .price {
        font-size: 1.2rem;
        font-weight: bold;
        margin-top: 0.5rem;
    }

    .card {
        height: 100%;
        min-height: 420px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .card img {
        max-height: 200px;
        object-fit: cover;
        width: 100%;
    }

    .card-body {
        flex-grow: 1;
    }

    /* Hide giant unwanted arrows */
    .carousel + div .carousel-control-prev,
    .carousel + div .carousel-control-next,
    .slick-arrow,
    .swiper-button-next,
    .swiper-button-prev {
        display: none !important;

        .pagination .page-link {
    color: #007bff;
    border: 1px solid #dee2e6;
    margin: 0 2px;
}

.pagination .page-item.active .page-link {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}

    }
</style>

<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        @include('template.include._customer_sidebar')
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="container mt-4">
            <h1 class="mb-4 text-center">Welcome to Our Hotel</h1>

            <!-- Bootstrap Carousel -->
            <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach($recommendedRooms as $index => $room)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <img src="{{ $room->image }}" class="d-block w-100" alt="{{ $room->name }}">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>{{ $room->name }}</h5>
                                <p>{{ $room->description }}</p>
                                <div class="price">₱{{ number_format($room->price, 2) }}</div>
                                <a href="#" class="btn btn-primary mt-2">View Room</a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>

<!-- Rooms Section -->
<div class="container mt-4 mb-4">
    <div class="row">
        @foreach($rooms as $room)
            <div class="col-md-4 mb-4 d-flex align-items-stretch">
                <div class="card shadow-sm w-100">
                    <img src="{{ $room->image }}"
                         onerror="this.onerror=null; this.src='https://placehold.co/300x200?text=No+Image';"
                         class="card-img-top"
                         alt="Room Image"
                         style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $room->name }}</h5>
                        <p class="card-text flex-grow-1">{{ $room->description }}</p>
                        <div class="price mb-2 fw-bold">₱{{ number_format($room->price, 2) }}</div>
                        <a href="{{ route('room.show', $room->id) }}" class="btn btn-primary mt-auto">View Room</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if ($rooms->hasPages())
        <nav class="mt-4 d-flex justify-content-center">
            <ul class="pagination">
                @if ($rooms->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $rooms->previousPageUrl() }}" rel="prev">&laquo;</a></li>
                @endif

                @foreach ($rooms->links()->elements[0] as $page => $url)
                    @if ($page == $rooms->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach

                @if ($rooms->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $rooms->nextPageUrl() }}" rel="next">&raquo;</a></li>
                @else
                    <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                @endif
            </ul>
        </nav>
    @endif
</div>

        <!-- Footer -->
        <footer class="bg-dark text-white text-center py-3 mt-5">
            <p>&copy; {{ date('Y') }} Our Hotel. All rights reserved.</p>
            <p>
                Follow us on
                <a href="#" class="text-white text-decoration-none">Facebook</a>,
                <a href="#" class="text-white text-decoration-none">Twitter</a>,
                <a href="#" class="text-white text-decoration-none">Instagram</a>
            </p>
        </footer>
    </div>
</div>
</div>
@endsection
