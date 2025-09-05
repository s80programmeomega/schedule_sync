@extends('layout.base')

@section('content')
  <div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-0 fw-bold">Groups</h1>
        <p class="text-muted mb-0">Organize contacts and team members</p>
      </div>
      <a href="{{ route('groups.create', ['team_id' => $teamId]) }}" class="btn btn-primary">
        <i class="bi bi-plus me-2"></i>Create Group
      </a>
    </div>

    <!-- Team Filter -->
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body">
        <form method="GET" class="row g-3">
          <div class="col-md-4">
            <select name="team_id" class="form-select" onchange="this.form.submit()">
              <option value="">Personal Groups</option>
              @foreach ($teams as $team)
                <option value="{{ $team->id }}" {{ $teamId == $team->id ? 'selected' : '' }}>
                  {{ $team->name }}
                </option>
              @endforeach
            </select>
          </div>
        </form>
      </div>
    </div>

    <!-- Groups Grid -->
    <div class="row">
      @forelse($groups as $group)
        <div class="col-md-6 col-lg-4 mb-4" style="z-index: 1">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex align-items-center mb-3">
                <div class="group-icon me-3"
                  style="background-color: {{ $group->color }}20; color: {{ $group->color }}; width: 48px; height: 48px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                  <i class="bi bi-collection fs-4"></i>
                </div>
                <div>
                  <h5 class="mb-0">{{ $group->name }}</h5>
                  <small class="text-muted">{{ $group->members->count() }} members</small>
                </div>
              </div>

              @if ($group->description)
                <p class="text-muted small mb-3">{{ Str::limit($group->description, 80) }}</p>
              @endif

              <div class="d-flex justify-content-between align-items-center">
                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $group->type)) }}</span>
                <div class="dropdown">
                  <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-three-dots"></i>
                  </button>
                  <ul class="dropdown-menu" style="z-index: 10;">
                    <li><a class="dropdown-item" href="{{ route('groups.show', $group) }}">View</a></li>
                    <li><a class="dropdown-item" href="{{ route('groups.edit', $group) }}">Edit</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <li><a class="dropdown-item" href="{{ route('groups.members.index', $group) }}">Manage Members</a></li>
                        <li>
                      <hr class="dropdown-divider">
                    </li>
                    <li>
                      <form method="POST" action="{{ route('groups.destroy', $group) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="dropdown-item text-danger">Delete</button>
                      </form>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12">
          <div class="text-center py-5">
            <i class="bi bi-collection text-muted" style="font-size: 4rem;"></i>
            <h4 class="mt-3 mb-2">No Groups Yet</h4>
            <p class="text-muted mb-4">Create your first group to organize contacts and team members</p>
            <a href="{{ route('groups.create', ['team_id' => $teamId]) }}" class="btn btn-primary">
              <i class="bi bi-plus me-2"></i>Create Group
            </a>
          </div>
        </div>
      @endforelse
    </div>

    <!-- Pagination -->
    @if ($groups->hasPages())
      <div class="d-flex justify-content-center mt-4">
        {{ $groups->appends(request()->query())->links() }}
      </div>
    @endif
  </div>
@endsection
