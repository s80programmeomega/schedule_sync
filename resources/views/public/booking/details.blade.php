@extends('layout.base')

@section('title', 'Booking Details')

@section('content')
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-8 py-2">
        <div class="card">
          <div class="card-header">
            <h4>Booking Details</h4>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <strong>Event:</strong> {{ $booking->eventType->name }}<br>
                <strong>Date:</strong> {{ $booking->booking_date->format('F d, Y') }}<br>
                <strong>Time:</strong> {{ $booking->start_time }} - {{ $booking->end_time }}<br>
                <strong>Duration:</strong> {{ $booking->eventType->duration }} minutes
              </div>
              <div class="col-md-6">
                <strong>Status:</strong>
                <span class="badge bg-{{ $booking->status === 'scheduled' ? 'success' : 'secondary' }}">
                  {{ ucfirst($booking->status) }}
                </span><br>
                <strong>Host:</strong> {{ $user->name }}
              </div>
            </div>
          </div>
        </div>
      </div>
      @if ($booking->status === 'scheduled')
          <div class="col-md-8 py-2">
            <div class="card">
              <div class="card-body text-center">
                <h5>Want to join this meeting?</h5>
                <p class="text-muted">Add yourself as an attendee to this scheduled meeting.</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#joinModal">
                  <i class="fas fa-user-plus me-2"></i>Join as Attendee
                </button>
              </div>
            </div>
          </div>

        <!-- Join Modal -->
        <div class="modal fade" id="joinModal" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">
              <form method="POST"
                action="{{ route('public.booking.join', ['username' => $user->username, 'booking' => $booking->id]) }}">
                @csrf
                <div class="modal-header">
                  <h5 class="modal-title">Join Meeting</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <div class="mb-3">
                    <label for="name" class="form-label">Your Name *</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                  </div>
                  <div class="mb-3">
                    <label for="email" class="form-label">Your Email *</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                  </div>
                  <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" name="phone">
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary">Join Meeting</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      @endif

      @if ($booking->status === 'scheduled' && $booking->isApproved() && now()->between($booking->full_start_time, $booking->full_end_time))
      <div class="col-md-8 py-2">
          <div class="card mt-3">
            <div class="card-header">
              <h5 class="mb-0"><i class="fas fa-video me-2"></i>Join Meeting</h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <strong>Location:</strong> {{ ucfirst(str_replace('_', ' ', $booking->eventType->location_type)) }}<br>
                  @if ($booking->eventType->location_details)
                    <strong>Details:</strong> {{ $booking->eventType->location_details }}<br>
                  @endif
                </div>
                <div class="col-md-6">
                  @if ($booking->eventType->location_type === 'zoom' && $booking->meeting_link)
                    <a href="{{ $booking->meeting_link }}" target="_blank" class="btn btn-primary">
                      <i class="fab fa-zoom me-2"></i>Join Zoom Meeting
                    </a>
                  @elseif($booking->eventType->location_type === 'google_meet' && $booking->meeting_link)
                    <a href="{{ $booking->meeting_link }}" target="_blank" class="btn btn-success">
                      <i class="fab fa-google me-2"></i>Join Google Meet
                    </a>
                  @elseif($booking->eventType->location_type === 'phone')
                    <button class="btn btn-info" onclick="window.open('tel:{{ $booking->eventType->location_details }}')">
                      <i class="fas fa-phone me-2"></i>Call Now
                    </button>
                  @elseif($booking->eventType->location_type === 'whatsapp')
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $booking->eventType->location_details) }}"
                      target="_blank" class="btn btn-success">
                      <i class="fab fa-whatsapp me-2"></i>Join WhatsApp
                    </a>
                  @else
                    <div class="alert alert-info mb-0">
                      <small>Meeting details will be shared closer to the meeting time.</small>
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @elseif($booking->isPendingApproval())
          <div class="alert alert-warning mt-3">
            <i class="fas fa-clock me-2"></i>This booking is pending approval. Join details will be available once approved.
          </div>
        @endif
      </div>
    </div>


  </div>
@endsection
