@extends('layout.base')

@section('title')
    Login - ScheduleSync
@endsection

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Login</div>
                    <div class="card-body">
                        <!-- Social Login Buttons -->
                        <div class="mb-4">
                            <div class="d-grid gap-2">
                                <a href="{{ route('social.redirect', 'google') }}" class="btn btn-outline-danger">
                                    <i class="fab fa-google me-2"></i>Continue with Google
                                </a>
                                <a href="{{ route('social.redirect', 'linkedin') }}" class="btn btn-outline-info">
                                    <i class="fab fa-linkedin me-2"></i>Continue with LinkedIn
                                </a>
                                <a href="{{ route('social.redirect', 'github') }}" class="btn btn-outline-dark">
                                    <i class="fab fa-github me-2"></i>Continue with GitHub
                                </a>
                                {{-- <a href="{{ route('social.redirect', 'facebook') }}" class="btn btn-outline-primary">
                                    <i class="fab fa-facebook me-2"></i>Continue with Facebook
                                </a> --}}
                            </div>

                            <div class="text-center my-3">
                                <span class="text-muted">or</span>
                            </div>
                        </div>

                        <!-- Regular Login Form -->
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            @if ($errors->has('social'))
                                <div class="alert alert-danger">
                                    {{ $errors->first('social') }}
                                </div>
                            @endif

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
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Login</button>
                            <a href="{{ route('register') }}" class="btn btn-link">Register</a>
                            <div class="text-center mt-3">
                                <a href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
