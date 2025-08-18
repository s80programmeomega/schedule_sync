@extends('layout.base')

@section('title', 'Create Booking - ScheduleSync')

@section('content')
<div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('bookings.index') }}" class="btn btn-light me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 mb-0 fw-bold">Create Booking</h1>
            <p class="text-muted mb-0">Schedule a new appointment</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('bookings.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="event_type_id" class="form-label">Event Type *</label>
                            <select class="form-select @error('event_type_id') is-invalid @enderror" id="event_type_id"
                                name="event_type_id" required>
                                <option value="">Select event type</option>
                                @foreach($eventTypes as $eventType)
                                <option value="{{ $eventType->id }}" {{ old('event_type_id')==$eventType->id ?
                                    'selected' : '' }}>
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
                                    id="attendee_name" name="attendee_name" value="{{ old('attendee_name') }}" required>
                                @error('attendee_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="attendee_email" class="form-label">Attendee Email *</label>
                                <input type="email" class="form-control @error('attendee_email') is-invalid @enderror"
                                    id="attendee_email" name="attendee_email" value="{{ old('attendee_email') }}"
                                    required>
                                @error('attendee_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="booking_date" class="form-label">Booking Date *</label>
                                <input type="date"
                                    class="form-control @error('booking_date') is-invalid @enderror" id="booking_date"
                                    name="booking_date" value="{{ old('booking_date') }}" required>
                                @error('booking_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="start_time" class="form-label">Start Time *</label>
                                <input type="time"
                                    class="form-control @error('start_time') is-invalid @enderror" id="start_time"
                                    name="start_time" value="{{ old('start_time') }}" required>
                                @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- <div class="col-md-6">
                                <label for="end_time" class="form-label">End Time *</label>
                                <input type="time"
                                    class="form-control @error('end_time') is-invalid @enderror" id="end_time"
                                    name="end_time" value="{{ old('end_time') }}" required>
                                @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div> --}}
                        </div>

                        <div class="mb-4">
                            <label for="meeting_link" class="form-label">Meeting Link</label>
                            <input type="url" class="form-control @error('meeting_link') is-invalid @enderror"
                                id="meeting_link" name="meeting_link" value="{{ old('meeting_link') }}"
                                placeholder="https://zoom.us/j/...">
                            @error('meeting_link')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="attendee_notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('attendee_notes') is-invalid @enderror"
                                id="attendee_notes" name="attendee_notes"
                                rows="3">{{ old('attendee_notes') }}</textarea>
                            @error('attendee_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check me-2"></i> Create Booking
                            </button>
                            <a href="{{ route('bookings.index') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
