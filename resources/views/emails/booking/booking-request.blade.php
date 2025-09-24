@extends('emails.layout.base')

@section('content')
<h2>New Booking Request</h2>
<p>You have a new booking request for <strong>{{ $eventType->name }}</strong></p>

<div class="booking-details">
    <p><strong>Attendee:</strong> {{ $attendee->name }}</p>
    <p><strong>Email:</strong> {{ $attendee->email }}</p>
    <p><strong>Date:</strong> {{ $booking->booking_date->format('l, F j, Y') }}</p>
    <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }}</p>
    <p>
        <strong>
            {{ $booking->eventType->duration }}
        </strong>
            minutes
    </p>
    <p><strong>Location:</strong> {{ $booking->eventType->location_type }}</p>
    <p><strong>Notes:</strong> {{ $attendee_notes }}</p>
</div>

<div class="actions">
    <a href="{{ $details_url }}" class="btn btn-primary">View Details</a>
    {{-- <a href="{{ $approveUrl }}" class="btn btn-success">Approve</a>
    <a href="{{ $rejectUrl }}" class="btn btn-danger">Reject</a> --}}
</div>
@endsection
