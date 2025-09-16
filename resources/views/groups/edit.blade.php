@extends('layout.base')

@section('title')
Edit Group
@endsection

@section('content')
<div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
  <div class="mb-4">
    <h1 class="h3 mb-0 fw-bold">Edit Group</h1>
    <p class="text-muted mb-0">Update group information</p>
  </div>

  <div class="row">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <form method="POST" action="{{ route('groups.update', $group) }}">
            @csrf @method('PUT')

            <div class="mb-4">
              <label for="name" class="form-label fw-semibold">Group Name</label>
              <input type="text" class="form-control @error('name') is-invalid @enderror"
                     id="name" name="name" value="{{ old('name', $group->name) }}" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
              <label for="description" class="form-label fw-semibold">Description</label>
              <textarea class="form-control @error('description') is-invalid @enderror"
                        id="description" name="description" rows="3">{{ old('description', $group->description) }}</textarea>
              @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
              <label for="type" class="form-label fw-semibold">Group Type</label>
              <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                <option value="contacts" {{ old('type', $group->type) === 'contacts' ? 'selected' : '' }}>Contacts Only</option>
                <option value="team_members" {{ old('type', $group->type) === 'team_members' ? 'selected' : '' }}>Team Members Only</option>
                <option value="mixed" {{ old('type', $group->type) === 'mixed' ? 'selected' : '' }}>Mixed</option>
              </select>
              @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
              <label for="color" class="form-label fw-semibold">Color</label>
              <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror"
                     id="color" name="color" value="{{ old('color', $group->color) }}" required>
              @error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex gap-3">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-check me-2"></i>Update Group
              </button>
              <a href="{{ route('groups.show', $group) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
