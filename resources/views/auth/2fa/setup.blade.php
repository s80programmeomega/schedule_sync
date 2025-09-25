@extends('layout.base')

@section('title', '2FA Setup')

@section('content')
<div class="col-lg-10 col-12 px-lg-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Two-Factor Authentication Setup</div>
                <div class="card-body">
                    @if(!$user->google2fa_enabled)
                        <div class="alert alert-info">
                            <strong>Secure your account with 2FA!</strong>
                            Scan the QR code below with your authenticator app.
                        </div>

                        <div class="text-center mb-4">
                            {!! $qrCodeSvg !!}
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Secret Key (manual entry):</label>
                            <code>{{ $user->google2fa_secret }}</code>
                        </div>

                        <form method="POST" action="{{ route('2fa.enable') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="code" class="form-label">Verification Code</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                       id="code" name="code" placeholder="Enter 6-digit code" maxlength="6">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Enable 2FA</button>
                        </form>
                    @else
                        <div class="alert alert-success">
                            <strong>2FA is enabled!</strong> Your account is protected.
                        </div>

                        <form method="POST" action="{{ route('2fa.disable') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-danger">Disable 2FA</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
