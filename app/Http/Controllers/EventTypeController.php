<?php

namespace App\Http\Controllers;

use App\Models\EventType;
use App\Http\Requests\StoreEventTypeRequest;
use App\Http\Requests\UpdateEventTypeRequest;
use Illuminate\Http\Request;

/**
 * Event Type Controller
 * Handles CRUD operations for event types
 */
class EventTypeController extends Controller
{
    /**
     * Display a listing of event types
     */
    public function index()
    {
        $eventTypes = EventType::where('user_id', auth()->id())
            ->withCount('bookings')
            ->latest()
            ->get();

        return view('event-types.index', compact('eventTypes'));
    }

    /**
     * Show the form for creating a new event type
     */
    public function create()
    {
        return view('event-types.create');
    }

    /**
     * Store a newly created event type
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'duration' => 'required|integer|min:5|max:480',
            'location_type' => 'required|in:zoom,google_meet,phone,custom',
            'location_details' => 'nullable|string|max:255',
            'buffer_time_before' => 'nullable|integer|min:0|max:60',
            'buffer_time_after' => 'nullable|integer|min:0|max:60',
            'requires_confirmation' => 'boolean',
            'max_events_per_day' => 'nullable|integer|min:1|max:20',
            'color' => 'nullable|string|max:7',
        ]);

        $validated['user_id'] = auth()->user()->id();

        EventType::create($validated);

        return redirect()->route('event-types.index')
            ->with('success', 'Event type created successfully!');
    }

    /**
     * Show the form for editing an event type
     */
    public function edit(EventType $eventType)
    {
        // Ensure user can only edit their own event types
        if ($eventType->user_id !== auth()->user()->id()) {
            abort(403);
        }

        return view('event-types.edit', compact('eventType'));
    }

    /**
     * Update the specified event type
     */
    public function update(Request $request, EventType $eventType)
    {
        // Ensure user can only edit their own event types
        if ($eventType->user_id !== auth()->user()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'duration' => 'required|integer|min:5|max:480',
            'location_type' => 'required|in:zoom,google_meet,phone,custom',
            'location_details' => 'nullable|string|max:255',
            'buffer_time_before' => 'nullable|integer|min:0|max:60',
            'buffer_time_after' => 'nullable|integer|min:0|max:60',
            'requires_confirmation' => 'boolean',
            'max_events_per_day' => 'nullable|integer|min:1|max:20',
            'color' => 'nullable|string|max:7',
        ]);

        $eventType->update($validated);

        return redirect()->route('event-types.index')
            ->with('success', 'Event type updated successfully!');
    }

    /**
     * Remove the specified event type
     */
    public function destroy(EventType $eventType)
    {
        // Ensure user can only delete their own event types
        if ($eventType->user_id !== auth()->user()->id()) {
            abort(403);
        }

        $eventType->delete();

        return redirect()->route('event-types.index')
            ->with('success', 'Event type deleted successfully!');
    }

    /**
     * Toggle active status of event type
     */
    public function toggle(EventType $eventType)
    {
        if ($eventType->user_id !== auth()->user()->id()) {
            abort(403);
        }

        $eventType->update(['is_active' => !$eventType->is_active]);

        return back()->with('success', 'Event type status updated!');
    }
}
