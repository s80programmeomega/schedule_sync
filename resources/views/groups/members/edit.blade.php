@extends('layout.base')

@section('content')
<div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
  <div class="mb-4">
    <h1 class="h3 mb-0 fw-bold">Edit Member Role</h1>
    <p class="text-muted mb-0">Update member role in {{ $group->name }}</p>
  </div>

  <div class="row">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <div class="d-flex align-items-center mb-4">
            @if($groupMember->member_type === 'App\Models\TeamMember')
              <img src="https://ui-avatars.com/api/?name={{ $groupMember->member->user->name }}&background=6366f1&color=fff"
                   class="rounded-circle me-3" width="48" height="48">
              <div>
                <h5 class="mb-0">{{ $groupMember->member->user->name }}</h5>
                <small class="text-muted">{{ $groupMember->member->user->email }}</small>
              </div>
            @else
              <img src="https://ui-avatars.com/api/?name={{ $groupMember->member->name }}&background=6366f1&color=fff"
                   class="rounded-circle me-3" width="48" height="48">
              <div>
                <h5 class="mb-0">{{ $groupMember->member->name }}</h5>
                <small class="text-muted">{{ $groupMember->member->email }}</small>
              </div>
            @endif
          </div>

          <form method="POST" action="{{ route('groups.members.update', [$group, $groupMember]) }}">
            @csrf @method('PUT')

            <div class="mb-4">
              <label for="role" class="form-label fw-semibold">Role</label>
              <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                <option value="member" {{ old('role', $groupMember->role) === 'member' ? 'selected' : '' }}>Member</option>
                <option value="admin" {{ old('role', $groupMember->role) === 'admin' ? 'selected' : '' }}>Admin</option>
              </select>
              @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex gap-3">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-check me-2"></i>Update Role
              </button>
              <a href="{{ route('groups.members.index', $group) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
