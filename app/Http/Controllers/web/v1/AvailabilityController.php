<?php

namespace App\Http\Controllers\web\v1;

use App\Models\Availability;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\StoreAvailabilityRequest;
use App\Http\Requests\v1\UpdateAvailabilityRequest;
use App\Models\Booking;
use App\Models\Timezone;

class AvailabilityController extends Controller
{

    public function __construct(
    ) {}

    public function index(Request $request)
    {
        $query = Availability::with(['timezone'])
        ->withCount(['bookings'])
        ->where('user_id', auth()->user()->id);

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('start_time', 'like', '%' . $request->search . '%')
                    ->orWhere('end_time', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('availability_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('availability_date', '<=', $request->date_to);
        }

        if ($request->filled('timezone_id')) {
            $query->where('timezone_id', $request->timezone_id);
        }

        if ($request->filled('is_available')) {
            $query->where('is_available', $request->is_available === 'yes');
        }

        if ($request->filled('time_range')) {
            switch ($request->time_range) {
                case 'morning':
                    $query->where('start_time', '<', '12:00');
                    break;
                case 'afternoon':
                    $query->where('start_time', '>=', '12:00')->where('start_time', '<', '17:00');
                    break;
                case 'evening':
                    $query->where('start_time', '>=', '17:00');
                    break;
            }
        }

        $availabilities = $query->orderBy('availability_date')
            ->orderBy('start_time')
            ->paginate(10)
            ->appends(request()->query());

        $timezones = Timezone::orderBy('display_name')->get();

        return view('availabilities.index', compact('availabilities', 'timezones'));
    }


    // public function index()
    // {
    //     $availabilities = Availability::where('user_id', auth()->user()->id)
    //         ->orderBy('availability_date')
    //         ->orderBy('start_time')
    //         ->paginate(10);
    //     $timezones = Timezone::orderBy('display_name')->get();

    //     return view('availabilities.index', compact('availabilities', 'timezones'));
    // }

    public function create()
    {
        $timezones = Timezone::orderBy('display_name')->get();
        return view('availabilities.create', compact('timezones'));
    }

    public function store(StoreAvailabilityRequest $request)
    {
        $validated = $request->validated();

        // Automatically set the user_id from the authenticated user
        $validated['user_id'] = auth()->user()->id;

        Availability::create($validated);

        return redirect()->route('availability.index')
            ->with('success', 'Availability added successfully!');
    }

    public function show(Availability $availability, Request $request)
    {
        if ($availability->user_id !== auth()->user()->id) {
            abort(403);
        }
        if (!$availability->is_available){
            return $this->index($request)->with(key: "Warning", value:"The selected availability is not available.");
        }

        $duration = (int)($request->get('duration', 30)); // Get duration from request
        $availability->load(['timezone', 'bookings.eventType']);
        $timeSlots = $availability->getTimeSlots($duration);

        return view('availabilities.show', compact('availability', 'timeSlots', "duration"));

    }

    public function edit(Availability $availability)
    {
        if ($availability->user_id !== auth()->user()->id) {
            abort(403);
        }

        $timezones = Timezone::orderBy('display_name')->get();

        return view('availabilities.edit', compact('availability', 'timezones'));
    }

    public function update(UpdateAvailabilityRequest $request, Availability $availability)
    {
        if ($availability->user_id !== auth()->user()->id) {
            abort(403);
        }

        $availability->update($request->validated());

        return redirect()->route('availability.index')
            ->with('success', 'Availability updated successfully!');
    }

    public function destroy(Availability $availability)
    {
        if ($availability->user_id !== auth()->user()->id) {
            abort(403);
        }

        $availability->delete();

        return redirect()->route('availability.index')
            ->with('success', 'Availability deleted successfully!');
    }

    public function slots(Availability $availability, Request $request)
    {
        if ($availability->user_id !== auth()->user()->id) {
            abort(403);
        }

        $duration = $request->get('duration', 30); // Default 30 minutes

        $slots = $availability->getTimeSlots($duration);

        return view('availabilities.slots', compact('availability', 'slots', 'duration'));
    }
}
