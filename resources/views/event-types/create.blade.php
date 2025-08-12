@extends('layout.base')

@section('title', 'Create Event Type - ScheduleSync')

@section('content')
    <div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('event-types.index') }}" class="btn btn-light me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="h3 mb-0 fw-bold">Create Event Type</h1>
                <p class="text-muted mb-0">Set up a new meeting type for your calendar</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('event-types.store') }}">
                            @csrf

                            <div class="mb-4">
                                <label for="name" class="form-label">Event Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="duration" class="form-label">Duration (minutes) *</label>
                                    <select class="form-select @error('duration') is-invalid @enderror" id="duration"
                                        name="duration" required>
                                        <option value="">Select duration</option>
                                        <option value="15" {{ old('duration') == '15' ? 'selected' : '' }}>15 minutes
                                        </option>
                                        <option value="30" {{ old('duration') == '30' ? 'selected' : '' }}>30 minutes
                                        </option>
                                        <option value="45" {{ old('duration') == '45' ? 'selected' : '' }}>45 minutes
                                        </option>
                                        <option value="60" {{ old('duration') == '60' ? 'selected' : '' }}>1 hour
                                        </option>
                                        <option value="90" {{ old('duration') == '90' ? 'selected' : '' }}>1.5 hours
                                        </option>
                                        <option value="120" {{ old('duration') == '120' ? 'selected' : '' }}>2 hours
                                        </option>
                                    </select>
                                    @error('duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="color" class="form-label">Color</label>
                                    <input type="color"
                                        class="form-control form-control-color @error('color') is-invalid @enderror"
                                        id="color" name="color" value="{{ old('color', '#6366f1') }}">
                                    @error('color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="location_type" class="form-label">Location Type *</label>
                                <select class="form-select @error('location_type') is-invalid @enderror" id="location_type"
                                    name="location_type" required>
                                    <option value="">Select location type</option>
                                    <option value="zoom" {{ old('location_type') == 'zoom' ? 'selected' : '' }}>Zoom
                                    </option>
                                    <option value="google_meet"
                                        {{ old('location_type') == 'google_meet' ? 'selected' : '' }}>Google Meet</option>
                                    <option value="phone" {{ old('location_type') == 'phone' ? 'selected' : '' }}>Phone
                                        Call</option>
                                    <option value="custom" {{ old('location_type') == 'custom' ? 'selected' : '' }}>Custom
                                    </option>
                                </select>
                                @error('location_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="location_details" class="form-label">Location Details</label>
                                <input type="text" class="form-control @error('location_details') is-invalid @enderror"
                                    id="location_details" name="location_details" value="{{ old('location_details') }}"
                                    placeholder="Meeting link, phone number, or address">
                                @error('location_details')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="buffer_time_before" class="form-label">Buffer Before (minutes)</label>
                                    <input type="number"
                                        class="form-control @error('buffer_time_before') is-invalid @enderror"
                                        id="buffer_time_before" name="buffer_time_before"
                                        value="{{ old('buffer_time_before', 0) }}" min="0" max="60">
                                    @error('buffer_time_before')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="buffer_time_after" class="form-label">Buffer After (minutes)</label>
                                    <input type="number"
                                        class="form-control @error('buffer_time_after') is-invalid @enderror"
                                        id="buffer_time_after" name="buffer_time_after"
                                        value="{{ old('buffer_time_after', 0) }}" min="0" max="60">
                                    @error('buffer_time_after')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="max_events_per_day" class="form-label">Max Events Per Day</label>
                                <input type="number"
                                    class="form-control @error('max_events_per_day') is-invalid @enderror"
                                    id="max_events_per_day" name="max_events_per_day"
                                    value="{{ old('max_events_per_day') }}" min="1" max="20">
                                @error('max_events_per_day')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="requires_confirmation"
                                    name="requires_confirmation" value="1"
                                    {{ old('requires_confirmation') ? 'checked' : '' }}>
                                <label class="form-check-label" for="requires_confirmation">
                                    Requires confirmation before booking
                                </label>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check me-2"></i> Create Event Type
                                </button>
                                <a href="{{ route('event-types.index') }}" class="btn btn-light">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
