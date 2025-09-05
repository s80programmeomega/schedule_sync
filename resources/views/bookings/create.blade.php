{{-- resources/views/bookings/create-with-attendees.blade.php --}}
@extends('layout.base')

@section('content')
<div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
    <div class="mb-4">
        <h1 class="h3 mb-0 fw-bold">Create Booking</h1>
        <p class="text-muted mb-0">Schedule a meeting with multiple attendees</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('bookings.store-with-attendees') }}" id="bookingForm">
                        @csrf

                        <div class="mb-4">
                            <label for="event_type_id" class="form-label fw-semibold">Event Type</label>
                            <select class="form-select @error('event_type_id') is-invalid @enderror"
                                    id="event_type_id" name="event_type_id" required>
                                <option value="">Select Event Type</option>
                                @foreach($eventTypes as $eventType)
                                <option value="{{ $eventType->id }}"
                                        data-duration="{{ $eventType->duration }}"
                                        data-multiple="{{ $eventType->allow_multiple_attendees ? 'true' : 'false' }}">
                                    {{ $eventType->name }} ({{ $eventType->duration }} min)
                                </option>
                                @endforeach
                            </select>
                            @error('event_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="booking_date" class="form-label fw-semibold">Date</label>
                                <input type="date" class="form-control @error('booking_date') is-invalid @enderror"
                                       id="booking_date" name="booking_date" value="{{ old('booking_date') }}" required>
                                @error('booking_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="start_time" class="form-label fw-semibold">Start Time</label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                                       id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Attendees Section -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label fw-semibold mb-0">Attendees</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addAttendee()">
                                    <i class="bi bi-plus me-1"></i>Add Attendee
                                </button>
                            </div>

                            <div id="attendees-container">
                                <div class="attendee-row border rounded p-3 mb-3">
                                    <div class="row">
                                        <div class="col-md-3 mb-2">
                                            <select class="form-select" name="attendees[0][type]" onchange="toggleAttendeeFields(this, 0)">
                                                <option value="contact">From Contacts</option>
                                                <option value="email">Enter Email</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <select class="form-select contact-select" name="attendees[0][contact_id]">
                                                <option value="">Select Contact</option>
                                                @foreach($contacts as $contact)
                                                <option value="{{ $contact->id }}">{{ $contact->name }} ({{ $contact->email }})</option>
                                                @endforeach
                                            </select>
                                            <input type="text" class="form-control email-input d-none" name="attendees[0][name]" placeholder="Full Name">
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <input type="email" class="form-control email-input d-none" name="attendees[0][email]" placeholder="Email Address">
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <select class="form-select" name="attendees[0][role]">
                                                <option value="required">Required</option>
                                                <option value="optional">Optional</option>
                                                <option value="organizer">Organizer</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check me-2"></i>Create Booking
                            </button>
                            <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let attendeeCount = 1;

function addAttendee() {
    const container = document.getElementById('attendees-container');
    const newRow = document.createElement('div');
    newRow.className = 'attendee-row border rounded p-3 mb-3';
    newRow.innerHTML = `
        <div class="row">
            <div class="col-md-3 mb-2">
                <select class="form-select" name="attendees[${attendeeCount}][type]" onchange="toggleAttendeeFields(this, ${attendeeCount})">
                    <option value="contact">From Contacts</option>
                    <option value="email">Enter Email</option>
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <select class="form-select contact-select" name="attendees[${attendeeCount}][contact_id]">
                    <option value="">Select Contact</option>
                    @foreach($contacts as $contact)
                    <option value="{{ $contact->id }}">{{ $contact->name }} ({{ $contact->email }})</option>
                    @endforeach
                </select>
                <input type="text" class="form-control email-input d-none" name="attendees[${attendeeCount}][name]" placeholder="Full Name">
            </div>
            <div class="col-md-3 mb-2">
                <input type="email" class="form-control email-input d-none" name="attendees[${attendeeCount}][email]" placeholder="Email Address">
            </div>
            <div class="col-md-1 mb-2">
                <select class="form-select" name="attendees[${attendeeCount}][role]">
                    <option value="required">Required</option>
                    <option value="optional">Optional</option>
                </select>
            </div>
            <div class="col-md-1 mb-2">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeAttendee(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(newRow);
    attendeeCount++;
}

function removeAttendee(button) {
    button.closest('.attendee-row').remove();
}

function toggleAttendeeFields(select, index) {
    const row = select.closest('.attendee-row');
    const contactSelect = row.querySelector('.contact-select');
    const emailInputs = row.querySelectorAll('.email-input');

    if (select.value === 'contact') {
        contactSelect.classList.remove('d-none');
        emailInputs.forEach(input => input.classList.add('d-none'));
    } else {
        contactSelect.classList.add('d-none');
        emailInputs.forEach(input => input.classList.remove('d-none'));
    }
}
</script>
@endsection
