@extends('layout.base')

@section('title')
Create Group
@endsection

@section('content')
<div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
  <div class="mb-4">
    <h1 class="h3 mb-0 fw-bold">Create Group</h1>
    <p class="text-muted mb-0">Organize contacts and team members</p>
  </div>

  <div class="row">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <form method="POST" action="{{ route('groups.store') }}">
            @csrf

            <div class="mb-4">
              <label for="name" class="form-label fw-semibold">Group Name</label>
              <input type="text" class="form-control @error('name') is-invalid @enderror"
                     id="name" name="name" value="{{ old('name') }}" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
              <label for="description" class="form-label fw-semibold">Description</label>
              <textarea class="form-control @error('description') is-invalid @enderror"
                        id="description" name="description" rows="3">{{ old('description') }}</textarea>
              @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
              <label for="type" class="form-label fw-semibold">Group Type</label>
              <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                <option value="contacts" {{ old('type') === 'contacts' ? 'selected' : '' }}>Contacts Only</option>
                <option value="team_members" {{ old('type') === 'team_members' ? 'selected' : '' }}>Team Members Only</option>
                <option value="mixed" {{ old('type') === 'mixed' ? 'selected' : '' }}>Mixed</option>
              </select>
              @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
              <label for="color" class="form-label fw-semibold">Color</label>
              <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror"
                     id="color" name="color" value="{{ old('color', '#6366f1') }}" required>
              @error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            @if($teams->count() > 0)
            <div class="mb-4">
              <label for="team_id" class="form-label fw-semibold">Team (Optional)</label>
              <select class="form-select @error('team_id') is-invalid @enderror" id="team_id" name="team_id">
                <option value="">Personal Group</option>
                @foreach($teams as $team)
                  <option value="{{ $team->id }}" {{ old('team_id', $selectedTeam) == $team->id ? 'selected' : '' }}>
                    {{ $team->name }}
                  </option>
                @endforeach
              </select>
              @error('team_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            @endif

            <div class="d-flex gap-3">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-check me-2"></i>Create Group
              </button>
              <a href="{{ route('groups.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
