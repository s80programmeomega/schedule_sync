@extends('layout.base')

@section('title')
Add Member
@endsection

@section('content')
<div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
  <div class="mb-4">
    <h1 class="h3 mb-0 fw-bold">Add Member to {{ $group->name }}</h1>
    <p class="text-muted mb-0">Add a new member to this group</p>
  </div>

  <div class="row">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <form method="POST" action="{{ route('groups.members.store', $group) }}">
            @csrf

            <div class="mb-4">
              <label for="member_type" class="form-label fw-semibold">Member Type</label>
              <select class="form-select @error('member_type') is-invalid @enderror" id="member_type" name="member_type" required>
                <option value="">Select Type</option>
                @if(in_array($group->type, ['team_members', 'mixed']))
                  <option value="team_member" {{ old('member_type') === 'team_member' ? 'selected' : '' }}>Team Member</option>
                @endif
                @if(in_array($group->type, ['contacts', 'mixed']))
                  <option value="contact" {{ old('member_type') === 'contact' ? 'selected' : '' }}>Contact</option>
                @endif
              </select>
              @error('member_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
              <label for="member_id" class="form-label fw-semibold">Select Member</label>
              <select class="form-select @error('member_id') is-invalid @enderror" id="member_id" name="member_id" required>
                <option value="">Select Member</option>
              </select>
              @error('member_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
              <label for="role" class="form-label fw-semibold">Role</label>
              <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                <option value="member" {{ old('role') === 'member' ? 'selected' : '' }}>Member</option>
                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
              </select>
              @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex gap-3">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-check me-2"></i>Add Member
              </button>
              <a href="{{ route('groups.members.index', $group) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('member_type').addEventListener('change', function() {
    const memberSelect = document.getElementById('member_id');
    const memberType = this.value;

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
