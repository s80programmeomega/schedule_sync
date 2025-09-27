{{-- resources/views/public/booking/success.blade.php --}}
@extends('layout.base')

@section('title', 'Booking Submitted')

@section('content')
  <div class="container mt-4">
    @if (session('message'))
      <div class="alert alert-info mb-3">
        {{ session('message') }}
      </div>
    @endif

    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-body text-center">
            @if ($booking->isPendingApproval())
              <div class="text-warning mb-3">
                <i class="fas fa-clock fa-3x"></i>
              </div>
              <h3 class="text-warning">Booking Request Submitted</h3>
              <p class="mb-4">Your booking request has been submitted and is pending approval. You'll receive an email
                once it's reviewed.</p>
            @else
              <div class="text-success mb-3">
                <i class="fas fa-check-circle fa-3x"></i>
              </div>
              <h3 class="text-success">Booking Confirmed!</h3>
              <p class="mb-4">Your meeting has been confirmed. Check your email for details and calendar invite.</p>
            @endif

            <div class="card bg-light">
              <div class="card-body">
                <h5>{{ $eventType->name }}</h5>
                <p class="mb-1"><strong>Date:</strong> {{ $booking->booking_date->format('l, F j, Y') }}</p>
                <p class="mb-1"><strong>Time:</strong> {{ Carbon\Carbon::parse($booking->start_time)->format('g:i A') }}
                </p>
                <p class="mb-0"><strong>Duration:</strong> {{ $eventType->duration }} minutes</p>
              </div>
            </div>

            <div class="mt-4">
              <a href="{{ route('public.booking.index', $booking->user->username) }}" class="btn btn-primary">
                Book Another Meeting
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
