@extends('layout.base')

@section('title', 'Bookings - ScheduleSync')

@section('content')
<div class="col-lg-10 col-12 py-4 px-4 px-lg-5" data-aos="fade-up" data-aos-duration="1000">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div>
                <h1 class="h3 mb-0 fw-bold">{{ $viewType === 'scheduled' ? 'Scheduled Events' : 'All Bookings' }}</h1>
                <p class="text-muted mb-0">{{ $viewType === 'scheduled' ? 'Manage your upcoming appointments' : 'Manage all your
                    appointments' }}</p>
            </div>
        </div>
        <a href="{{ route('bookings.create') }}" class="btn btn-primary d-flex align-items-center">
            <i class="bi bi-calendar-plus me-2"></i> New Booking
        </a>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @forelse($bookings as $booking)
            <div class="booking-item border-bottom p-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="booking-status me-3">
                                <span
                                    class="badge bg-{{ $booking->status === 'scheduled' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : 'secondary') }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-semibold">{{ $booking->attendee_name }}</h6>
                                <p class="text-muted mb-0 small">{{ $booking->eventType->name }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">
                            <i
                                class="bi bi-calendar{{ $booking->status === 'completed' ? '-check' : ($booking->status === 'cancelled' ? '-x' : '') }} me-1"></i>
                            {{ $booking->full_start_time->format('M j, Y \a\t g:i A') }}
                            <br><small class="text-muted">{{ $booking->timezone->display_name ?? 'UTC' }}</small>
                        </div>
                    </div>
                    <div class="col-md-2 text-end">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('bookings.show', $booking) }}">
                                        <i class="bi bi-eye me-2"></i>View</a></li>
                                <li><a class="dropdown-item" href="{{ route('bookings.edit', $booking) }}">
                                        <i class="bi bi-pencil me-2"></i>Edit</a></li>
                                @if($booking->status === 'scheduled')
                                <li><a class="dropdown-item text-warning" href="#"
                                        onclick="cancelBooking({{ $booking->id }})">
                                        <i class="bi bi-x-circle me-2"></i>Cancel</a></li>
                                @endif
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('bookings.destroy', $booking) }}">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="redirect_to" value="{{ $viewType }}">
                                        <button type="submit"
                                            class="dropdown-item text-danger border-0 bg-transparent w-100 text-start"
                                            onclick="return confirm('Delete this booking?')">
                                            <i class="bi bi-trash me-2"></i>Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3 mb-2">{{ $viewType === 'scheduled' ? 'No Scheduled Events' : 'No Bookings Yet' }}</h4>
                <p class="text-muted mb-4">{{ $viewType === 'scheduled' ? 'You have no upcoming appointments' : 'Create your first
                    booking to get started' }}</p>
                <a href="{{ route('bookings.create') }}" class="btn btn-primary">
                    <i class="bi bi-calendar-plus me-2"></i> New Booking
                </a>
            </div>
            {{-- <div class="text-center py-5">
                <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3 mb-2">No Bookings Yet</h4>
                <p class="text-muted mb-4">Create your first booking to get started</p>
                <a href="{{ route('bookings.create') }}" class="btn btn-primary">
                    <i class="bi bi-calendar-plus me-2"></i> New Booking
                </a>
            </div> --}}
            @endforelse
        </div>
    </div>
</div>
@if($bookings->hasPages())
    <div class="mt-4">
        {{ $bookings->links('pagination.bootstrap') }}
    </div>
@endif

<script>
    function cancelBooking(id) {
            const reason = prompt('Cancellation reason (optional):');
            if (reason !== null) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/bookings/${id}/cancel`;
                form.innerHTML = `
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="cancellation_reason" value="${reason}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
</script>
@endsection
