<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Booking;
use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $stats = [
            'total_bookings' => Booking::where('user_id', $user->id)->count(),
            'upcoming_bookings' => Booking::where('user_id', $user->id)
                ->where('start_time', '>', now())
                ->where('status', 'scheduled')
                ->count(),
            'total_event_types' => EventType::where('user_id', $user->id)->count(),
            'active_event_types' => EventType::where('user_id', $user->id)
                ->where('is_active', true)
                ->count(),
        ];

        return $this->successResponse($stats, 'Dashboard data retrieved successfully');
    }

    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();
        $period = $request->input('period', '30'); // days

        $bookings = Booking::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays($period))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $this->successResponse($bookings, 'Statistics retrieved successfully');
    }

    public function recentBookings(Request $request): JsonResponse
    {
        $bookings = Booking::with('eventType')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->limit(5)
            ->get();

        return $this->successResponse($bookings, 'Recent bookings retrieved successfully');
    }
}
