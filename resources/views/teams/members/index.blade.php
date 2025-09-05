@extends('layout.base')

@section('content')
  <div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-0 fw-bold">{{ $team->name }} Members</h1>
        <p class="text-muted mb-0">Manage team members and permissions</p>
      </div>
      @can('manageMembers', $team)
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#inviteMemberModal">
          <i class="bi bi-plus me-2"></i>Invite Member
        </button>
      @endcan
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="bg-light">
              <tr>
                <th class="ps-4">Member</th>
                <th>Role</th>
                <th>Status</th>
                <th>Joined</th>
                <th class="pe-4">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($members as $member)
                <tr>
                  <td class="ps-4">
                    <div class="d-flex align-items-center">
                      <img
                        src="{{ $member->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($member->user->name) }}"
                        class="rounded-circle me-3" width="40" height="40">
                      <div>
                        <h6 class="mb-0">{{ $member->user->name }}</h6>
                        <small class="text-muted">{{ $member->user->email }}</small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span
                      class="badge bg-{{ $member->role === 'owner' ? 'primary' : ($member->role === 'admin' ? 'success' : 'secondary') }}">
                      {{ $member->role_display }}
                    </span>
                  </td>
                  <td>
                    <span
                      class="badge bg-{{ $member->status === 'active' ? 'success' : ($member->status === 'pending' ? 'warning' : 'secondary') }}">
                      {{ $member->status_display }}
                    </span>
                  </td>
                  <td>
                    @if ($member->joined_at)
                      {{ $member->joined_at->format('M j, Y') }}
                    @else
                      <span class="text-muted">Pending</span>
                    @endif
                  </td>
                  <td class="pe-4">
                    @can('manageMembers', $team)
                      @if ($member->role !== 'owner')
                        <div class="dropdown">
                          <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"
                                onclick="editMember({{ $member->id }}, '{{ $member->role }}')">Change Role</a></li>
                            <li>
                              <hr class="dropdown-divider">
                            </li>
                            <li>
                              <form method="POST" action="{{ route('teams.members.destroy', [$team, $member]) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger"
                                  onclick="return confirm('Remove this member?')">Remove</button>
                              </form>
                            </li>
                          </ul>
                        </div>
                      @endif
                    @endcan
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Invite Member Modal -->
  @can('manageMembers', $team)
    <div class="modal fade" id="inviteMemberModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <form method="POST" action="{{ route('teams.members.store', $team) }}">
            @csrf
            <div class="modal-header">
              <h5 class="modal-title">Invite Team Member</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" required>
              </div>
              <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role" required>
                  <option value="member">Member - Can create events and manage bookings</option>
                  <option value="admin">Admin - Can manage team settings and members</option>
                  <option value="viewer">Viewer - Read-only access</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Send Invitation</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endcan

  <!-- Edit Member Role Modal -->
<div class="modal fade" id="editMemberModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="editMemberForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Change Member Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editRole" class="form-label">Role</label>
                        <select class="form-select" id="editRole" name="role" required>
                            <option value="member">Member - Can create events and manage bookings</option>
                            <option value="admin">Admin - Can manage team settings and members</option>
                            <option value="viewer">Viewer - Read-only access</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editMember(memberId, currentRole) {
    const form = document.getElementById('editMemberForm');
    const roleSelect = document.getElementById('editRole');

    form.action = `{{ route('teams.members.update', [$team, ':member']) }}`.replace(':member', memberId);
    roleSelect.value = currentRole;

    new bootstrap.Modal(document.getElementById('editMemberModal')).show();
}
</script>
@endsection
