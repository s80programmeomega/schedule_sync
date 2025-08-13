@extends('layout.base')

@section('title', 'Add Availability - ScheduleSync')

@section('content')
<div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('availability.index') }}" class="btn btn-light me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 mb-0 fw-bold">Add Availability</h1>
            <p class="text-muted mb-0">Set your available time slots</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('availability.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="day_of_week" class="form-label">Day of Week *</label>
                            <select class="form-select @error('day_of_week') is-invalid @enderror" id="day_of_week"
                                name="day_of_week" required>
                                <option value="">Select day</option>
                                <option value="monday" {{ old('day_of_week')=='monday' ? 'selected' : '' }}>Monday
                                </option>
                                <option value="tuesday" {{ old('day_of_week')=='tuesday' ? 'selected' : '' }}>Tuesday
                                </option>
                                <option value="wednesday" {{ old('day_of_week')=='wednesday' ? 'selected' : '' }}>
                                    Wednesday</option>
                                <option value="thursday" {{ old('day_of_week')=='thursday' ? 'selected' : '' }}>Thursday
                                </option>
                                <option value="friday" {{ old('day_of_week')=='friday' ? 'selected' : '' }}>Friday
                                </option>
                                <option value="saturday" {{ old('day_of_week')=='saturday' ? 'selected' : '' }}>Saturday
                                </option>
                                <option value="sunday" {{ old('day_of_week')=='sunday' ? 'selected' : '' }}>Sunday
                                </option>
                            </select>
                            @error('day_of_week')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="start_time" class="form-label">Start Time *</label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                                    id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                                @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="end_time" class="form-label">End Time *</label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror"
                                    id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                                @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input type="hidden" name="is_available" value="0">
                            <input class="form-check-input" type="checkbox" id="is_available" name="is_available"
                                value="1" {{ old('is_available', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_available">
                                Available for booking
                            </label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check me-2"></i> Add Availability
                            </button>
                            <a href="{{ route('availability.index') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
