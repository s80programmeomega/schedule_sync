@extends('layout.base')

@section('content')
  <div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
    <div class="mb-4">
      <h1 class="h3 mb-0 fw-bold">Create Team</h1>
      <p class="text-muted mb-0">Set up a new team workspace</p>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
          <div class="card-body p-4">
            <form method="POST" action="{{ route('teams.store') }}">
              @csrf

              <div class="mb-4">
                <label for="name" class="form-label fw-semibold">Team Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                  name="name" value="{{ old('name') }}" required>
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-4">
                <label for="description" class="form-label fw-semibold">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                  rows="3">{{ old('description') }}</textarea>
                @error('description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-4">
                <label for="timezone" class="form-label fw-semibold">Timezone</label>
                <select class="form-select @error('timezone') is-invalid @enderror" id="timezone" name="timezone" required>
                    <option value="">Select a timezone</option>
                    @foreach($timezones as $timezone)
                      <option value="{{ $timezone->name }}" {{ old('timezone') === $timezone->name ? 'selected' : '' }}>
                        {{ $timezone->display_name }}
                      </option>
                    @endforeach
                  </select>
                @error('timezone')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="d-flex gap-3">
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-check me-2"></i>Create Team
                </button>
                <a href="{{ route('teams.index') }}" class="btn btn-outline-secondary">Cancel</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
