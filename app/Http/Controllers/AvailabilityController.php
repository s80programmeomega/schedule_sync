<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function index()
    {
        $availabilities = Availability::where('user_id', auth()->user()->id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('availabilities.index', compact('availabilities'));
    }

    public function create()
    {
        return view('availabilities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_available' => 'boolean',
        ]);

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

        return view('availabilities.edit', compact('availability'));
    }

    public function update(Request $request, Availability $availability)
    {
        if ($availability->user_id !== auth()->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_available' => 'boolean',
        ]);

        $availability->update($validated);

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
}
