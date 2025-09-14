@extends('emails.layout.base')

@section('title', 'Booking Confirmed - ScheduleSync')

@section('header-content')
<p class="mb-0 opacity-75">Your meeting has been confirmed!</p>
@endsection

@section('content')
<!-- Success Alert using Bootstrap -->
<div class="alert alert-success d-flex align-items-center mb-4" role="alert">
    <i class="bi bi-check-circle-fill me-3 fs-4"></i>
    <div>
        <h4 class="alert-heading mb-1">Booking Confirmed!</h4>
        <p class="mb-0">Your meeting with <strong>{{ $host->name }}</strong> is all set.</p>
    </div>
</div>

<p class="mb-4">Hi <strong>{{ $attendee['name'] }}</strong>,</p>

<!-- Meeting Details Card using your card styling -->
<div class="meeting-details-card">
    <div class="d-flex align-items-center mb-3">
        <div class="email-icon">
            <i class="bi bi-calendar-event"></i>
        </div>
        <h3 class="mb-0 fw-semibold">Meeting Details</h3>
    </div>

    <div class="row g-3">
        <div class="col-sm-6">
            <div class="d-flex align-items-center">
                <i class="bi bi-bookmark text-primary me-2"></i>
                <div>
                    <small class="text-muted d-block">Event Type</small>
                    <strong>{{ $eventType->name }}</strong>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="d-flex align-items-center">
                <i class="bi bi-calendar3 text-primary me-2"></i>
                <div>
                    <small class="text-muted d-block">Date</small>
                    <strong>{{ $meetingDetails['date'] }}</strong>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="d-flex align-items-center">
                <i class="bi bi-clock text-primary me-2"></i>
                <div>
                    <small class="text-muted d-block">Time</small>
                    <strong>{{ $meetingDetails['time']}}</strong> - {{ $meetingDetails['timezone'] }}
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="d-flex align-items-center">
                <i class="bi bi-hourglass text-primary me-2"></i>
                <div>
                    <small class="text-muted d-block">Duration</small>
                    <strong>{{ $meetingDetails['duration'] }}</strong>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="d-flex align-items-center">
                <i class="bi bi-geo-alt text-primary me-2"></i>
                <div>
                    <small class="text-muted d-block">Location</small>
                    <strong>
                        @if($eventType->location_type === 'zoom')
                        <i class="bi bi-camera-video me-1"></i>Zoom Meeting
                        @elseif($eventType->location_type === 'google_meet')
                        <i class="bi bi-google me-1"></i>Google Meet
                        @elseif($eventType->location_type === 'phone')
                        <i class="bi bi-telephone me-1"></i>Phone Call
                        @else
                        {{ ucfirst($eventType->location_type) }}
                        @endif
                    </strong>
                </div>
            </div>
        </div>
    </div>

    @if($booking->attendee_notes)
    <div class="mt-4 p-3 bg-white rounded border-start border-primary border-3">
        <small class="text-muted d-block mb-1">Your Notes:</small>
        <em>"{{ $booking->attendee_notes }}"</em>
    </div>
    @endif
</div>

@if($joinLink)
<!-- Join Meeting Button using your button styling -->
<div class="text-center my-4">
    <a href="{{ $joinLink }}" class="btn btn-primary btn-lg">
        <i class="bi bi-camera-video me-2"></i>Join Meeting
    </a>
</div>
@endif

<hr class="my-4">

<!-- Calendar Integration -->
<div class="row align-items-center mb-4">
    <div class="col-auto">
        <div class="email-icon">
            <i class="bi bi-calendar-plus"></i>
        </div>
    </div>
    <div class="col">
        <h5 class="mb-1">Add to Calendar</h5>
        <p class="text-muted mb-0">
            A calendar invite (.ics file) is attached to this email.
            Click on it to add this meeting to your calendar.
        </p>
    </div>
</div>

<!-- Action Buttons -->
<div class="row align-items-center mb-4">
    <div class="col-auto">
        <div class="email-icon">
            <i class="bi bi-gear"></i>
        </div>
    </div>
    <div class="col">
        <h5 class="mb-2">Need to Make Changes?</h5>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ $rescheduleLink }}" class="btn btn-outline-primary">
                <i class="bi bi-calendar-week me-1"></i>Reschedule
            </a>
            <a href="{{ $cancelLink }}" class="btn btn-outline-primary">
                <i class="bi bi-x-circle me-1"></i>Cancel
            </a>
        </div>
    </div>
</div>

<hr class="my-4">

<!-- What's Next Section -->
<div class="alert alert-info" role="alert">
    <h6 class="alert-heading">
        <i class="bi bi-info-circle me-2"></i>What's Next?
    </h6>
    <ul class="mb-0 ps-3">
        <li>You'll receive a reminder email 24 hours before the meeting</li>
        <li>Another reminder will be sent 1 hour before</li>
        <li>Use the join link above when it's time for your meeting</li>
    </ul>
</div>

<div class="mt-4">
    <p class="mb-2">Looking forward to meeting with you!</p>
    <p class="mb-0">
        <strong>{{ $host->name }}</strong><br>
        <a href="mailto:{{ $host->email }}" class="text-decoration-none" style="color: var(--primary);">
            {{ $host->email }}
        </a>
    </p>
</div>
@endsection
