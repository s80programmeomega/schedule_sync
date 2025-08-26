<?php

namespace App\Http\Controllers\web\v1;

use App\Models\Availability;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\StoreAvailabilityRequest;
use App\Http\Requests\v1\UpdateAvailabilityRequest;
use App\Models\Timezone;
use App\Services\AvailabilityService;

class AvailabilityController extends Controller
{

    public function __construct(
        private AvailabilityService $availabilityService
    ) {}

    public function index()
    {
        $availabilities = Availability::where('user_id', auth()->user()->id)
            ->orderBy('availability_date')
            ->orderBy('start_time')
            ->paginate(10);
        $timezones = Timezone::orderBy('display_name')->get();

        return view('availabilities.index', compact('availabilities', 'timezones'));
    }

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

    public function show(Availability $availability)
    {
        if ($availability->user_id !== auth()->user()->id) {
            abort(403);
        }

        return view('availabilities.show', compact('availability'));
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


        $slots = $this->availabilityService->getAvailableSlots(
            auth()->user()->id,
            $availability->availability_date->toDateString(),
            $duration
        );

        return view('availabilities.slots', compact('availability', 'slots', 'duration'));
    }
}
