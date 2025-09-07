@extends('emails.layout.base')

@section('title', 'Team Invitation - ScheduleSync')

@section('header-content')
<p class="mb-0 opacity-75">You've been invited to join a team!</p>
@endsection

@section('content')
<!-- Team Invitation Alert -->
<div class="alert alert-primary d-flex align-items-center mb-4" role="alert">
    <i class="bi bi-people-fill me-3 fs-4"></i>
    <div>
        <h4 class="alert-heading mb-1">Team Invitation</h4>
        <p class="mb-0">You've been invited to collaborate with <strong>{{ $team->name }}</strong></p>
    </div>
</div>

<p class="mb-4">Hi there!</p>

<!-- Team Details Card -->
<div class="meeting-details-card">
    <div class="d-flex align-items-center mb-3">
        @if($team->logo)
        <img src="{{ asset('storage/' . $team->logo) }}" alt="{{ $team->name }}"
             class="rounded me-3" style="width: 48px; height: 48px; object-fit: cover;">
        @else
        <div class="email-icon">
            <i class="bi bi-people"></i>
        </div>
        @endif
        <h3 class="mb-0 fw-semibold">{{ $team->name }}</h3>
    </div>

    <div class="row g-3">
        <div class="col-sm-6">
            <div class="d-flex align-items-center">
                <i class="bi bi-person-badge text-primary me-2"></i>
                <div>
                    <small class="text-muted d-block">Invited by</small>
                    <strong>{{ $inviter->name }}</strong>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="d-flex align-items-center">
                <i class="bi bi-shield-check text-primary me-2"></i>
                <div>
                    <small class="text-muted d-block">Role</small>
                    <strong>{{ $role }}</strong>
                </div>
            </div>
        </div>
    </div>

    @if($team->description)
    <div class="mt-4 p-3 bg-white rounded border-start border-primary border-3">
        <small class="text-muted d-block mb-1">About this team:</small>
        <em>"{{ $team->description }}"</em>
    </div>
    @endif
</div>

<!-- Accept Invitation Button -->
<div class="text-center my-4">
    <a href="{{ $acceptUrl }}" class="btn btn-primary btn-lg">
        <i class="bi bi-check-circle me-2"></i>Accept Invitation
    </a>
</div>

<!-- Expiration Notice -->
<div class="alert alert-warning" role="alert">
    <h6 class="alert-heading">
        <i class="bi bi-clock me-2"></i>Time Sensitive
    </h6>
    <p class="mb-0">This invitation expires on <strong>{{ $expiresAt->format('M j, Y \a\t g:i A') }}</strong>.</p>
</div>

<hr class="my-4">

<div class="mt-4">
    <p class="mb-2">If you didn't expect this invitation, you can safely ignore this email.</p>
    <p class="mb-0">
        <strong>{{ $inviter->name }}</strong><br>
        <a href="mailto:{{ $inviter->email }}" class="text-decoration-none" style="color: var(--primary);">
            {{ $inviter->email }}
        </a>
    </p>
</div>
@endsection
