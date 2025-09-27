@extends('emails.layout.base')

@section('title', 'Meeting Reminder - ScheduleSync')

@section('header-content')
<p class="mb-0 opacity-75">
    Your meeting is {{ $reminderType === '24h' ? 'tomorrow' : 'starting soon' }}!
</p>
@endsection

@section('content')
<!-- Reminder Alert -->
<div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
    <i class="bi bi-bell-fill me-3 fs-4"></i>
    <div>
        <h4 class="alert-heading mb-1">Meeting Reminder</h4>
        <p class="mb-0">Your meeting starts <strong>{{ $timeUntil }}</strong></p>
    </div>
</div>

<p class="mb-4">Hi <strong>{{ $booking->attendee_name }}</strong>,</p>

<!-- Quick Meeting Info Card -->
<div class="meeting-details-card">
    <div class="row g-3 align-items-center">
        <div class="col-auto">
            <div class="email-icon">
                <i class="bi bi-calendar-event"></i>
            </div>
        </div>
        <div class="col">
            <h5 class="mb-1">{{ $booking->eventType->name }}</h5>
            <p class="text-muted mb-1">
                <i class="bi bi-person me-1"></i>with {{ $booking->eventType->user->name }}
            </p>
            <p class="text-muted mb-1">
                <i class="bi bi-calendar3 me-1"></i>{{ $formattedDate }}
            </p>
            <p class="text-muted mb-0">
                <i class="bi bi-clock me-1"></i>{{ $formattedTime }}
                ({{ $booking->eventType->duration }} minutes)
            </p>
        </div>
    </div>
</div>

@if($joinLink)
<!-- Join Button -->
<div class="text-center my-4">
    <a href="{{ $joinLink }}" class="btn btn-primary btn-lg">
        <i class="bi bi-camera-video me-2"></i>Join Meeting Now
    </a>
</div>
@endif

@if($reminderType === '24h')
<!-- Preparation Tips for 24h reminder -->
<div class="alert alert-light border-warning" role="alert">
    <div class="d-flex align-items-start">
        <div class="email-icon me-3" style="background-color: rgba(255, 193, 7, 0.1); color: #f59e0b;">
            <i class="bi bi-lightbulb"></i>
        </div>
        <div>
            <h6 class="alert-heading">Preparation Tips</h6>
            <ul class="mb-0 ps-3">
                <li>Test your camera and microphone</li>
                <li>Find a quiet, well-lit space</li>
                <li>Prepare any questions or materials</li>
                <li>Check your internet connection</li>
            </ul>
        </div>
    </div>
</div>
@endif

<hr class="my-4">

<!-- Cancel Option -->
<div class="text-center">
    <p class="text-muted mb-3">Need to cancel or reschedule?</p>
    <a href="{{ $cancelLink }}" class="btn btn-outline-primary">
        <i class="bi bi-x-circle me-1"></i>Cancel Meeting
    </a>
</div>

<div class="mt-4 text-center">
    <p class="mb-0">See you soon!</p>
    <p class="fw-semibold">{{ $booking->eventType->user->name }}</p>
</div>
@endsection
