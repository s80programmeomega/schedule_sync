@extends('layout.base')

@section('title', '2FA Verification')

@section('content')
<div class="col-lg-10 col-12 py-5 mt-lg-5 px-lg-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Two-Factor Authentication</div>
                <div class="card-body">
                    <p>Please enter the 6-digit code from your authenticator app.</p>

                    <form method="POST" action="{{ route('2fa.validate') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="code" class="form-label">Verification Code</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                   id="code" name="code" placeholder="000000" maxlength="6" autofocus>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Verify</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
