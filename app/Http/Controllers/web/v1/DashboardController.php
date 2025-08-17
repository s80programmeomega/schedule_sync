<?php

namespace App\Http\Controllers\web\v1;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\EventType;
use App\Http\Controllers\Controller;

/**
 * Dashboard Controller
 * Handles the main dashboard view with statistics and overview
 */
class DashboardController extends Controller
{
    /**
     * Display the dashboard with user statistics
     */
    public function index()
    {
        $user = auth()->user();

        // Get booking statistics
        $upcomingMeetings = Booking::where('user_id', $user->id)
            ->with('eventType')
            ->where('status', 'scheduled')
            ->where('start_time', '>', now())
            ->get();

        // dd($upcomingMeetings);

        $completedMeetings = Booking::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $cancelledMeetings = Booking::where('user_id', $user->id)
            ->where('status', 'cancelled')
            ->count();

        // Get recent event types
        $eventTypes = EventType::where('user_id', $user->id)
            ->where('is_active', true)
            ->latest()
            ->take(3)
            ->get();

        // Get today's meetings
        $todaysBookings = Booking::where('user_id', $user->id)
            ->whereDate('start_time', today())
            ->where('status', 'scheduled')
            ->orderBy('start_time')
            ->get();

        // Get upcoming meetings for the table
        $upcomingBookings = Booking::where('user_id', $user->id)
            ->where('status', 'scheduled')
            ->where('start_time', '>', now())
            ->with('eventType')
            ->orderBy('start_time')
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'upcomingMeetings',
            'completedMeetings',
            'cancelledMeetings',
            'eventTypes',
            'todaysBookings',
            'upcomingBookings'
        ));
    }
}
