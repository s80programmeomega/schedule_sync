{{-- In resources/views/emails/booking/confirmation.blade.php --}}

@extends('emails.layout.base')

@section('title', 'Booking Confirmed - ScheduleSync')

@section('header-content')
<p class="mb-0 opacity-75">
    @if($isForAttendee)
        Your meeting has been confirmed!
    @else
        New booking received!
    @endif
</p>
@endsection

@section('content')
<div class="alert alert-success d-flex align-items-center mb-4" role="alert">
    <i class="bi bi-check-circle-fill me-3 fs-4"></i>
    <div>
        <h4 class="alert-heading mb-1">Booking Confirmed!</h4>
        <p class="mb-0">
            @if($isForAttendee)
                Your meeting with <strong>{{ $host->name }}</strong> is all set.
            @else
                You have a new booking from <strong>{{ $allAttendees->first()->name ?? 'a client' }}</strong>.
            @endif
        </p>
    </div>
</div>

<p class="mb-4">
    @if($isForAttendee)
        Hi <strong>{{ $attendee['name'] }}</strong>,
    @else
        Hi <strong>{{ $host->name }}</strong>,
    @endif
</p>

{{-- Meeting Details --}}
<div class="meeting-details-card">
    <div class="d-flex align-items-center mb-3">
        <div class="email-icon">
            <i class="bi bi-calendar-event"></i>
        </div>
        <h3 class="mb-0 fw-semibold">Meeting Details</h3>
    </div>

    {{-- Existing meeting details code --}}

    {{-- Show all attendees if there are multiple --}}
    @if($allAttendees->count() > 1)
    <div class="col-12 mt-3">
        <div class="d-flex align-items-start">
            <i class="bi bi-people text-primary me-2"></i>
            <div>
                <small class="text-muted d-block">Attendees ({{ $allAttendees->count() }})</small>
                @foreach($allAttendees as $attendeeItem)
                    <div class="mb-1">
                        <strong>{{ $attendeeItem->name }}</strong>
                        <small class="text-muted">({{ $attendeeItem->email }})</small>
                        @if($attendeeItem->role === 'organizer')
                            <span class="badge bg-primary">Organizer</span>
                        @elseif($attendeeItem->role === 'optional')
                            <span class="badge bg-secondary">Optional</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Rest of your existing template --}}
@endsection
