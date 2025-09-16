@extends('layout.base')

@section('title')
Teams - Schedule Sync
@endsection

@section('content')
<div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Teams</h1>
            <p class="text-muted mb-0">Manage your team workspaces</p>
        </div>
        <a href="{{ route('teams.create') }}" class="btn btn-primary">
            <i class="bi bi-plus me-2"></i> Create Team
        </a>
    </div>

    <div class="row">
        @forelse($teams as $team)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="team-avatar me-3">
                            @if($team->logo)
                                <img src="{{ asset('storage/' . $team->logo) }}" alt="{{ $team->name }}" class="rounded">
                            @else
                                <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    {{ substr($team->name, 0, 2) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $team->name }}</h5>
                            <small class="text-muted">{{ $team->activeMembers->count() }} members</small>
                        </div>
                    </div>

                    @if($team->description)
                    <p class="text-muted small mb-3">{{ Str::limit($team->description, 100) }}</p>
                    @endif

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-{{ $team->userHasRole(auth()->user(), 'owner') ? 'primary' : 'secondary' }}">
                            {{ ucfirst(auth()->user()->getRoleInTeam($team)) }}
                        </span>
                        <a href="{{ route('teams.show', $team) }}" class="btn btn-sm btn-outline-primary">
                            View Team
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3 mb-2">No Teams Yet</h4>
                <p class="text-muted mb-4">Create your first team to start collaborating</p>
                <a href="{{ route('teams.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus me-2"></i> Create Team
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
