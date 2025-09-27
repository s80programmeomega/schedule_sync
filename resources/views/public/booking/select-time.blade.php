@extends('layout.base')

@section('title', 'Book ' . $eventType->name . ' with ' . $user->name)

@section('content')
  <div class="container mt-4">
    @if (session('message'))
      <div class="alert alert-info mb-3">
        {{ session('message') }}
      </div>
    @endif
    <div class="row justify-content-center">
      <div class="col-md-10">
        {{-- Header --}}
        <div class="d-flex align-items-center mb-4">
          <a href="{{ route('public.booking.index', $user->username) }}" class="btn btn-light me-3">
            <i class="fas fa-arrow-left"></i>
          </a>
          <div>
            <h1 class="h3 mb-0 fw-bold">Book: {{ $eventType->name }}</h1>
            <small class="text-muted">{{ $eventType->duration }} minutes with {{ $user->name }}</small>
          </div>
        </div>

        @if ($eventType->requires_approval)
          <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle"></i> This booking requires approval from {{ $user->name }}.
          </div>
        @endif

        <div class="row">
          {{-- Date Selection --}}
          <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
              <div class="card-header">
                <h5 class="mb-0">Select Date</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <label for="booking_date" class="form-label">Available Dates</label>
                  <input type="date" class="form-control" id="booking_date"
                    value="{{ $selectedDate->format('Y-m-d') }}" min="{{ now()->format('Y-m-d') }}"
                    onchange="changeDate(this.value)">
                </div>

                @if (!empty($availableDates))
                  <div class="mb-3">
                    <small class="text-muted">Quick select:</small>
                    <div class="d-flex flex-wrap gap-1 mt-1">
                      @foreach (array_slice($availableDates, 0, 7) as $date)
                        @php $dateObj = \Carbon\Carbon::parse($date); @endphp
                        <button type="button"
                          class="btn btn-sm {{ $date === $selectedDate->format('Y-m-d') ? 'btn-primary' : 'btn-outline-primary' }}"
                          onclick="changeDate('{{ $date }}')">
                          {{ $dateObj->format('M j') }}
                        </button>
                      @endforeach
                    </div>
                  </div>
                @endif
              </div>
            </div>
          </div>

          {{-- Availability & Time Slots --}}
          <div class="col-md-8">
            @if ($availability)
              {{-- Availability Details --}}
              <div class="card border-0 shadow-sm mb-4">
                <div class="card-header">
                  <h5 class="mb-0">Availability for {{ $selectedDate->format('F d, Y') }}</h5>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <label class="form-label fw-semibold">Available Time</label>
                      <p class="mb-0">{{ $availability->start_time }} - {{ $availability->end_time }}</p>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-semibold">Time Zone</label>
                      <p class="mb-0">{{ $availability->timezone->display_name }}</p>
                    </div>
                  </div>
                </div>
              </div>

              {{-- Time Slots --}}
              <div class="card border-0 shadow-sm">
                <div class="card-header">
                  <h5 class="mb-0">Available Time Slots</h5>
                  <small class="text-muted">Click on an available slot to book</small>
                </div>
                <div class="card-body">
                  @if (!empty($timeSlots))
                    <div class="row g-2">
                      @foreach ($timeSlots as $slot)
                        <div class="col-md-4 col-sm-6">
                          @if ($slot['is_occupied'])
                            @php
                              $statusColor = match ($slot['booking']->status) {
                                  'scheduled' => 'btn-secondary',
                                  'completed' => 'btn-success',
                                  'cancelled' => 'btn-danger',
                                  'no_show' => 'btn-warning',
                                  default => 'btn-secondary',
                              };
                            @endphp
                            <a href="{{ route('public.booking.details', ['username' => $user->username, 'booking' => $slot['booking']->id]) }}"
                              target="_blank" class="btn {{ $statusColor }} btn-sm w-100 text-decoration-none"
                              title="View Booking Details">
                              {{ $slot['formatted_time'] }}
                              <small class="d-block">Booked - <i class="bi bi-box-arrow-up-right ms-1"></i></small>
                            </a>
                          @else
                            <button type="button" class="btn btn-outline-primary btn-sm w-100 time-slot-btn"
                              data-start-time="{{ $slot['start_time'] }}" data-end-time="{{ $slot['end_time'] }}"
                              data-formatted-time="{{ $slot['formatted_time'] }}" onclick="selectTimeSlot(this)"
                              title="Book {{ $eventType->name }} at {{ $slot['formatted_time'] }}">
                              {{ $slot['formatted_time'] }}
                            </button>
                          @endif
                        </div>
                      @endforeach
                    </div>
                  @else
                    <div class="text-center py-4">
                      <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                      <h6 class="text-muted">No available time slots</h6>
                      <p class="text-muted">All slots for this date are booked or unavailable.</p>
                    </div>
                  @endif
                </div>
              </div>
            @else
              {{-- No Availability --}}
              <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                  <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                  <h5 class="text-muted">No Availability</h5>
                  <p class="text-muted">{{ $user->name }} is not available on {{ $selectedDate->format('F d, Y') }}.
                  </p>
                  <p class="text-muted">Please select a different date.</p>
                </div>
              </div>
            @endif
          </div>
        </div>

        {{-- Booking Form Modal --}}
        <div class="modal fade" id="bookingModal" tabindex="-1">
          @if ($errors->has('error'))
            <div class="alert alert-danger">{{ $errors->first('error') }}</div>
          @endif

          <div class="modal-dialog">
            <div class="modal-content">
              <form method="POST"
                action="{{ route('public.booking.store', ['username' => $user->username, 'eventType' => $eventType->id]) }}">
                @csrf
                <input type="hidden" name="booking_date" id="selected_date"
                  value="{{ $selectedDate->format('Y-m-d') }}">
                <input type="hidden" name="start_time" id="selected_start_time">

                <div class="modal-header">
                  <h5 class="modal-title">Book {{ $eventType->name }}</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                  <div class="alert alert-info">
                    <strong>Selected Time:</strong> <span id="selected_time_display"></span><br>
                    <strong>Duration:</strong> {{ $eventType->duration }} minutes
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label for="attendee_name" class="form-label">Your Name *</label>
                        <input type="text" class="form-control @error('attendee_name') is-invalid @enderror"
                          id="attendee_name" name="attendee_name" value="{{ old('attendee_name') }}" required>
                        @error('attendee_name')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label for="attendee_email" class="form-label">Your Email *</label>
                        <input type="email" class="form-control @error('attendee_email') is-invalid @enderror"
                          id="attendee_email" name="attendee_email" value="{{ old('attendee_email') }}" required>
                        @error('attendee_email')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="attendee_phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control @error('attendee_phone') is-invalid @enderror"
                      id="attendee_phone" name="attendee_phone" value="{{ old('attendee_phone') }}">
                    @error('attendee_phone')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="mb-3">
                    <label for="attendee_notes" class="form-label">Additional Notes</label>
                    <textarea class="form-control @error('attendee_notes') is-invalid @enderror" id="attendee_notes"
                      name="attendee_notes" rows="3">{{ old('attendee_notes') }}</textarea>
                    @error('attendee_notes')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary">
                    {{ $eventType->requires_approval ? 'Request Booking' : 'Book Meeting' }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function changeDate(date) {
      const url = new URL(window.location);
      url.searchParams.set('date', date);
      window.location.href = url.toString();
    }

    function selectTimeSlot(button) {
      // Remove active class from all buttons
      document.querySelectorAll('.time-slot-btn').forEach(btn => {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-primary');
      });

      // Add active class to selected button
      button.classList.remove('btn-outline-primary');
      button.classList.add('btn-primary');

      // Set form values
      const startTime = button.dataset.startTime;
      const formattedTime = button.dataset.formattedTime;

      document.getElementById('selected_start_time').value = startTime;
      document.getElementById('selected_time_display').textContent = formattedTime;

      // Show modal
      const modal = new bootstrap.Modal(document.getElementById('bookingModal'));
      modal.show();
    }

    // Show validation errors in modal if they exist
    @if ($errors->any())
      document.addEventListener('DOMContentLoaded', function() {
        @if (old('start_time'))
          // If there were validation errors, reopen the modal with the previously selected time
          const startTime = '{{ old('start_time') }}';
          const buttons = document.querySelectorAll('.time-slot-btn');
          buttons.forEach(button => {
            if (button.dataset.startTime === startTime) {
              selectTimeSlot(button);
            }
          });
        @endif
      });
    @endif
  </script>
@endsection




{{-- @extends('layout.base')

@section('title', 'Book ' . $eventType->name . ' with ' . $user->name)

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Book: {{ $eventType->name }}</h4>
                    <small class="text-muted">{{ $eventType->duration }} minutes with {{ $user->name }}</small>
                </div>
                <div class="card-body">
                    @if ($eventType->requires_approval)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> This booking requires approval from {{ $user->name }}.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('public.booking.store', ['username' => $user->username, 'eventType' => $eventType->id]) }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="attendee_name" class="form-label">Your Name *</label>
                                    <input type="text" class="form-control @error('attendee_name') is-invalid @enderror"
                                           id="attendee_name" name="attendee_name" value="{{ old('attendee_name') }}" required>
                                    @error('attendee_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="attendee_email" class="form-label">Your Email *</label>
                                    <input type="email" class="form-control @error('attendee_email') is-invalid @enderror"
                                           id="attendee_email" name="attendee_email" value="{{ old('attendee_email') }}" required>
                                    @error('attendee_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="attendee_phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control @error('attendee_phone') is-invalid @enderror"
                                   id="attendee_phone" name="attendee_phone" value="{{ old('attendee_phone') }}">
                            @error('attendee_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="booking_date" class="form-label">Date *</label>
                                    <input type="date" class="form-control @error('booking_date') is-invalid @enderror"
                                           id="booking_date" name="booking_date" value="{{ old('booking_date', now()->format('Y-m-d')) }}"
                                           min="{{ now()->format('Y-m-d') }}" required>
                                    @error('booking_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_time" class="form-label">Time *</label>
                                    <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                                           id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="attendee_notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control @error('attendee_notes') is-invalid @enderror"
                                      id="attendee_notes" name="attendee_notes" rows="3">{{ old('attendee_notes') }}</textarea>
                            @error('attendee_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if ($errors->has('error'))
                            <div class="alert alert-danger">{{ $errors->first('error') }}</div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('public.booking.index', $user->username) }}" class="btn btn-secondary">Back</a>
                            <button type="submit" class="btn btn-primary">
                                {{ $eventType->requires_approval ? 'Request Booking' : 'Book Meeting' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}
