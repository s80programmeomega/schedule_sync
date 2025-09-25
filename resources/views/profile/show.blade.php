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
            @php
              $avatar = auth()->user()->avatar;
              $avatarSrc = $avatar
                  ? (Str::startsWith($avatar, 'https')
                      ? $avatar
                      : asset('storage/' . $avatar))
                  : 'https://ui-avatars.com/api/?name=' .
                      urlencode(auth()->user()->name) .
                      '&background=6366f1&color=fff';
            @endphp

            <a href="{{ $avatarSrc }}" target="_blank" class="d-inline-block">
              <img src="{{ $avatarSrc }}" alt="avatar" class="img img-fluid rounded-circle" width="150"
                height="150" style="cursor: pointer;">
            </a>


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
      @if ($user->bio)
        <div class="row mb-3">
          <div class="col-sm-3 fw-semibold">Bio:</div>
          <div class="col-sm-9">{{ $user->bio }}</div>
        </div>
      @endif
    </div>

    <!-- 2FA section -->
    <div class="card mt-4">
      <div class="card-header">
        <h5>Two-Factor Authentication</h5>
      </div>
      <div class="card-body">
        @if ($user->has2FA())
          <div class="d-flex align-items-center">
            <span class="badge bg-success me-2">Enabled</span>
            <span>Your account is protected with 2FA</span>
            <a href="{{ route('2fa.setup') }}" class="btn btn-sm btn-outline-secondary ms-auto">Manage</a>
          </div>
        @else
          <div class="d-flex align-items-center">
            <span class="badge bg-warning me-2">Disabled</span>
            <span>Secure your account with 2FA</span>
            <a href="{{ route('2fa.setup') }}" class="btn btn-sm btn-primary ms-auto">Enable 2FA</a>
          </div>
        @endif
      </div>
    </div>


  </div>

@endsection
