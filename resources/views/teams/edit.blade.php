@extends('layout.base')

@section('content')
<div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-0 fw-bold">Edit Team</h1>
      <p class="text-muted mb-0">Update {{ $team->name }} settings</p>
    </div>
    <a href="{{ route('teams.show', $team) }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-2"></i>Back to Team
    </a>
  </div>

  <div class="row">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <form method="POST" action="{{ route('teams.update', $team) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
              <label for="name" class="form-label fw-semibold">Team Name</label>
              <input type="text" class="form-control @error('name') is-invalid @enderror"
                     id="name" name="name" value="{{ old('name', $team->name) }}" required>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-4">
              <label for="description" class="form-label fw-semibold">Description</label>
              <textarea class="form-control @error('description') is-invalid @enderror"
                        id="description" name="description" rows="3">{{ old('description', $team->description) }}</textarea>
              @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-4">
              <label for="logo" class="form-label fw-semibold">Team Logo</label>
              <input type="file" class="form-control @error('logo') is-invalid @enderror"
                     id="logo" name="logo" accept="image/*">
              @if($team->logo)
                <div class="mt-2">
                  <img src="{{ asset('storage/' . $team->logo) }}" alt="Current logo" class="rounded" width="64" height="64">
                  <small class="text-muted d-block">Current logo</small>
                </div>
              @endif
              @error('logo')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-4">
              <label for="timezone" class="form-label fw-semibold">Default Timezone</label>
              <select class="form-select @error('timezone') is-invalid @enderror" id="timezone" name="timezone" required>
                <option value="UTC" {{ old('timezone', $team->timezone) === 'UTC' ? 'selected' : '' }}>UTC</option>
                <option value="America/New_York" {{ old('timezone', $team->timezone) === 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                <option value="America/Chicago" {{ old('timezone', $team->timezone) === 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                <option value="America/Denver" {{ old('timezone', $team->timezone) === 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                <option value="America/Los_Angeles" {{ old('timezone', $team->timezone) === 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
              </select>
              @error('timezone')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="d-flex gap-3">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-check me-2"></i>Update Team
              </button>
              <a href="{{ route('teams.show', $team) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>

      <!-- Danger Zone -->
      @can('delete', $team)
      <div class="card border-danger mt-4">
        <div class="card-header bg-danger text-white">
          <h5 class="mb-0">Danger Zone</h5>
        </div>
        <div class="card-body">
          <p class="text-muted">Once you delete a team, there is no going back. Please be certain.</p>
          <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteTeamModal">
            Delete Team
          </button>
        </div>
      </div>
      @endcan
    </div>
  </div>
</div>

<!-- Delete Team Modal -->
@can('delete', $team)
<div class="modal fade" id="deleteTeamModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Team</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete <strong>{{ $team->name }}</strong>?</p>
        <p class="text-danger">This action cannot be undone. All team data will be permanently deleted.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form method="POST" action="{{ route('teams.destroy', $team) }}" class="d-inline">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">Delete Team</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endcan
@endsection
