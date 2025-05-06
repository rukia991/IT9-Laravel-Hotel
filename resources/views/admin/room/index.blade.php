@extends('template.master')

@section('title', 'Manage Hotel Rooms')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4 text-center">Manage Hotel Rooms</h1>

    <!-- Add Room Button -->
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('room.create') }}" class="btn btn-primary">Add New Room</a>
    </div>

    <!-- Rooms Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Room Number</th>
                    <th>Type</th>
                    <th>Capacity</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rooms as $room)
                    <tr>
                                                <td>{{ ($rooms->currentPage() - 1) * $rooms->perPage() + $loop->iteration }}</td>
                        <td>{{ $room->number }}</td>
                        <td>{{ $room->type->name }}</td>
                        <td>{{ $room->capacity }}</td>
                        <td>₱{{ number_format($room->price, 2) }}</td>
                        <td>{{ $room->roomStatus->name }}</td>
                        <td>
                            <a href="{{ route('room.show', $room->id) }}" class="btn btn-info btn-sm">View</a>
                            <a href="{{ route('room.edit', $room->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('room.destroy', $room->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this room?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No rooms available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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

@endsection