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
            <small class="text-muted d-block">Date</small>
            <strong>{{ $booking->full_start_time->format('l, F j, Y') }}</strong>
        </div>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="d-flex align-items-center">
          <i class="bi bi-clock text-info me-2"></i>
          <div>
            <small class="text-muted d-block">Time</small>
            <strong>{{ $booking->full_start_time->format('g:i A') }} - {{ $booking->full_end_time->format('g:i A') }}</strong>
          </div>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="d-flex align-items-center">
          <i class="bi bi-globe text-secondary me-2"></i>
          <div>
            <small class="text-muted d-block">Timezone</small>
            <strong>{{ $booking->timezone->display_name }}</strong>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Action Buttons -->
  <div class="text-center mt-4">
    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-primary me-2">
        <i class="bi bi-calendar-event me-1"></i>View Details
      </a>
      <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-outline-primary">
        <i class="bi bi-calendar-week me-1"></i>Edit Booking
      </a>
  </div>

  <hr class="my-4">

  <p class="text-muted mb-0">
    <small>
      <i class="bi bi-info-circle me-1"></i>
      If you need to make changes, please use the links above or contact {{ $host->name }} directly.
    </small>
  </p>
@endsection
