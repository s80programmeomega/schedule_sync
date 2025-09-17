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
        if ($request->user()->id !== auth()->user()->id) {
            abort(403);
        }
        $user = auth()->user();
        $duration = $request->get('duration', 30);
        $filter = $request->get('filter', 'day');

        $todaysAvailability = Availability::where('user_id', $user->id)
            ->whereDate('availability_date', today())
            ->first();

        // Apply filter logic
        $query = Booking::where('user_id', $user->id)
            ->with('eventType')
            ->where('status', 'scheduled');


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
                // $query = $query->whereDate('booking_date', today())->whereTime('start_time', '>=', now()->format('H:i'));
                $query = $todaysAvailability->bookings()
                ->where('start_time', '>=', now()->format('H:i'));
                break;
        }

        // Calendar data
        $currentMonth = $request->get('month') ?
            \Carbon\Carbon::parse($request->get('month')) : now();
        $calendarData = $this->generateCalendarData($user, $currentMonth);

        // Get booking statistics
        $upcomingMeetings = $query->with('user.availability')->get();
        // dd($upcomingMeetings->toArray());


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
        $todaysBookings = $query->whereDate('booking_date', today())
            ->whereTime('start_time', '>=', now()->format('H:i'))
            ->orderBy('start_time')
            ->get();

        $availableSlots = $todaysAvailability->getTimeSlots($duration);

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
            'todaysAvailability',
            'upcomingBookings',
            'availableSlots',
            'duration',
            'calendarData',
        ));
    }

    // Calendar data generation function
    private function generateCalendarData($user, $currentMonth = null)
    {
        $currentMonth = $currentMonth ?: now();
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        // Get bookings for the month
        $monthlyBookings = Booking::where('user_id', $user->id)
            ->whereBetween('booking_date', [
                $startOfMonth->toDateString(),
                $endOfMonth->toDateString()
            ])
            ->where('status', 'scheduled')
            ->with('eventType')
            ->get()
            ->groupBy(function ($booking) {
                return $booking->booking_date->format('Y-m-d');
            });

        // Get availabilities for the month
        $monthlyAvailabilities = Availability::where('user_id', $user->id)
            ->whereBetween('availability_date', [
                $startOfMonth->toDateString(),
                $endOfMonth->toDateString()
            ])
            ->where('is_available', true)
            ->get()
            ->groupBy(function ($availability) {
                return $availability->availability_date->format('Y-m-d');
            });

        // Generate calendar grid
        $calendarDays = [];
        $startOfCalendar = $startOfMonth->copy()->startOfWeek();
        $endOfCalendar = $endOfMonth->copy()->endOfWeek();


        for ($date = $startOfCalendar->copy(); $date->lte($endOfCalendar); $date->addDay()) {
            $dateKey = $date->format('Y-m-d');
            $bookings = $monthlyBookings->get($dateKey, collect());
            $availabilities = $monthlyAvailabilities->get($dateKey, collect());

            $todaysAvailabilities = Availability::where('user_id', $user->id)
                ->whereDate('availability_date', $date->toDateString())
                ->where('is_available', true)
                ->get();

            $todays_booking_count = 0;
            if ($todaysAvailabilities->isNotEmpty()) {
                $todaysAvailabilities->each(function ($availability) use (&$todays_booking_count) {
                    $todays_booking_count += $availability->getBookingCountAttribute();
                });
            }

            $calendarDays[] = [
                'date' => $date->copy(),
                'day' => $date->day,
                'is_current_month' => $date->month === $currentMonth->month,
                'is_today' => $date->isToday(),
                'bookings_count' => $todays_booking_count,
                'has_availability' => $availabilities->isNotEmpty(),
                'bookings' => $bookings->take(3), // Limit for display
            ];
        }

        return [
            'calendar_days' => $calendarDays,
            'current_month' => $currentMonth,
            'prev_month' => $currentMonth->copy()->subMonth(),
            'next_month' => $currentMonth->copy()->addMonth(),
        ];
    }
}
