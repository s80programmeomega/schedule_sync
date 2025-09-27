@extends('layout.base')

@section('title')
    Register - ScheduleSync
@endsection

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Register</div>
                    <div class="card-body">
                        <!-- Social Registration Buttons -->
                        <div class="mb-4">
                            <div class="d-grid gap-2">
                                <a href="{{ route('social.redirect', 'google') }}" class="btn btn-outline-danger">
                                    <i class="bi bi-google me-2"></i>Sign up with Google
                                </a>
                                {{-- <a href="{{ route('social.redirect', 'linkedin') }}" class="btn btn-outline-info">
                                    <i class="bi bi-linkedin me-2"></i>Sign up with LinkedIn
                                </a> --}}
                                <a href="{{ route('social.redirect', 'github') }}" class="btn btn-outline-dark">
                                    <i class="bi bi-github me-2"></i>Sign up with GitHub
                                </a>
                                {{-- <a href="{{ route('social.redirect', 'facebook') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-facebook me-2"></i>Sign up with Facebook
                                </a> --}}
                            </div>

                            <div class="text-center my-3">
                                <span class="text-muted">or</span>
                            </div>
                        </div>

                        <!-- Regular Registration Form -->
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            @if ($errors->has('social'))
                                <div class="alert alert-danger">
                                    {{ $errors->first('social') }}
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                    id="username" name="username" value="{{ old('username') }}" required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" required>
                                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')" title="Show/Hide Password">
                                            <i id="passwordIcon" class="bi bi-eye-slash"></i>
                                        </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" required>
                                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_confirmation')" title="Show/Hide Password">
                                            <i id="confirmPasswordIcon" class="bi bi-eye-slash"></i>
                                        </button>
                                </div>
                            </div>

                            {{-- <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" required>
                            </div> --}}
                            <div class="mb-3">
                                <select name="timezone_id" class="form-select">
                                    <option value="">Select your timezone</option>
                                    @foreach(\App\Models\Timezone::all() as $timezone)
                                    <option value="{{ $timezone->id }}" {{ old('timezone_id')==$timezone->id ? 'selected' : '' }}>
                                        {{ $timezone->display_name }} ({{ $timezone->offset }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Register</button>
                            <a href="{{ route('login') }}" class="btn btn-link">Login</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId === 'password' ? 'passwordIcon' : 'confirmPasswordIcon');

            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'bi bi-eye';
            } else {
                field.type = 'password';
                icon.className = 'bi bi-eye-slash';
            }
        }
        </script>

@endsection
