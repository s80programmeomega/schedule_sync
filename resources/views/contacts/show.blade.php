@extends('layout.base')

@section('title')
View Contact
@endsection

@section('content')
<div class="col-lg-10 col-12 col-lg-10 py-4 px-4 px-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">{{ $contact->name }}</h1>
            <p class="text-muted mb-0">Contact Details</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-2"></i>Edit
            </a>
            <a href="{{ route('contacts.index', ['team_id' => $contact->team_id]) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-10">
            <!-- Contact Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <img src="{{ $contact->avatar_url }}" class="rounded-circle me-3" width="80" height="80">
                        <div>
                            <h4 class="mb-1">{{ $contact->name }}</h4>
                            <p class="text-muted mb-0">{{ $contact->email }}</p>
                            @if($contact->phone)
                            <p class="text-muted mb-0">{{ $contact->phone }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        @if($contact->company)
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Company</label>
                            <p class="mb-0">{{ $contact->company }}</p>
                        </div>
                        @endif
                        @if($contact->job_title)
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Job Title</label>
                            <p class="mb-0">{{ $contact->job_title }}</p>
                        </div>
                        @endif
                        @if($contact->team)
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Team</label>
                            <p class="mb-0">{{ $contact->team->name }}</p>
                        </div>
                        @endif
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted ">Total Bookings</label>
                            <p class="mb-0 text-danger fw-bold">{{ $contact->total_bookings }}</p>
                        </div>
                    </div>

                    @if($contact->notes)
                    <div class="mt-3">
                        <label class="form-label fw-semibold text-muted">Notes</label>
                        <p class="mb-0">{{ $contact->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Recent Bookings -->
            @if($contact->bookingAttendances->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Recent Bookings</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Event</th>
                                    <th>Date</th>
                                    <th>Start Time</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contact->bookingAttendances->take(5) as $attendance)
                                <tr>
                                    <td class="ps-4">{{ $attendance->booking->eventType->name }}</td>
                                    <td>
                                        {{ $attendance->booking->booking_date->format('Y-d-m') }}
                                    </td>
                                    <td>{{ $attendance->booking->start_time}}</td>
                                    <td>{{ $attendance->booking->eventType->duration}} min</td>
                                    <td>
                                        <span class="badge bg-{{ $attendance->booking->status === 'confirmed' ? 'success' : 'warning' }}">
                                            {{ ucfirst($attendance->booking->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
