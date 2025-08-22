@extends('layout.base')

@section('title', 'Edit Booking - ScheduleSync')

@section('content')
    <div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('bookings.show', $booking) }}" class="btn btn-light me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="h3 mb-0 fw-bold">Edit Booking</h1>
                <p class="text-muted mb-0">{{ $booking->attendee_name }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('bookings.update', $booking) }}">
                            @csrf @method('PUT')

                            <div class="mb-4">
                                <label for="event_type_id" class="form-label">Event Type *</label>
                                <select class="form-select @error('event_type_id') is-invalid @enderror" id="event_type_id"
                                    name="event_type_id" value="{{ old('event_type_id', $booking->event_type_id) }}" required>
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
                                    <label for="attendee_name" class="form-label">Attendee Name *</label>
                                    <input type="text" class="form-control @error('attendee_name') is-invalid @enderror"
                                        id="attendee_name" name="attendee_name"
                                        value="{{ old('attendee_name', $booking->attendee_name) }}" required>
                                    @error('attendee_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="attendee_email" class="form-label">Attendee Email *</label>
                                    <input type="email" class="form-control @error('attendee_email') is-invalid @enderror"
                                        id="attendee_email" name="attendee_email"
                                        value="{{ old('attendee_email', $booking->attendee_email) }}" required>
                                    @error('attendee_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
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
                                    <select class="form-select @error('timezone_id') is-invalid @enderror" id="timezone_id" name="timezone_id" required>
                                        <option value="">Select timezone</option>
                                        @foreach($timezones as $timezone)
                                        <option value="{{ $timezone->id }}" {{ old('timezone_id', $booking->timezone_id) == $timezone->id ? 'selected' :
                                            '' }}>
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
                                    <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                                        id="start_time" name="start_time"
                                        value="{{ old('start_time', \Carbon\Carbon::parse($booking->start_time)->format('H:i')) }}" required>
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="end_time" class="form-label">End Time *</label>
                                    <input type="time" class="form-control @error('end_time') is-invalid @enderror"
                                        id="end_time" name="end_time"
                                        value="{{ old('end_time', \Carbon\Carbon::parse($booking->end_time)->format('H:i')) }}" disabled>
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status"
                                    name="status" required>
                                    <option value="scheduled"
                                        {{ old('status', $booking->status) === 'scheduled' ? 'selected' : '' }}>
                                        Scheduled</option>
                                    <option value="completed"
                                        {{ old('status', $booking->status) === 'completed' ? 'selected' : '' }}>
                                        Completed</option>
                                    <option value="pending"
                                        {{ old('status', $booking->status) === 'pending' ? 'selected' : '' }}>
                                        Pending</option>
                                    <option value="cancelled"
                                        {{ old('status', $booking->status) === 'cancelled' ? 'selected' : '' }}>
                                        Cancelled</option>
                                    <option value="no_show"
                                        {{ old('status', $booking->status) === 'no_show' ? 'selected' : '' }}>
                                        No Show</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="meeting_link" class="form-label">Meeting Link</label>
                                <input type="url" class="form-control @error('meeting_link') is-invalid @enderror"
                                    id="meeting_link" name="meeting_link"
                                    value="{{ old('meeting_link', $booking->meeting_link) }}">
                                @error('meeting_link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="attendee_notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('attendee_notes') is-invalid @enderror" id="attendee_notes" name="attendee_notes"
                                    rows="3">{{ old('attendee_notes', $booking->attendee_notes) }}</textarea>
                                @error('attendee_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="cancellation_reason" class="form-label">Cancellation Reason</label>
                                <textarea class="form-control @error('cancellation_reason') is-invalid @enderror" id="cancellation_reason"
                                    name="cancellation_reason" rows="2">{{ old('cancellation_reason', $booking->cancellation_reason) }}</textarea>
                                @error('cancellation_reason')
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
            </div>
        </div>
    </div>
@endsection
