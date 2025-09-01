@extends('layout.base')

@section('title')
Verify Your Email
@endsection

@section('content')
<div class="col col-lg-10">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="bi bi-envelope-open me-2"></i>
                        Verify Your Email Address
                    </h4>
                </div>

                <div class="card-body">
                    {{-- Success message when verification email is sent --}}
                    @if (session('status') == 'verification-link-sent')
                    <div class="alert alert-success" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        A new verification link has been sent to your email address.
                    </div>
                    @endif

                    <div class="text-center mb-4">
                        <i class="bi bi-envelope-open text-primary" style="font-size: 4rem;"></i>
                    </div>

                    <h5 class="text-center mb-3">Check Your Email</h5>

                    <p class="text-muted text-center mb-4">
                        Before proceeding, please check your email for a verification link.
                        We've sent a verification email to <strong>{{ auth()->user()->email }}</strong>.
                    </p>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Why verify your email?</strong>
                        <ul class="mb-0 mt-2">
                            <li>Secure your account</li>
                            <li>Receive booking notifications</li>
                            <li>Reset your password if needed</li>
                            <li>Access all ScheduleSync features</li>
                        </ul>
                    </div>

                    <div class="text-center">
                        {{-- Resend verification email form --}}
                        <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-2"></i>
                                Resend Verification Email
                            </button>
                        </form>
                    </div>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="text-muted mb-2">Need to update your email address?</p>
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-pencil me-2"></i>
                            Edit Profile
                        </a>
                    </div>

                    {{-- Logout option --}}
                    <div class="text-center mt-3">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link text-muted">
                                <i class="bi bi-box-arrow-right me-1"></i>
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Help section --}}
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="bi bi-question-circle me-2"></i>
                        Didn't receive the email?
                    </h6>
                    <ul class="list-unstyled mb-0">
                        <li><i class="bi bi-check text-success me-2"></i>Check your spam/junk folder</li>
                        <li><i class="bi bi-check text-success me-2"></i>
                            Make sure <strong>{{ config('mail.from.address') }}</strong> is not blocked
                        </li>
                        <li><i class="bi bi-check text-success me-2"></i>Wait a few minutes for the email to arrive</li>
                        <li><i class="bi bi-check text-success me-2"></i>Click "Resend Verification Email" above</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-refresh page every 30 seconds to check if email was verified
    // This provides better UX if user verifies email in another tab
    setInterval(function() {
        fetch('{{ route('verification.notice') }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(response => {
            if (response.redirected) {
                window.location.href = response.url;
            }
        });
    }, 30000);
</script>
@endpush
