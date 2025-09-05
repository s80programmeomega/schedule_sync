@extends('layout.base')

@section('content')
<div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-0 fw-bold">{{ $group->name }} Members</h1>
      <p class="text-muted mb-0">Manage group membership</p>
    </div>
    <a href="{{ route('groups.show', $group) }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-2"></i>Back to Group
    </a>
  </div>

  <!-- Add Member Form -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
      <h6 class="mb-0">Add Member</h6>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('groups.members.store', $group) }}" class="row g-3">
        @csrf
        <div class="col-md-4">
          <select name="member_type" id="member_type" class="form-select" required>
            <option value="">Select Type</option>
            @if(in_array($group->type, ['team_members', 'mixed']))
              <option value="team_member">Team Member</option>
            @endif
            @if(in_array($group->type, ['contacts', 'mixed']))
              <option value="contact">Contact</option>
            @endif
          </select>
        </div>
        <div class="col-md-4">
          <select name="member_id" id="member_id" class="form-select" required>
            <option value="">Select Member</option>
          </select>
        </div>
        <div class="col-md-2">
          <select name="role" class="form-select">
            <option value="member">Member</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary w-100">Add</button>
        </div>
      </form>
    </div>
  </div>


  <!-- Members List -->
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
      <h6 class="mb-0">Current Members ({{ $members->count() }})</h6>
    </div>
    <div class="card-body p-0">
      @forelse($members as $member)
        <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
          <div class="d-flex align-items-center">
            <img src="https://ui-avatars.com/api/?name={{ $member->member->name ?? $member->member->user->name }}&background=6366f1&color=fff"
                 class="rounded-circle me-3" width="40" height="40">
            <div>
              <div class="fw-semibold">{{ $member->member->name ?? $member->member->user->name }}</div>
              <small class="text-muted">{{ $member->member->email ?? $member->member->user->email }}</small>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-secondary">{{ ucfirst($member->role) }}</span>
            <form method="POST" action="{{ route('groups.members.destroy', [$group, $member]) }}" class="d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
            </form>
          </div>
        </div>
      @empty
        <div class="text-center py-4">
          <p class="text-muted mb-0">No members in this group yet</p>
        </div>
      @endforelse
    </div>
  </div>
</div>

<script>
    document.getElementById('member_type').addEventListener('change', function() {
        const memberSelect = document.getElementById('member_id');
        const memberType = this.value;

        // Clear existing options
        memberSelect.innerHTML = '<option value="">Select Member</option>';

        if (memberType === 'team_member') {
            @json($availableTeamMembers).forEach(member => {
                const option = document.createElement('option');
                option.value = member.id;
                option.textContent = member.user.name;
                memberSelect.appendChild(option);
            });
        } else if (memberType === 'contact') {
            @json($availableContacts).forEach(contact => {
                const option = document.createElement('option');
                option.value = contact.id;
                option.textContent = contact.name;
                memberSelect.appendChild(option);
            });
        }
    });
</script>

@endsection
