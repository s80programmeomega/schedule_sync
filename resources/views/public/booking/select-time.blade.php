@extends('layout.base')

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
                    @if($eventType->requires_approval)
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

                        @if($errors->has('error'))
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
@endsection
