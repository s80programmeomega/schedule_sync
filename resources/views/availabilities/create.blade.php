@extends('layout.base')

@section('title', 'Add Availability - ScheduleSync')

@section('content')
  <div class="col-lg-10 col-12 py-4 px-4 px-lg-5" data-aos="fade-down" data-aos-duration="1000">
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
      <div class="col-lg-8" data-aos="zoom-in" data-aos-duration="500">
        <div class="card border-0 shadow-sm">
          <div class="card-body p-4">
            <form method="POST" action="{{ route('availability.store') }}">
              @csrf

              <div class="mb-4">
                <label for="availability_date" class="form-label">Availability Date</label>
                <input type="date" class="form-control @error('availability_date') is-invalid @enderror"
                  id="availability_date" name="availability_date" value="{{ old('availability_date') }}" required>
                @error('availability_date')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="mb-4">
                <label for="timezone_id" class="form-label">Timezone *</label>
                <select name="timezone_id" class="form-select @error('timezone_id') is-invalid @enderror" id="timezone_id"
                  required>
                  <option value="">Select your timezone</option>
                  @foreach ($timezones as $timezone)
                  <option value="{{ $timezone->id }}"
                    {{ old('timezone_id') == $timezone->id ? 'selected' : '' }}>
                    {{ $timezone->display_name }} ({{ $timezone->offset }})
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
                  <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time"
                    name="start_time" value="{{ old('start_time') }}" required>
                  @error('start_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label for="end_time" class="form-label">End Time *</label>
                  <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time"
                    name="end_time" value="{{ old('end_time') }}" required>
                  @error('end_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="form-check mb-4">
                <input type="hidden" name="is_available" value="0">
                <input class="form-check-input" type="checkbox" id="is_available" name="is_available" value="1"
                  {{ old('is_available', true) ? 'checked' : '' }}>
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
