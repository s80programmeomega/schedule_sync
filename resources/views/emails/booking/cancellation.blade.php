@extends('emails.layout.base')

@section('title', 'Meeting Cancelled - ScheduleSync')

@section('header-content')
<p class="mb-0 opacity-75">Meeting has been cancelled</p>
@endsection

@section('content')
<!-- Cancellation Alert -->
<div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
    <i class="bi bi-x-circle-fill me-3 fs-4"></i>
    <div>
        <h4 class="alert-heading mb-1">Meeting Cancelled</h4>
        <p class="mb-0">
            This meeting was cancelled by the
            {{ $cancelledBy === 'host' ? 'host' : 'attendee' }}
        </p>
    </div>
</div>

<p class="mb-4">Hi <strong>{{ $booking->attendee_name }}</strong>,</p>

<!-- Cancelled Meeting Details -->
<div class="meeting-details-card">
    <div class="row g-3 align-items-center">
        <div class="col-auto">
            <div class="email-icon" style="background-color: rgba(239, 68, 68, 0.1); color: #dc2626;">
                <i class="bi bi-calendar-x"></i>
            </div>
        </div>
        <div class="col">
            <h5 class="mb-1 text-decoration-line-through text-muted">
                {{ $booking->eventType->name }}
            </h5>
            <p class="text-muted mb-1">
                <i class="bi bi-person me-1"></i>with {{ $booking->eventType->user->name }}
            </p>
            <p class="text-muted mb-1">
                <i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($booking->booking_date)->format('l, F j, Y') }}
            </p>
            <p class="text-muted mb-0">
                <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A T') }}
            </p>
        </div>
    </div>

    @if($reason)
    <div class="mt-3 p-3 bg-white rounded border-start border-danger border-3">
        <small class="text-muted d-block mb-1">Cancellation Reason:</small>
        <em>"{{ $reason }}"</em>
    </div>
    @endif
</div>

<hr class="my-4">

<!-- Rebook Section -->
<div class="text-center">
    <div class="email-icon mx-auto mb-3" style="background-color: rgba(34, 197, 94, 0.1); color: #059669;">
        <i class="bi bi-calendar-plus"></i>
    </div>
    <h5 class="mb-3">Want to Schedule Another Meeting?</h5>
    <p class="text-muted mb-4">
        You can easily book a new appointment at your convenience.
    </p>
    <a href="{{ $rebookLink }}" class="btn btn-primary">
        <i class="bi bi-calendar-plus me-2"></i>Book New Meeting
    </a>
</div>

<div class="mt-4">
    <p class="mb-0">
        If you have any questions, feel free to reach out.<br>
        <strong>{{ $booking->eventType->user->name }}</strong>
    </p>
</div>
@endsection
