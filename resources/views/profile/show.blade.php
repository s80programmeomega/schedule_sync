@extends('layout.base')

@section('title', 'Profile - ScheduleSync')

@section('content')
<div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold">User Profile</h1>
        <a href="{{ route('profile.edit') }}" class="btn btn-primary">
            <i class="bi bi-pencil me-2"></i>Edit Profile
        </a>
    </div>

    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header">
            <div class="row mb-3">
                <div class="col-5"></div>
                <div class="col-2">
                    @if($user->avatar && Str::startsWith($user->avatar, 'https'))
                    <img src="{{ $user->avatar }}" alt="avatar" class="rounded-circle" width="64" height="64">
                    @elseif($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="avatar" class="img img-fluid rounded-circle ">
                    @else
                    <img src="https://ui-avatars.com/api/?name={{ $user->name }}&background=6366f1&color=fff" class="rounded-circle"
                        width="64" height="64" alt="Profile" />
                    @endif
                </div>
                <div class="col-5"></div>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row mb-3">
                <div class="col-sm-3 fw-semibold">Name:</div>
                <div class="col-sm-9">{{ $user->name }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-semibold">Username:</div>
                <div class="col-sm-9">{{ $user->username }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-semibold">Email:</div>
                <div class="col-sm-9">{{ $user->email }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-semibold">Timezone:</div>
                <div class="col-sm-9">{{ $user->timezone->display_name ?? 'Not set' }}</div>
            </div>
            @if($user->bio)
            <div class="row mb-3">
                <div class="col-sm-3 fw-semibold">Bio:</div>
                <div class="col-sm-9">{{ $user->bio }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
