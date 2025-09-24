@extends('bookings.base-show')

@section('title', 'Pending Booking Details - ScheduleSync')

@section('actions')
    @if($booking->isPendingApproval() && $booking->user_id === auth()->id())
    <div class="mt-3 pt-3 border-top">
        <h5>Approval Required</h5>
        <div class="d-flex gap-2">
            <form method="POST" action="{{ route('bookings.approve', $booking) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-2"></i> Approve
                </button>
            </form>
            <button class="btn btn-danger" onclick="rejectBooking({{ $booking->id }})">
                <i class="bi bi-x-circle me-2"></i> Reject
            </button>
        </div>
    </div>
    @endif
@endsection
