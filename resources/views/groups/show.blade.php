@extends('layout.base')

@section('content')
<div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
      <div class="group-icon me-3" style="background-color: {{ $group->color }}20; color: {{ $group->color }}; width: 48px; height: 48px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
        <i class="bi bi-collection fs-4"></i>
      </div>
      <div>
        <h1 class="h3 mb-0 fw-bold">{{ $group->name }}</h1>
        <p class="text-muted mb-0">{{ $group->teamMembers->count() + $group->contacts->count() }} members • {{ ucfirst(str_replace('_', ' ', $group->type)) }}</p>
      </div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('groups.members.index', $group) }}" class="btn btn-outline-primary">
        <i class="bi bi-people me-2"></i>Manage Members
      </a>
      <a href="{{ route('groups.edit', $group) }}" class="btn btn-primary">
        <i class="bi bi-pencil me-2"></i>Edit
      </a>
    </div>
  </div>

  @if($group->description)
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <p class="mb-0">{{ $group->description }}</p>
    </div>
  </div>
  @endif

  <div class="row">
    <div class="col-md-8">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <h6 class="mb-0">Members</h6>
          <a href="{{ route('groups.members.index', $group) }}" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body">
          @php
            $allMembers = collect();
            if($group->teamMembers) $allMembers = $allMembers->merge($group->teamMembers->take(3));
            if($group->contacts) $allMembers = $allMembers->merge($group->contacts->take(3));
          @endphp

          @forelse($allMembers as $member)
            <div class="d-flex align-items-center mb-3">
              @if($member->user ?? false)
                <img src="https://ui-avatars.com/api/?name={{ $member->user->name }}&background=6366f1&color=fff"
                     class="rounded-circle me-3" width="40" height="40">
                <div>
                  <div class="fw-semibold">{{ $member->user->name }}</div>
                  <small class="text-muted">{{ $member->user->email }}</small>
                </div>
              @else
                <img src="https://ui-avatars.com/api/?name={{ $member->name }}&background=6366f1&color=fff"
                     class="rounded-circle me-3" width="40" height="40">
                <div>
                  <div class="fw-semibold">{{ $member->name }}</div>
                  <small class="text-muted">{{ $member->email }}</small>
                </div>
              @endif
            </div>
          @empty
            <div class="text-center py-3">
              <i class="bi bi-people text-muted" style="font-size: 2rem;"></i>
              <p class="text-muted mb-2">No members yet</p>
              <a href="{{ route('groups.members.index', $group) }}" class="btn btn-sm btn-primary">Add Members</a>
            </div>
          @endforelse
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
          <h6 class="mb-0">Group Info</h6>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <small class="text-muted">Type</small>
            <div>{{ ucfirst(str_replace('_', ' ', $group->type)) }}</div>
          </div>
          <div class="mb-3">
            <small class="text-muted">Created</small>
            <div>{{ $group->created_at->format('M j, Y') }}</div>
          </div>
          @if($group->team)
          <div class="mb-3">
            <small class="text-muted">Team</small>
            <div>{{ $group->team->name }}</div>
          </div>
          @endif
          <div class="mb-0">
            <small class="text-muted">Created by</small>
            <div>{{ $group->createdBy->name }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
