@extends('layout.base')

@section('title', 'Booking Details - ScheduleSync')

@section('content')
<div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('bookings.index') }}" class="btn btn-light me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 mb-0 fw-bold">Booking Details</h1>
            <p class="text-muted mb-0">{{ $booking->attendee_name }}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-sm-3 fw-semibold">Status:</div>
                        <div class="col-sm-9">
                            <span
                                class="badge bg-{{ $booking->status === 'scheduled' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : 'secondary') }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-sm-3 fw-semibold">Event Type:</div>
                        <div class="col-sm-9">{{ $booking->eventType->name }}</div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-sm-3 fw-semibold">Attendee:</div>
                        <div class="col-sm-9">
                            {{ $booking->attendee_name }}<br>
                            <small class="text-muted">{{ $booking->attendee_email }}</small>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-sm-3 fw-semibold">Date & Time:</div>
                        <div class="col-sm-9">{{ $booking->full_start_time->format('M j, Y \a\t g:i A') }}</div>
                    </div>

                    @if($booking->meeting_link)
                    <div class="row mb-4">
                        <div class="col-sm-3 fw-semibold">Meeting Link:</div>
                        <div class="col-sm-9">
                            <a href="{{ $booking->meeting_link }}" target="_blank"
                                class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-camera-video me-1"></i> Join Meeting
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($booking->attendee_notes)
                    <div class="row mb-4">
                        <div class="col-sm-3 fw-semibold">Notes:</div>
                        <div class="col-sm-9">{{ $booking->attendee_notes }}</div>
                    </div>
                    @endif

                    @if($booking->cancellation_reason)
                    <div class="row mb-4">
                        <div class="col-sm-3 fw-semibold">Cancellation Reason:</div>
                        <div class="col-sm-9 text-danger">{{ $booking->cancellation_reason }}</div>
                    </div>
                    @endif

                    <div class="d-flex gap-2">
                        <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-2"></i> Edit
                        </a>
                        @if($booking->status === 'scheduled')
                        <button class="btn btn-warning" onclick="cancelBooking({{ $booking->id }})">
                            <i class="bi bi-x-circle me-2"></i> Cancel
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
