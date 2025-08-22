@extends('layout.base')

@section('title', 'Edit Availability - ScheduleSync')

@section('content')
<div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('availability.index') }}" class="btn btn-light me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 mb-0 fw-bold">Edit Availability</h1>
            <p class="text-muted mb-0">Update your available time slots</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('availability.update', $availability) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="availability_date" class="form-label">Availability Date *</label>
                            <input type="date" class="form-control @error('availability_date') is-invalid @enderror" id="availability_date"
                                name="availability_date" value="{{ old('availability_date', $availability->availability_date->format('Y-m-d')) }}">
                            @error('availability_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="timezone_id" class="form-label">Timezone *</label>
                            <select class="form-select @error('timezone_id') is-invalid @enderror" id="timezone_id" name="timezone_id">
                                <option value="">Select Timezone</option>
                                @foreach($timezones as $timezone)
                                    <option value="{{ $timezone->id }}" {{ old('timezone_id', $availability->timezone_id) == $timezone->id ? 'selected' : '' }}>
                                        {{ $timezone->display_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('timezone_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="start_time" class="form-label">Start Time *</label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                                    id="start_time" name="start_time"
                                    value="{{ old('start_time', \Carbon\Carbon::parse($availability->start_time)->format('H:i')) }}">
                                @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="end_time" class="form-label">End Time *</label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror"
                                    id="end_time" name="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($availability->end_time)->format('H:i')) }}"
                                    >
                                @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input type="hidden" name="is_available" value="0">
                            <input class="form-check-input" type="checkbox" id="is_available" name="is_available"
                                value="1" {{ $availability->is_available ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_available">
                                Available for booking
                            </label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check me-2"></i> Update Availability
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
