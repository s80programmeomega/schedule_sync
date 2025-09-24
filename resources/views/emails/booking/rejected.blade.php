{{-- resources/views/emails/booking/rejected.blade.php --}}

@extends('emails.layout.base')

@section('title', 'Booking Request Declined')

@section('content')
<div style="text-align: center; margin-bottom: 30px;">
    <div style="background-color: #EF4444; color: white; padding: 15px; border-radius: 8px; display: inline-block; margin-bottom: 20px;">
        <h1 style="margin: 0; font-size: 24px;">❌ Booking Request Declined</h1>
    </div>
</div>

<div style="background-color: #f8f9fa; padding: 25px; border-radius: 8px; margin-bottom: 25px;">
    <h2 style="color: #333; margin-top: 0;">Unfortunately, {{ $host->name }} cannot accommodate your meeting request</h2>

    <div style="background-color: white; padding: 20px; border-radius: 6px; border-left: 4px solid #EF4444;">
        <h3 style="margin-top: 0; color: #333;">Requested Meeting Details</h3>

        <div style="margin: 15px 0; color: #666;">
            <strong>Event:</strong> {{ $eventType->name }}<br>
            <strong>📅 Date:</strong> {{ $requestedMeetingDetails['date'] }}<br>
            <strong>🕐 Time:</strong> {{ $requestedMeetingDetails['time'] }} ({{ $requestedMeetingDetails['duration'] }})
        </div>

        @if($rejectionReason)
        <div style="background-color: #FEF2F2; padding: 15px; border-radius: 6px; margin-top: 20px;">
            <strong style="color: #DC2626;">Reason:</strong>
            <p style="margin: 5px 0 0 0; color: #666;">{{ $rejectionReason }}</p>
        </div>
        @endif
    </div>
</div>

<div style="background-color: #f1f5f9; padding: 20px; border-radius: 8px; margin-bottom: 25px;">
    <h3 style="margin-top: 0; color: #333;">Don't Give Up!</h3>
    <p style="color: #666; line-height: 1.6; margin-bottom: 15px;">
        {{ $host->name }} may have other available time slots that work better.
        You can try booking a different time that might fit both of your schedules.
    </p>

    <div style="text-align: center;">
        <a href="{{ $bookAgainUrl }}"
           style="background-color: #3B82F6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
            📅 Try Another Time
        </a>
    </div>
</div>

<div style="border-top: 1px solid #e5e7eb; padding-top: 20px; margin-top: 30px; text-align: center;">
    <p style="color: #9CA3AF; font-size: 14px; margin: 0;">
        This notification was sent through {{ config('app.name') }}
    </p>
</div>
@endsection
