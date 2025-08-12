@extends('layout.base')

@section('title')
    Logout - ScheduleSync
@endsection

@section('content')
    <div class="container mt-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Logout</div>
                    <div class="card-body text-center">
                        <h5>You have been logged out!</h5>
                        <p>
                            You can <a href="{{ route('login') }}">login</a> again!
                        </p>
                        <p>
                            Or are you a new user? <a href="{{ route('register') }}">Register here</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
