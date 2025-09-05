@extends('layout.base')

@section('content')
<div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <div class="team-avatar me-3">
                @if($team->logo)
                    <img src="{{ asset('storage/' . $team->logo) }}" alt="{{ $team->name }}" class="rounded" style="width: 64px; height: 64px;">
                @else
                    <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; font-size: 1.5rem;">
                        {{ substr($team->name, 0, 2) }}
                    </div>
                @endif
            </div>
            <div>
                <h1 class="h3 mb-0 fw-bold">{{ $team->name }}</h1>
                <p class="text-muted mb-0">{{ $team->description }}</p>
            </div>
        </div>
        <div class="dropdown">
            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-gear me-2"></i>Manage
            </button>
            <ul class="dropdown-menu">
                @can('update', $team)
                <li><a class="dropdown-item" href="{{ route('teams.edit', $team) }}">Edit Team</a></li>
                @endcan
                @can('manageMembers', $team)
                <li><a class="dropdown-item" href="{{ route('teams.members.index', $team) }}">Manage Members</a></li>
                @endcan
                <li><a class="dropdown-item" href="{{ route('contacts.index', ['team_id' => $team->id]) }}">Team Contacts</a></li>
            </ul>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-people text-primary fs-2"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['total_members'] }}</h3>
                    <p class="text-muted mb-0">Members</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-event text-success fs-2"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['total_event_types'] }}</h3>
                    <p class="text-muted mb-0">Event Types</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-check text-info fs-2"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['total_bookings'] }}</h3>
                    <p class="text-muted mb-0">Bookings</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-person-lines-fill text-warning fs-2"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['total_contacts'] }}</h3>
                    <p class="text-muted mb-0">Contacts</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Members -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Team Members</h5>
                @can('manageMembers', $team)
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#inviteMemberModal">
                    <i class="bi bi-plus me-1"></i>Invite Member
                </button>
                @endcan
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <tbody>
                        @foreach($team->activeMembers->take(5) as $member)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $member->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($member->user->name) }}"
                                         class="rounded-circle me-3" width="40" height="40">
                                    <div>
                                        <h6 class="mb-0">{{ $member->user->name }}</h6>
                                        <small class="text-muted">{{ $member->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $member->role === 'owner' ? 'primary' : 'secondary' }}">
                                    {{ $member->role_display }}
                                </span>
                            </td>
                            <td class="pe-4">
                                <small class="text-muted">
                                    Joined {{ $member->joined_at?->diffForHumans() }}
                                </small>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($team->activeMembers->count() > 5)
            <div class="card-footer bg-light text-center">
                <a href="{{ route('teams.members.index', $team) }}" class="text-decoration-none">
                    View all {{ $team->activeMembers->count() }} members
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Recent Event Types -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Team Event Types</h5>
        </div>
        <div class="card-body">
            @forelse($team->eventTypes->take(3) as $eventType)
            <div class="d-flex align-items-center mb-3">
                <div class="event-type-icon me-3" style="background-color: {{ $eventType->color }}20; color: {{ $eventType->color }}">
                    <i class="bi bi-calendar-event"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-0">{{ $eventType->name }}</h6>
                    <small class="text-muted">{{ $eventType->duration }} min • {{ $eventType->bookings_count }} bookings</small>
                </div>
                <span class="badge bg-{{ $eventType->is_active ? 'success' : 'secondary' }}">
                    {{ $eventType->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            @empty
            <p class="text-muted mb-0">No event types created yet.</p>
            @endforelse
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
                            <option value="member">Member</option>
                            <option value="admin">Admin</option>
                            <option value="viewer">Viewer</option>
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
@endsection
