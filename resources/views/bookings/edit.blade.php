@extends('layout.base')

@section('title', 'Edit Booking - ScheduleSync')

@section('content')
  <div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
    {{-- Display all validation errors --}}
    @if ($errors->any())
      <div class="alert alert-danger">
        <h6 class="alert-heading mb-2">Please correct the following errors:</h6>
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    <div class="d-flex align-items-center mb-4">
      <a href="{{ route('bookings.show', $booking) }}" class="btn btn-light me-3">
        <i class="bi bi-arrow-left"></i>
      </a>
      <div>
        <h1 class="h3 mb-0 fw-bold">Edit Booking</h1>
        <p class="text-muted mb-0">{{ $booking->eventType->name }}</p>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <!-- Booking Details Form -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body p-4">
            <h5 class="card-title mb-3">Booking Details</h5>
            <form method="POST" action="{{ route('bookings.update', $booking) }}">
              @csrf @method('PUT')

              <div class="mb-4">
                <label for="event_type_id" class="form-label">Event Type *</label>
                <select class="form-select @error('event_type_id') is-invalid @enderror" id="event_type_id"
                  name="event_type_id" required>
                  @foreach ($eventTypes as $eventType)
                    <option value="{{ $eventType->id }}"
                      {{ old('event_type_id', $booking->event_type_id) == $eventType->id ? 'selected' : '' }}>
                      {{ $eventType->name }} ({{ $eventType->duration }} min)
                    </option>
                  @endforeach
                </select>
                @error('event_type_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="row mb-4">
                <div class="col-md-6">
                  <label for="booking_date" class="form-label">Booking Date</label>
                  <input type="date" class="form-control @error('booking_date') is-invalid @enderror" id="booking_date"
                    name="booking_date"
                    value="{{ old('booking_date', \Carbon\Carbon::parse($booking->booking_date)->format('Y-m-d')) }}"
                    required>
                  @error('booking_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-6">
                  <label for="timezone_id" class="form-label">Timezone *</label>
                  <select class="form-select @error('timezone_id') is-invalid @enderror" id="timezone_id"
                    name="timezone_id" required>
                    <option value="">Select timezone</option>
                    @foreach ($timezones as $timezone)
                      <option value="{{ $timezone->id }}"
                        {{ old('timezone_id', $booking->timezone_id) == $timezone->id ? 'selected' : '' }}>
                        {{ $timezone->display_name }}
                      </option>
                    @endforeach
                  </select>
                  @error('timezone_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="row mb-4">
                <div class="col-md-6">
                  <label for="start_time" class="form-label">Start Time *</label>
                  <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time"
                    name="start_time"
                    data-format="24"
                    value="{{ old('start_time', \Carbon\Carbon::parse($booking->start_time)->format('H:i')) }}" required>
                  @error('start_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label for="status" class="form-label">Status *</label>
                  <select class="form-select @error('status') is-invalid @enderror" id="status" name="status"
                    required>
                    <option value="scheduled" {{ old('status', $booking->status) === 'scheduled' ? 'selected' : '' }}>
                      Scheduled</option>
                    <option value="completed" {{ old('status', $booking->status) === 'completed' ? 'selected' : '' }}>
                      Completed</option>
                    <option value="pending" {{ old('status', $booking->status) === 'pending' ? 'selected' : '' }}>Pending
                    </option>
                    <option value="cancelled" {{ old('status', $booking->status) === 'cancelled' ? 'selected' : '' }}>
                      Cancelled</option>
                  </select>
                  @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="mb-4">
                <label for="meeting_link" class="form-label">Meeting Link</label>
                <input type="url" class="form-control @error('meeting_link') is-invalid @enderror" id="meeting_link"
                  name="meeting_link" value="{{ old('meeting_link', $booking->meeting_link) }}">
                @error('meeting_link')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-check me-2"></i> Update Booking
                </button>
                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-light">Cancel</a>
              </div>
            </form>
          </div>
        </div>

        <!-- Attendee Management -->
        <div class="card border-0 shadow-sm">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="card-title mb-0">Manage Attendees</h5>
              <button class="btn btn-sm btn-primary" onclick="showAddAttendeeModal()">
                <i class="bi bi-person-plus me-1"></i> Add Attendee
              </button>
            </div>

            <div id="attendees-container">
              @foreach ($booking->attendees as $attendee)
                <div class="attendee-item border rounded p-3 mb-2" data-attendee-id="{{ $attendee->id }}">
                  <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                      <div class="avatar-sm me-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                          style="width: 40px; height: 40px;">
                          {{ substr($attendee->name, 0, 1) }}
                        </div>
                      </div>
                      <div>
                        <div class="fw-semibold">{{ $attendee->name }}</div>
                        <small class="text-muted">{{ $attendee->email }}</small>
                      </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                      <span
                        class="badge bg-{{ $attendee->role === 'organizer' ? 'primary' : ($attendee->role === 'required' ? 'success' : 'secondary') }}">
                        {{ ucfirst($attendee->role) }}
                      </span>
                      <span
                        class="badge bg-{{ $attendee->status === 'accepted' ? 'success' : ($attendee->status === 'declined' ? 'danger' : 'warning') }}">
                        {{ ucfirst($attendee->status) }}
                      </span>
                      <div class="dropdown">
                        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                          <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu">
                          <li><a class="dropdown-item" href="#" onclick="editAttendee({{ $attendee->id }})">
                              <i class="bi bi-pencil me-2"></i>Edit</a></li>
                          <li><a class="dropdown-item text-danger" href="#"
                              onclick="removeAttendee({{ $attendee->id }})">
                              <i class="bi bi-trash me-2"></i>Remove</a></li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Attendee Modal -->
  <div class="modal fade" id="addAttendeeModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Attendee</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="addAttendeeForm">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Attendee Type</label>
              <select class="form-select" name="type" id="attendeeType" onchange="toggleAttendeeFields()">
                <option value="">Select type</option>
                <option value="contact">Contact</option>
                <option value="email">Email</option>
                <option value="team">Team Member</option>
                <option value="group">Group Member</option>
              </select>
            </div>

            <div id="contactField" class="mb-3" style="display: none;">
              <label class="form-label">Contact</label>
              <select class="form-select" name="contact_id">
                <option value="">Select contact</option>
                @foreach ($contacts as $contact)
                  <option value="{{ $contact->id }}">{{ $contact->name }} ({{ $contact->email }})</option>
                @endforeach
              </select>
            </div>

            <div id="emailFields" style="display: none;">
              <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" name="name">
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email">
              </div>
            </div>

            <div id="teamField" class="mb-3" style="display: none;">
              <label class="form-label">Team Member</label>
              <select class="form-select" name="team_member_id" id="teamMemberId">
                <option value="">Select team member</option>
                @foreach ($teams as $team)
                  <optgroup label="{{ $team->name }}">
                    @foreach ($team->members as $member)
                      <option value="{{ $member->id }}">{{ $member->user->name }} ({{ $member->user->email }})
                      </option>
                    @endforeach
                  </optgroup>
                @endforeach
              </select>
            </div>

            <div id="groupField" class="mb-3" style="display: none;">
              <label class="form-label">Group Member</label>
              <select class="form-select" name="group_member_id" id="groupMemberId">
                <option value="">Select group member</option>
                @foreach ($groups as $group)
                  <optgroup label="{{ $group->name }}">
                    @foreach ($group->members as $member)
                      <option value="{{ $member->id }}">{{ $member->member->name }} ({{ $member->member->email }})
                      </option>
                    @endforeach
                  </optgroup>
                @endforeach
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Role</label>
              <select class="form-select" name="role">
                <option value="required">Required</option>
                <option value="optional">Optional</option>
                <option value="organizer">Organizer</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Add Attendee</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    function showAddAttendeeModal() {
      new bootstrap.Modal(document.getElementById('addAttendeeModal')).show();
    }

    function toggleAttendeeFields() {
      const type = document.getElementById('attendeeType').value;
      document.getElementById('contactField').style.display = type === 'contact' ? 'block' : 'none';
      document.getElementById('emailFields').style.display = type === 'email' ? 'block' : 'none';
      document.getElementById('teamField').style.display = type === 'team' ? 'block' : 'none';
      document.getElementById('groupField').style.display = type === 'group' ? 'block' : 'none';
    }

    document.getElementById('addAttendeeForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const type = document.getElementById('attendeeType').value;
      const role = document.querySelector('select[name="role"]').value;

      let data = {
        type: type,
        role: role
      };

      // Get the correct member_id based on type
      switch (type) {
        case 'contact':
          data.contact_id = document.querySelector('select[name="contact_id"]').value;
          break;
        case 'email':
          data.name = document.querySelector('input[name="name"]').value;
          data.email = document.querySelector('input[name="email"]').value;
          break;
        case 'team':
          data.member_id = document.getElementById('teamMemberId').value;
          break;
        case 'group':
          data.member_id = document.getElementById('groupMemberId').value;
          break;
      }

      console.log('Sending data:', data);

      fetch(`/bookings/{{ $booking->id }}/attendees`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
          },
          body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            location.reload();
          } else {
            alert('Error: ' + (data.message || 'Failed to add attendee'));
          }
        })
        .catch(error => {
          console.error('Fetch error:', error);
          alert('Network error: ' + error.message);
        });
    });

    // document.getElementById('addAttendeeForm').addEventListener('submit', function(e) {
    //   e.preventDefault();

    //   const formData = new FormData(this);

    //   fetch(`/bookings/{{ $booking->id }}/attendees`, {
    //       method: 'POST',
    //       body: formData,
    //       headers: {
    //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    //       }
    //     })
    //     .then(response => response.json())
    //     .then(data => {
    //       if (data.success) {
    //         location.reload();
    //       } else {
    //         alert('Error: ' + (data.message || 'Failed to add attendee'));
    //       }
    //     })
    //     .catch(error => {
    //       alert('Error adding attendee');
    //       console.error(error);
    //     });
    // });

    function removeAttendee(attendeeId) {
      if (confirm('Remove this attendee?')) {
        fetch(`/bookings/{{ $booking->id }}/attendees/${attendeeId}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
          })
          .then(() => location.reload())
          .catch(error => {
            alert('Error removing attendee');
            console.error(error);
          });
      }
    }

    function editAttendee(attendeeId) {
      // Simple prompt-based edit for now
      const role = prompt('Enter new role (organizer, required, optional):');
      const status = prompt('Enter new status (pending, accepted, declined):');

      if (role && status) {
        fetch(`/bookings/{{ $booking->id }}/attendees/${attendeeId}`, {
            method: 'PATCH',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
              role,
              status
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              location.reload();
            } else {
              alert('Error updating attendee');
            }
          })
          .catch(error => {
            alert('Error updating attendee');
            console.error(error);
          });
      }
    }
  </script>

@endsection
