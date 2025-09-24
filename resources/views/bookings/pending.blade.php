@extends('layout.base')

@section('title', 'Pending Approvals - ScheduleSync')

@section('content')
<div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Pending Approvals</h1>
            <p class="text-muted mb-0">Review and approve booking requests</p>
        </div>
    </div>

    @if($bookings->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 px-4 py-3">Event Type</th>
                                <th class="border-0 px-4 py-3">Date & Time</th>
                                <th class="border-0 px-4 py-3">Status</th>
                                <th class="border-0 px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                            <tr>
                                <td class="px-4 py-3">{{ $booking->eventType->name }}</td>
                                <td class="px-4 py-3">
                                    <div>
                                        <div>{{ $booking->booking_date->format('M j, Y') }}</div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }}</small>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-warning">Pending</span>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $bookings->links('pagination.bootstrap') }}
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="bi bi-calendar-check display-1 text-muted"></i>
            </div>
            <h4>No Pending Requests</h4>
            <p class="text-muted">All booking requests have been reviewed.</p>
        </div>
    @endif
</div>
@endsection
