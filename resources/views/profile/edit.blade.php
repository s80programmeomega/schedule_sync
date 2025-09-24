@extends('layout.base')

@section('title', 'Edit Profile - ScheduleSync')

@section('content')
  <div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
    <div class="d-flex align-items-center mb-4">
      <a href="{{ route('profile.show') }}" class="btn btn-light me-3">
        <i class="bi bi-arrow-left"></i>
      </a>
      <h1 class="h3 mb-0 fw-bold">Edit Profile</h1>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body p-4">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
          @csrf
          @method('PATCH')

          <div class="card-header">
            <div class="row justify-content-start mb-3">
              <div class="col-4"></div>
              <div class="col-4">
                @php
                  $avatar = auth()->user()->avatar;
                  $src = $avatar
                      ? (Str::startsWith($avatar, 'https')
                          ? $avatar
                          : asset('storage/' . $avatar))
                      : 'https://ui-avatars.com/api/?name=' .
                          urlencode(auth()->user()->name) .
                          '&background=6366f1&color=fff';
                @endphp
                <a href="{{ $src }}" target="_blank" class="d-inline-block">
                  <img src="{{ $src }}" alt="avatar" class="img img-fluid rounded-circle"
                    style="cursor: pointer;">
                </a>


                <div class="col-4"></div>
              </div>
            </div>
            <div class="mb-3">
              {{-- <label for="avatar" class="form-label">Profile Picture</label> --}}
              <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar"
                name="avatar" value="{{ old('avatar', auth()->user()->avatar) }}" accept="image/*">

              @if (auth()->user()->avatar)
                <small class="text-muted">Current:
                  {{ auth()->user()->original_avatar_name ?? basename(auth()->user()->avatar) }}</small>
                <div class="form-check mt-2">
                  <input class="form-check-input" type="checkbox" id="clear_avatar" name="clear_avatar" value="1">
                  <label class="form-check-label" for="clear_avatar">
                    Remove current avatar
                  </label>
                </div>
              @endif

              @error('avatar')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror

            </div>
          </div>
          <div class="mb-3">
            <label for="name" class="form-label">Name *</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
              value="{{ old('name', auth()->user()->name) }}" required>
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="username" class="form-label">Username *</label>
            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username"
              name="username" value="{{ old('username', auth()->user()->username) }}" required>
            @error('username')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email *</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
              value="{{ old('email', auth()->user()->email) }}" required>
            @error('email')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="timezone_id" class="form-label">Timezone *</label>
            <select name="timezone_id" class="form-select @error('timezone_id') is-invalid @enderror" required>
              <option value="">Select timezone</option>
              @foreach ($timezones as $timezone)
                <option value="{{ $timezone->id }}"
                  {{ old('timezone_id', auth()->user()->timezone_id) == $timezone->id ? 'selected' : '' }}>
                  {{ $timezone->display_name }}
                </option>
              @endforeach
            </select>
            @error('timezone_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="bio" class="form-label">Bio</label>
            <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="3">{{ old('bio', auth()->user()->bio) }}</textarea>
            @error('bio')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-globe"></i> Public Booking Settings
                </h5>
            </div>
            <div class="card-body">
                <div class="form-check form-switch">
                    <input class="form-check-input"
                           type="checkbox"
                           id="is_public"
                           name="is_public"
                           value="1"
                           {{ old('is_public', $user->is_public) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_public">
                        <strong>Allow Public Bookings</strong>
                    </label>
                </div>
                <small class="text-muted">
                    When enabled, external users can view your availability and request meetings through your public booking link.
                </small>

                @if($user->is_public)
                    <div class="mt-3 p-3 bg-light rounded">
                        <strong>Your Public Booking URL:</strong><br>
                        <code>{{ $user->public_booking_url }}</code>
                        <button type="button"
                                class="btn btn-sm btn-outline-primary ms-2"
                                onclick="copyToClipboard('{{ $user->public_booking_url }}')">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                @endif
            </div>
        </div>

          <hr class="my-4">
          <h5>Change Password</h5>

          <div class="mb-3">
            <label for="current_password" class="form-label">Current Password</label>
            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
              id="current_password" name="current_password">
            @error('current_password')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">New Password</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
              name="password">
            @error('password')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm New Password</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Update Profile</button>
            <a href="{{ route('profile.show') }}" class="btn btn-light">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Show success message
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-success');

            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-primary');
            }, 2000);
        });
    }
    </script>
@endsection
