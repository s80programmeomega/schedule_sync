@extends('layout.base')

@section('title', 'View Availability - ScheduleSync')

@section('content')
<div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('availability.index') }}" class="btn btn-light me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 mb-0 fw-bold">Availability Details</h1>
            <p class="text-muted mb-0">View availability information</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Day of Week</label>
                        <p class="mb-0">{{ ucfirst($availability->day_of_week) }}</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Start Time</label>
                            <p class="mb-0">{{ $availability->start_time }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">End Time</label>
                            <p class="mb-0">{{ $availability->end_time }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Status</label>
                        <p class="mb-0">
                            <span class="badge {{ $availability->is_available ? 'bg-success' : 'bg-secondary' }}">
                                {{ $availability->is_available ? 'Available' : 'Unavailable' }}
                            </span>
                        </p>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('availability.edit', $availability) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-2"></i> Edit
                        </a>
                        <a href="{{ route('availability.index') }}" class="btn btn-light">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
