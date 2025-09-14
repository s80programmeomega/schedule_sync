<?php

namespace App\Http\Controllers\web\v1;

use App\Models\Booking;
use App\Models\EventType;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\StoreBookingRequest;
use App\Http\Requests\v1\UpdateBookingRequest;
use App\Models\Timezone;
use App\Services\AvailabilityService;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\BookingAttendee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function __construct(
        private AvailabilityService $availabilityService
    ) {}

    private function getBookingsByStatus($status = null, $filters = [])
    {
        $query = Booking::with(['eventType', 'user', 'attendees.attendee', 'timezone'])
            ->where('user_id', auth()->id());

        if ($status) {
            $query->where('status', $status);
        }

        // Enhanced filters
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->whereHas('eventType', function ($eq) use ($filters) {
                    $eq->where('name', 'like', '%' . $filters['search'] . '%');
                })->orWhere('meeting_link', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('cancellation_reason', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['event_type_id'])) {
            $query->where('event_type_id', $filters['event_type_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('booking_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('booking_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['timezone_id'])) {
            $query->where('timezone_id', $filters['timezone_id']);
        }


        if (!empty($filters['status']) && !$status) {
            $query->where('status', $filters['status']);
        }

        return $query->latest('start_time')->paginate(10)->appends(request()->query());
    }

    public function index(Request $request)
    {
        $filters = $request->only(['event_type_id', 'date_from', 'date_to', 'search', 'timezone_id', 'has_meeting_link', 'status']);
        $bookings = $this->getBookingsByStatus(null, $filters);

        $eventTypes = EventType::where('user_id', auth()->id())->get();
        $timezones = Timezone::orderBy('display_name')->get();

        return view('bookings.index', compact('bookings', 'eventTypes', 'timezones'))->with('viewType', 'all');
    }


    public function create()
    {
        $eventTypes = EventType::where('user_id', auth()->id())->where('is_active', true)->get();
        $contacts = Contact::where('created_by', auth()->id())->where('is_active', true)->get();
        $teams = Team::whereHas('members', function ($query) {
            $query->where('user_id', auth()->id())->where('status', 'active');
        })->with(['members.user'])->get();
        $groups = Group::where('created_by', auth()->id())->where('is_active', true)->get();
        $timezones = Timezone::orderBy('display_name')->get();

        return view('bookings.create', compact('eventTypes', 'contacts', 'teams', 'groups', 'timezones'));
    }

    public function store(StoreBookingRequest $request)
    {
        $validated = $request->validated();
        $eventType = EventType::findOrFail($validated['event_type_id']);

        if (!$this->availabilityService->isSlotAvailable(
            auth()->id(),
            $validated['booking_date'],
            $validated['start_time'],
            $eventType->duration
        )) {
            return back()->withErrors(['start_time' => 'Selected time slot is not available'])->withInput();
        }

        $bookingData = collect($validated)->except(['full_start_time', 'full_end_time'])->all();
        $bookingData['user_id'] = auth()->id();
        $bookingData['status'] = 'scheduled';

        Booking::create($bookingData);

        return redirect()->route('bookings.index')->with('success', 'Booking created successfully!');
    }

    public function show(Booking $booking)
    {
        $this->authorizeBookingAccess($booking);
        $booking->load(['attendees.attendee', 'eventType', 'timezone']);
        return view('bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $this->authorizeBookingAccess($booking);

        $booking->load(['attendees.attendee']);
        $eventTypes = EventType::where('user_id', auth()->id())->where('is_active', true)->get();
        $timezones = Timezone::orderBy('display_name')->get();
        $contacts = Contact::where('created_by', auth()->id())->where('is_active', true)->get();
        $teams = Team::whereHas('members', function ($query) {
            $query->where('user_id', auth()->id())->where('status', 'active');
        })->with(['members.user'])->get();
        $groups = Group::where('created_by', auth()->id())->where('is_active', true)->get();

        return view('bookings.edit', compact('booking', 'eventTypes', 'timezones', 'contacts', 'teams', 'groups'));
    }

    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        $this->authorizeBookingAccess($booking);

        $validated = $request->validated();
        $eventType = EventType::findOrFail($validated['event_type_id']);

        if (!$this->availabilityService->isSlotAvailable(
            auth()->id(),
            $validated['booking_date'],
            $validated['start_time'],
            $eventType->duration,
            $booking->id,
        )) {
            return back()->withErrors(['start_time' => 'Selected time slot is not available'])->withInput();
        }

        if ($validated['status'] === 'cancelled' && !$booking->cancelled_at) {
            $validated['cancelled_at'] = now();
        }

        $booking->update($validated);

        return redirect()->route('bookings.index')->with('success', 'Booking updated successfully!');
    }

    public function destroy(Request $request, Booking $booking)
    {
        $this->authorizeBookingAccess($booking);

        $booking->delete();
        $redirectRoute = $request->redirect_to === 'scheduled' ? 'bookings.scheduled' : 'bookings.index';

        return redirect()->route($redirectRoute)->with('success', 'Booking deleted successfully!');
    }

    public function cancel(Request $request, Booking $booking)
    {
        $this->authorizeBookingAccess($booking);

        $validated = $request->validate([
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
            'cancelled_at' => now(),
        ]);

        return back()->with('success', 'Booking cancelled successfully!');
    }

    public function addAttendee(Request $request, Booking $booking)
    {
        $this->authorizeBookingAccess($booking);

        try {
            $type = $request->input('type');
            $role = $request->input('role', 'required');

            if (!in_array($type, ['contact', 'email', 'team', 'group'])) {
                return response()->json(['success' => false, 'message' => 'Invalid attendee type'], 422);
            }

            switch ($type) {
                case 'contact':
                    $contactId = $request->input('contact_id');
                    if (!$contactId) {
                        return response()->json(['success' => false, 'message' => 'Contact is required'], 422);
                    }
                    $contact = Contact::findOrFail($contactId);
                    $booking->addAttendee($contact, $role);
                    break;

                case 'team':
                    $memberId = $request->input('member_id');
                    if (!$memberId) {
                        return response()->json(['success' => false, 'message' => 'Team member is required'], 422);
                    }
                    $teamMember = TeamMember::with('user')->find($memberId);
                    if (!$teamMember) {
                        return response()->json(['success' => false, 'message' => 'Team member not found'], 422);
                    }
                    $booking->addAttendee($teamMember->user, $role);

                    break;

                case 'group':
                    $memberId = $request->input('member_id');
                    if (!$memberId) {
                        return response()->json(['success' => false, 'message' => 'Group member is required'], 422);
                    }
                    $groupMember = GroupMember::with('member')->find($memberId);
                    if (!$groupMember) {
                        return response()->json(['success' => false, 'message' => 'Group member not found'], 422);
                    }
                    $booking->addAttendee($groupMember->member, $role);

                    break;

                case 'email':
                    $name = $request->input('name');
                    $email = $request->input('email');
                    if (!$name || !$email) {
                        return response()->json(['success' => false, 'message' => 'Name and email are required'], 422);
                    }

                    // Create or find existing contact
                    $contact = Contact::firstOrCreate(
                        ['email' => $email, 'created_by' => auth()->id()],
                        [
                            'name' => $name,
                            'created_by' => auth()->id(),
                            'is_active' => true,
                        ]
                    );

                    // Add contact as attendee
                    $booking->addAttendee($contact, $role);

                    // $booking->attendees()->create([
                    //     'attendee_id' => null,
                    //     'attendee_type' => 'guest',
                    //     'name' => $name,
                    //     'email' => $email,
                    //     'role' => $role,
                    //     'status' => 'pending',
                    // ]);
                    break;
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Add attendee error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function removeAttendee(Booking $booking, BookingAttendee $attendee)
    {
        $this->authorizeBookingAccess($booking);

        if ($attendee->booking_id !== $booking->id) {
            abort(403);
        }

        $attendee->delete();
        return back()->with('success', 'Attendee removed successfully!');
    }

    public function updateAttendee(Request $request, Booking $booking, BookingAttendee $attendee)
    {
        $this->authorizeBookingAccess($booking);

        if ($attendee->booking_id !== $booking->id) {
            abort(403);
        }

        $validated = $request->validate([
            'role' => 'required|in:organizer,required,optional',
            'status' => 'required|in:pending,accepted,declined',
        ]);

        $attendee->update($validated);
        return response()->json(['success' => true]);
    }

    public function getAttendees(Booking $booking)
    {
        $this->authorizeBookingAccess($booking);

        $attendees = $booking->attendees()->with('attendee')->get();
        return response()->json($attendees);
    }

    public function storeWithAttendees(Request $request)
    {
        $validated = $request->validate([
            'event_type_id' => 'required|exists:event_types,id',
            'booking_date' => 'required|date',
            'start_time' => 'required',
            'timezone' => 'nullable|string|exists:timezones,name',
            'status' => 'required|in:scheduled,pending,confirmed,cancelled,completed',
            'meeting_link' => 'nullable|url|max:500',
            'attendees' => 'required|array|min:1',
            'attendees.*.type' => 'required|in:contact,email,team,group',
            'attendees.*.contact_id' => 'required_if:attendees.*.type,contact|nullable|exists:contacts,id',
            'attendees.*.member_id' => 'required_if:attendees.*.type,team,group|nullable|string',
            'attendees.*.name' => 'required_if:attendees.*.type,email|nullable|string',
            'attendees.*.email' => 'required_if:attendees.*.type,email|nullable|email',
            'attendees.*.role' => 'required|in:organizer,required,optional',
        ]);


        $eventType = EventType::findOrFail($validated['event_type_id']);

        if (!$this->availabilityService->isSlotAvailable(
            auth()->id(),
            $validated['booking_date'],
            $validated['start_time'],
            $eventType->duration
        )) {
            return back()->withErrors(['start_time' => 'Selected time slot is not available'])->withInput();
        }

        $startTime = Carbon::parse($validated['start_time']);
        $endTime = $startTime->copy()->addMinutes($eventType->duration);

        $booking = Booking::create([
            'event_type_id' => $validated['event_type_id'],
            'user_id' => auth()->id(),
            'booking_date' => $validated['booking_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $endTime->format('H:i'),
            'status' => $validated['status'] ?? 'scheduled',
            'meeting_link' => $validated['meeting_link'],
        ]);

        foreach ($validated['attendees'] as $attendeeData) {
            try {
                switch ($attendeeData['type']) {
                    case 'contact':
                        $contact = Contact::findOrFail($attendeeData['contact_id']);
                        $booking->addAttendee($contact, $attendeeData['role']);
                        break;
                    case 'team':
                        $teamMember = TeamMember::with('user')->find($attendeeData['member_id']);
                        if (!$teamMember) {
                            throw new \Exception("Team member not found");
                        }
                        $booking->addAttendee($teamMember->user, $attendeeData['role']);

                        break;
                    case 'group':
                        $groupMember = GroupMember::with('member')->find($attendeeData['member_id']);
                        if (!$groupMember) {
                            throw new \Exception("Group member not found");
                        }
                        $booking->addAttendee($groupMember->member, $attendeeData['role']);

                        break;
                    case 'email':
                        $booking->attendees()->create([
                            'attendee_type' => 'guest',
                            'name' => $attendeeData['name'],
                            'email' => $attendeeData['email'],
                            'role' => $attendeeData['role'],
                            'status' => 'pending',
                        ]);
                        break;
                }
            } catch (\Exception $e) {
                return back()->withErrors(['attendees' => 'Error adding attendee: ' . $e->getMessage()])->withInput();
            }
        }

        return redirect()->route('bookings.index')->with('success', 'Booking created with attendees successfully!');
    }

    private function authorizeBookingAccess(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
