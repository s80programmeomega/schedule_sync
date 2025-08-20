@extends('emails.layout.base')

@section('title', 'Meeting Rescheduled - ScheduleSync')

@section('header-content')
<p class="mb-0 opacity-75">Your meeting has been rescheduled</p>
@endsection

@section('content')
<!-- Rescheduled Alert -->
<div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
    <i class="bi bi-calendar-week-fill me-3 fs-4"></i>
    <div>
        <h4 class="alert-heading mb-1">Meeting Rescheduled</h4>
        <p class="mb-0">Your meeting time has been updated</p>
    </div>
</div>

<p class="mb-4">Hi <strong>{{ $attendee['name'] }}</strong>,</p>

<p>Your meeting with <strong>{{ $host->name }}</strong> has been rescheduled to a new time.</p>

<!-- New Meeting Details -->
<div class="meeting-details-card">
    <div class="d-flex align-items-center mb-3">
        <div class="email-icon" style="background-color: rgba(34, 197, 94, 0.1); color: #059669;">
            <i class="bi bi-calendar-check"></i>
        </div>
        <h3 class="mb-0 fw-semibold text-success">New Meeting Time</h3>
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
                <i class="bi bi-calendar3 text-success me-2"></i>
                <div>
                    <small class="
