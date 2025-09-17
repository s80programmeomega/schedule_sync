@extends('layout.base')

@section('title', 'Available Slots - ScheduleSync')

@section('content')
<div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('availability.index') }}" class="btn btn-light me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 mb-0 fw-bold">Available Time Slots</h1>
            <p class="text-muted mb-0">{{ $availability->availability_date->format('F d, Y') }} - {{
                $availability->start_time }} to {{ $availability->end_time }}</p>
        </div>
    </div>

    <!-- Duration Selector -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('availability.slots', $availability) }}"
                class="d-flex align-items-center gap-3">
                <label class="form-label mb-0 fw-semibold">Slot Duration:</label>
                <select name="duration" class="form-select pe-5" style="width: auto;" onchange="this.form.submit()">
                    <option  value="15" {{ $duration==15 ? 'selected' : '' }}>15 Minutes</option>
                    <option  value="30" {{ $duration==30 ? 'selected' : '' }}>30 Minutes</option>
                    <option  value="45" {{ $duration==45 ? 'selected' : '' }}>45 Minutes</option>
                    <option  value="60" {{ $duration==60 ? 'selected' : '' }}>1 Hour</option>
                    <option  value="90" {{ $duration==90 ? 'selected' : '' }}>1 Hour 30 Minutes</option>
                    <option  value="120" {{ $duration==120 ? 'selected' : '' }}>2 Hours</option>
                </select>
            </form>
        </div>
    </div>

    <div class="row" data-aos="zoom-in" data-aos-duration="1000">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    @if(count($slots) > 0)
                    <div class="row g-3">
                        @foreach($slots as $slot)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card border border-primary-subtle">
                                <div class="card-body text-center py-3">
                                    <i class="bi bi-clock text-primary mb-2" style="font-size: 1.5rem;"></i>
                                    <h6 class="mb-0">{{ $slot['formatted_time'] }}</h6>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                        @if ($availability->is_available)
                            <div class="text-center py-5">
                                <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                                <h5 class="mt-3 mb-2">No Available Slots</h5>
                                <p class="text-muted">All time slots are either booked or in the past</p>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                                <h5 class="mt-3 mb-2">Unavailable Period</h5>
                                <p class="text-muted">This availability period is marked as unavailable</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Availability Details</h6>
                    <div class="mb-3">
                        <small class="text-muted">Date</small>
                        <p class="mb-0">{{ $availability->availability_date->format('F d, Y') }}</p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Time Range</small>
                        <p class="mb-0">{{ $availability->start_time }} - {{ $availability->end_time }}</p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Status</small>
                        <p class="mb-0">
                            <span class="badge {{ $availability->is_available ? 'bg-success' : 'bg-secondary' }}">
                                {{ $availability->is_available ? 'Available' : 'Unavailable' }}
                            </span>
                        </p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Total Slots</small>
                        <p class="mb-0">{{ count($slots) }} available</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
