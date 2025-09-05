@extends('layout.base')

@section('content')
<div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
    <div class="mb-4">
        <h1 class="h3 mb-0 fw-bold">{{ $team->name }} Settings</h1>
        <p class="text-muted mb-0">Manage team configuration and preferences</p>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <div class="nav flex-column nav-pills" role="tablist">
                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#general" type="button">
                    <i class="bi bi-gear me-2"></i>General
                </button>
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#members" type="button">
                    <i class="bi bi-people me-2"></i>Members
                </button>
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#permissions" type="button">
                    <i class="bi bi-shield-check me-2"></i>Permissions
                </button>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="general">
                    @include('teams.settings.general')
                </div>
                <div class="tab-pane fade" id="members">
                    @include('teams.settings.members')
                </div>
                <div class="tab-pane fade" id="permissions">
                    @include('teams.settings.permissions')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
