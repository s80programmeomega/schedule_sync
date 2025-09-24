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
        <p class="text-muted mb-0">{{ $booking->eventType->name }}</p>
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
              <div class="col-sm-3 fw-semibold">Duration:</div>
              <div class="col-sm-9">{{ $booking->eventType->duration }} minutes</div>
            </div>

            <div class="row mb-4">
              <div class="col-sm-3 fw-semibold">Attendees:</div>
              <div class="col-sm-9">
                @if ($booking->attendees->count() > 0)
                  <div class="attendees-list">
                    @foreach ($booking->attendees->take(3) as $attendee)
                      <div class="d-flex align-items-center mb-2">
                        <div class="avatar-sm me-2">
                          <div
                            class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px;">
                            {{ substr($attendee->name, 0, 1) }}
                          </div>
                        </div>
                        <div class="flex-grow-1">
                          <div class="fw-semibold">{{ $attendee->name }}</div>
                          <small class="text-muted">{{ $attendee->email }}</small>
                        </div>
                        <div>
                          <span
                            class="badge bg-{{ $attendee->role === 'organizer' ? 'primary' : ($attendee->role === 'required' ? 'success' : 'secondary') }} me-1">
                            {{ ucfirst($attendee->role) }}
                          </span>
                          <span
                            class="badge bg-{{ $attendee->status === 'accepted' ? 'success' : ($attendee->status === 'declined' ? 'danger' : 'warning') }}">
                            {{ ucfirst($attendee->status) }}
                          </span>
                        </div>
                      </div>
                    @endforeach
                    @if ($booking->attendees->count() > 3)
                      <div class="text-muted small">
                        and {{ $booking->attendees->count() - 3 }} more attendees
                      </div>
                    @endif
                    <button class="btn btn-sm btn-outline-primary mt-2" onclick="showAttendees({{ $booking->id }})">
                      <i class="bi bi-people me-1"></i> View All Attendees
                    </button>
                  </div>
                @else
                  <span class="text-muted">No attendees</span>
                @endif
              </div>
            </div>

            <div class="row mb-4">
              <div class="col-sm-3 fw-semibold">Date & Time:</div>
              <div class="col-sm-9">
                {{ $booking->full_start_time->format('M j, Y \\a\\t g:i A') }}
                <br><small class="text-muted">{{ $booking->timezone->display_name ?? 'UTC' }}</small>
              </div>
            </div>

            @if ($booking->meeting_link)
              <div class="row mb-4">
                <div class="col-sm-3 fw-semibold">Meeting Link:</div>
                <div class="col-sm-9">
                  <a href="{{ $booking->meeting_link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-camera-video me-1"></i> Join Meeting
                  </a>
                </div>
              </div>
            @endif

            @if ($booking->cancellation_reason)
              <div class="row mb-4">
                <div class="col-sm-3 fw-semibold">Cancellation Reason:</div>
                <div class="col-sm-9 text-danger">{{ $booking->cancellation_reason }}</div>
              </div>
            @endif

            <div class="d-flex gap-2">
              <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i> Edit
              </a>
              @if ($booking->status === 'scheduled')
                <button class="btn btn-warning" onclick="cancelBooking({{ $booking->id }})">
                  <i class="bi bi-x-circle me-2"></i> Cancel
                </button>
              @endif
            </div>
            @if ($booking->isPendingApproval() && $booking->user_id === auth()->id())
              <div class="mt-3 pt-3 border-top">
                <h5>Approval Required</h5>
                <div class="d-flex gap-2">
                  <form method="POST" action="{{ route('bookings.approve', $booking) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                      <i class="bi bi-check-circle me-2"></i> Approve
                    </button>
                  </form>
                  <button class="btn btn-danger" onclick="rejectBooking({{ $booking->id }})">
                    <i class="bi bi-x-circle me-2"></i> Reject
                  </button>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  @include('partials.attendee-details-modal')

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

    function showAttendees(bookingId) {
      fetch(`/bookings/${bookingId}/attendees`)
        .then(response => response.json())
        .then(attendees => {
          const modal = document.getElementById('attendeesModal');
          const tbody = modal.querySelector('#attendees-list');
          tbody.innerHTML = '';

          attendees.forEach(attendee => {
            const row = `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-2">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        ${attendee.name.charAt(0).toUpperCase()}
                                    </div>
                                </div>
                                <div>
                                    <div class="fw-semibold">${attendee.name}</div>
                                    <small class="text-muted">${attendee.email}</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-${attendee.role === 'organizer' ? 'primary' : attendee.role === 'required' ? 'success' : 'secondary'}">${attendee.role}</span></td>
                        <td><span class="badge bg-${attendee.status === 'accepted' ? 'success' : attendee.status === 'declined' ? 'danger' : 'warning'}">${attendee.status}</span></td>
                    </tr>
                `;
            tbody.innerHTML += row;
          });

          new bootstrap.Modal(modal).show();
        });
    }

    function rejectBooking(id) {
      const reason = prompt('Rejection reason (optional):');
      if (reason !== null) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/bookings/${id}/reject`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="rejection_reason" value="${reason}">
        `;
        document.body.appendChild(form);
        form.submit();
      }
    }
  </script>
@endsection
