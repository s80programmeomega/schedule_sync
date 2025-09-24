{{-- resources/views/emails/booking/approved.blade.php --}}

@extends('emails.layout.base')

@section('title', 'Booking Approved')

@section('content')
<div style="text-align: center; margin-bottom: 30px;">
    <div style="background-color: #10B981; color: white; padding: 15px; border-radius: 8px; display: inline-block; margin-bottom: 20px;">
        <h1 style="margin: 0; font-size: 24px;">✅ Booking Approved!</h1>
    </div>
</div>

<div style="background-color: #f8f9fa; padding: 25px; border-radius: 8px; margin-bottom: 25px;">
    <h2 style="color: #333; margin-top: 0;">Your meeting with {{ $host->name }} is confirmed</h2>

    <div style="background-color: white; padding: 20px; border-radius: 6px; border-left: 4px solid #10B981;">
        <h3 style="margin-top: 0; color: #333;">{{ $eventType->name }}</h3>

        <div style="margin: 15px 0;">
            <strong>📅 Date:</strong> {{ $meetingDetails['date'] }}<br>
            <strong>🕐 Time:</strong> {{ $meetingDetails['time'] }} ({{ $meetingDetails['duration'] }})<br>
            <strong>🌍 Timezone:</strong> {{ $meetingDetails['timezone'] }}
        </div>

        @if($joinLink)
        <div style="margin: 20px 0;">
            <a href="{{ $joinLink }}"
               style="background-color: #3B82F6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                🔗 Join Meeting
            </a>
        </div>
        @endif

        @if($eventType->location_type === 'phone')
        <div style="margin: 15px 0;">
            <strong>📞 Phone:</strong> {{ $eventType->location_details }}
        </div>
        @endif
    </div>
</div>

<div style="background-color: #f1f5f9; padding: 20px; border-radius: 8px; margin-bottom: 25px;">
    <h3 style="margin-top: 0; color: #333;">What's Next?</h3>
    <ul style="color: #666; line-height: 1.6;">
        <li>📧 A calendar invite has been attached to this email</li>
        <li>📱 Add this meeting to your calendar</li>
        <li>🔔 You'll receive a reminder 24 hours and 1 hour before the meeting</li>
        @if($joinLink)
        <li>💻 Use the "Join Meeting" link above when it's time</li>
        @endif
    </ul>
</div>

<div style="text-align: center; margin: 30px 0;">
    <p style="color: #666; margin-bottom: 15px;">Need to make changes?</p>
    <a href="{{ $rescheduleUrl }}"
       style="background-color: #F59E0B; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; margin-right: 10px;">
        📅 Reschedule
    </a>
    <a href="{{ $cancelUrl }}"
       style="background-color: #EF4444; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px;">
        ❌ Cancel
    </a>
</div>

<div style="border-top: 1px solid #e5e7eb; padding-top: 20px; margin-top: 30px; text-align: center;">
    <p style="color: #9CA3AF; font-size: 14px; margin: 0;">
        This meeting was scheduled through {{ config('app.name') }}
    </p>
</div>
@endsection
