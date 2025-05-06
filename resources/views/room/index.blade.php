@extends('template.master')
@section('title', 'Room')
@section('head')
    <style>
        .text {
            display: block;
            width: 150px;
            height: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row mt-2 mb-2">
                <div class="col-lg-12 mb-2">
                    <div class="d-grid gap-2 d-md-block">
                        <a href="{{ route('room.create') }}" class="btn btn-sm shadow-sm myBtn border rounded">
                            <svg width="25" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                viewBox="0 0 24 24" stroke="black">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add Room
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow-sm border">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select id="status" class="form-select" aria-label="Choose status">
                                            <option selected>All</option>
                                            @forelse ($roomStatuses as $roomStatus)
                                                <option value="{{ $roomStatus->id }}">{{ $roomStatus->name }}</option>
                                            @empty
                                                <option value="">No room status</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Type</label>
                                        <select id="type" class="form-select" aria-label="Choose type">
                                            <option selected>All</option>
                                            @forelse ($types as $type)
                                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                            @empty
                                                <option value="">No type</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <hr>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="room-table" class="table table-sm table-hover" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th scope="col">Number</th>
                                            <th scope="col">Type</th>
                                            <th scope="col">Capacity</th>
                                            <th scope="col">Price / Day</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($rooms as $room)
                                            <tr>
                                                <td>{{ $room->number }}</td>
                                                <td>{{ $room->type->name }}</td>
                                                <td>{{ $room->capacity }}</td>
                                                <td>₱{{ number_format($room->price, 2) }}</td>
                                                <td>{{ $room->roomStatus->name ?? 'N/A' }}</td>
                                                <td>
                                                    <a href="{{ route('room.show', $room->id) }}" class="btn btn-info btn-sm">View</a>
                                                    <a href="{{ route('room.edit', $room->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                                    <form action="{{ route('room.destroy', $room->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No rooms available.</td>
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
                        <div class="card-footer">
                            <h3>Room</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection