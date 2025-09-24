
@extends('layout.base')

@section('title', $user->name . ' - Book a Meeting')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            {{-- User Profile Header --}}
            <div class="card mb-4">
                <div class="card-body text-center">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}"
                             alt="{{ $user->name }}"
                             class="rounded-circle mb-3"
                             style="width: 80px; height: 80px; object-fit: cover;">
                    @else
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 80px; height: 80px;">
                            <span class="text-white fs-2">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                    @endif

                    <h2 class="mb-2">{{ $user->name }}</h2>
                    @if($user->bio)
                        <p class="text-muted">{{ $user->bio }}</p>
                    @endif
                    <small class="text-muted">
                        <i class="fas fa-clock"></i>
                        {{ $user->timezone->display_name ?? 'UTC' }}
                    </small>
                </div>
            </div>

            {{-- Event Types --}}
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-calendar-alt"></i>
                        Select a Meeting Type
                    </h4>
                </div>
                <div class="card-body">
                    @if($eventTypes->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Available Meeting Types</h5>
                            <p class="text-muted">This user hasn't set up any public meeting types yet.</p>
                        </div>
                    @else
                        <div class="row">
                            @foreach($eventTypes as $eventType)
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start">
                                                <div class="flex-shrink-0">
                                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center"
                                                         style="width: 40px; height: 40px; background-color: {{ $eventType->color }};">
                                                        <i class="fas fa-video text-white"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-1">{{ $eventType->name }}</h6>
                                                    <p class="text-muted small mb-2">
                                                        <i class="fas fa-clock"></i> {{ $eventType->duration }} min
                                                        @if($eventType->location_type !== 'custom')
                                                            • <i class="fas fa-video"></i> {{ ucfirst(str_replace('_', ' ', $eventType->location_type)) }}
                                                        @endif
                                                    </p>
                                                    @if($eventType->description)
                                                        <p class="small text-muted mb-2">{{ Str::limit($eventType->description, 80) }}</p>
                                                    @endif

                                                    @if($eventType->requires_approval)
                                                        <span class="badge bg-warning text-dark small">
                                                            <i class="fas fa-clock"></i> Requires Approval
                                                        </span>
                                                    @else
                                                        <span class="badge bg-success small">
                                                            <i class="fas fa-check"></i> Instant Booking
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="mt-3">
                                                <a href="{{ route('public.booking.select-time', ['username' => $user->username, 'eventType' => $eventType->id]) }}"
                                                   class="btn btn-primary btn-sm w-100">
                                                    <i class="fas fa-calendar-plus"></i> Book Now
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
