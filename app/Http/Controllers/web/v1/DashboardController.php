<?php

namespace App\Http\Controllers\web\v1;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\EventType;
use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Services\AvailabilityService;

/**
 * Dashboard Controller
 * Handles the main dashboard view with statistics and overview
 */
class DashboardController extends Controller
{

    protected $availabilityService;

    public function __construct(AvailabilityService $availabilityService)
    {
        $this->availabilityService = $availabilityService;
    }

    /**
     * Display the dashboard with user statistics
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $duration = $request->get('duration', 30);
        $filter = $request->get('filter', 'day');

        // Apply filter logic
        $query = Booking::where('user_id', $user->id)
            ->with('eventType')
            ->where('status', 'scheduled')
            ->where('start_time', '>', now());


        switch ($filter) {
            case 'week':
                $startOfWeek = now()->startOfWeek();
                $endOfWeek = now()->endOfWeek();
                $query = $query->where(function ($q) use ($startOfWeek, $endOfWeek) {
                    $q->where('booking_date', '>=', $startOfWeek->toDateString())
                        ->where('booking_date', '<=', $endOfWeek->toDateString());
                });
                break;
            case 'month':
                $startOfMonth = now()->startOfMonth();
                $endOfMonth = now()->endOfMonth();
                $query = $query->where(function ($q) use ($startOfMonth, $endOfMonth) {
                    $q->where('booking_date', '>=', $startOfMonth->toDateString())
                        ->where('booking_date', '<=', $endOfMonth->toDateString());
                });
                break;
            default: // day
                $query = $query->whereDate('booking_date', today());
                break;
        }


        // switch ($filter) {
        //     case 'week':
        //         $query =  $query->whereBetween('booking_date', [now()->startOfWeek(), now()->endOfWeek()]);
        //         break;
        //     case 'month':
        //         $query = $query->whereBetween('booking_date', [now()->startOfMonth(), now()->endOfMonth()]);
        //         break;
        //     default: // day
        //         $query = $query->whereDate('booking_date', today()->toDateString());
        //         break;
        // }

        // Get booking statistics

        // $upcomingMeetings = $query->get();

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
            ->whereDate('booking_date', today())
            ->where('status', 'scheduled')
            ->whereTime('start_time', '>=', now()->format('H:i A'))
            ->orderBy('start_time')
            ->get();

        $todaysAvailability = Availability::whereDate('availability_date', today())->first();
        $availableSlots = $this->availabilityService->getAvailableSlots(
            $user->id,
            today()->toDateString(),
            $duration // Use the selected duration instead of hardcoded 30
        );


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
            'upcomingBookings',
            'availableSlots',
            'duration',
        ));
    }
}
