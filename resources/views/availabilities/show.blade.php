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
            <div class="col">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Date</label>
                            <p class="mb-0">
                                {{ \Carbon\Carbon::parse(ucfirst($availability->availability_date))->format('F d, Y') }}
                            </p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Day of Week</label>
                            <p class="mb-0">{{ ucfirst($availability->day_of_week) }}</p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Time Zone</label>
                            <p class="mb-0">{{ ucfirst($availability->timezone->display_name) }}</p>
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
        <div class="row col py-5">
            <!-- Accordion for Time Slots and Bookings -->
            <div class="col-2 mb-3">
                <label class="form-label fw-semibold">Slot Duration</label>
                <select class="form-select" id="durationSelect" onchange="updateDuration()">
                    <option value="15" {{ $duration == 15 ? 'selected' : '' }}>15 Minutes</option>
                    <option value="30" {{ $duration == 30 ? 'selected' : '' }}>30 Minutes</option>
                    <option value="45" {{ $duration == 45 ? 'selected' : '' }}>45 Minutes</option>
                    <option value="60" {{ $duration == 60 ? 'selected' : '' }}>1 Hour</option>
                    <option value="90" {{ $duration == 90 ? 'selected' : '' }}>1 Hour 30 Minutes</option>
                    <option value="120" {{ $duration == 120 ? 'selected' : '' }}>2 Hours</option>
                </select>
            </div>
            <script>
                function updateDuration() {
                    const duration = document.getElementById('durationSelect').value;
                    window.location.href = `{{ route('availability.show', $availability) }}?duration=${duration}`;
                }
            </script>
            <div class="accordion" id="availabilityAccordion">
                <!-- Time Slots Section -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse"
                            data-bs-target="#timeSlotsCollapse">
                            Time Slots
                        </button>
                    </h2>
                    <div id="timeSlotsCollapse" class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            <div class="row g-2">
                                @foreach ($timeSlots as $slot)
                                    <div class="col-md-3">
                                        @if ($slot['is_occupied'])
                                            @php
                                                $statusColor = match ($slot['booking']->status) {
                                                    'scheduled' => 'btn-primary',
                                                    'completed' => 'btn-success',
                                                    'cancelled' => 'btn-danger',
                                                    'no_show' => 'btn-warning',
                                                    default => 'btn-secondary',
                                                };
                                              @endphp
                                            <a href="{{ route('bookings.show', $slot['booking']) }}" target="_blank"
                                                class="btn {{ $statusColor }} btn-sm w-100" style="cursor: pointer;">
                                                {{ $slot['formatted_time'] }}
                                                <i class="bi bi-box-arrow-up-right ms-1"></i>
                                            </a>
                                        @else
                                            <div class="btn btn-success btn-sm w-100" style="cursor: default;">
                                                {{ $slot['formatted_time'] }}
                                            </div>
                                        @endif


                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bookings Section -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse"
                            data-bs-target="#bookingsCollapse">
                            Bookings (
                            <span class="text-danger">
                                {{ $availability->bookings()->count() }}
                            </span>
                            )
                        </button>
                    </h2>
                    <div id="bookingsCollapse" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            @forelse($availability->bookings()->get() as $booking)
                                <div class="card mb-2">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ $booking->eventType->name }}</h6>
                                                <small class="text-muted">{{ $booking->start_time }} -
                                                    {{ $booking->end_time }}</small>
                                            </div>
                                            <a href="{{ route('bookings.show', $booking) }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary">
                                                View <i class="bi bi-box-arrow-up-right ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">No bookings for this availability.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
