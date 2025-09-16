<?php

namespace App\Http\Controllers\web\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\StoreContactRequest;
use App\Http\Requests\v1\UpdateContactRequest;
use App\Models\Contact;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Models\Timezone;

class ContactController extends Controller
{

    public function index(Request $request)
    {
        $teamId = $request->get('team_id');
        $query = Contact::with(['team', 'createdBy']);

        if ($teamId) {
            $team = Team::findOrFail($teamId);
            $this->authorize('manageContacts', $team);
            $query->where('team_id', $teamId);
        } else {
            $query->where('created_by', auth()->id());
        }

        // Enhanced filters
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('company', 'like', '%' . $request->search . '%')
                    ->orWhere('job_title', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('company')) {
            $query->where('company', 'like', '%' . $request->company . '%');
        }

        if ($request->filled('job_title')) {
            $query->where('job_title', 'like', '%' . $request->job_title . '%');
        }

        if ($request->filled('timezone')) {
            $query->where('timezone', $request->timezone);
        }

        if ($request->filled('email_notifications')) {
            $query->where('email_notifications', $request->email_notifications === 'yes');
        }

        if ($request->filled('total_bookings_min')) {
            $query->where('total_bookings', '>=', $request->total_bookings_min);
        }

        if ($request->filled('last_contacted')) {
            $days = $request->last_contacted;
            $query->where('last_contacted_at', '>=', now()->subDays($days));
        }

        $contacts = $query->where('is_active', true)
            ->orderBy('name')
            ->paginate(8)
            ->appends(request()->query());

        $teams = auth()->user()->teams;
        $companies = Contact::whereNotNull('company')->distinct()->pluck('company', 'company');
        $jobTitles = Contact::whereNotNull('job_title')->distinct()->pluck('job_title', 'job_title');
        $timezones = Timezone::orderBy('display_name')->get();


        return view('contacts.index', compact('contacts', 'teams', 'teamId', 'companies', 'jobTitles', 'timezones'));
    }



    // public function index(Request $request)
    // {
    //     $teamId = $request->get('team_id');
    //     $query = Contact::with(['team', 'createdBy']);

    //     if ($teamId) {
    //         $team = Team::findOrFail($teamId);
    //         $this->authorize('manageContacts', $team);
    //         $query->where('team_id', $teamId);
    //     } else {
    //         $query->where('created_by', auth()->id());
    //     }

    //     // Apply additional filters
    //     if ($request->filled('search')) {
    //         $query->where(function ($q) use ($request) {
    //             $q->where('name', 'like', '%' . $request->search . '%')
    //                 ->orWhere('email', 'like', '%' . $request->search . '%')
    //                 ->orWhere('company', 'like', '%' . $request->search . '%');
    //         });
    //     }

    //     if ($request->filled('company')) {
    //         $query->where('company', 'like', '%' . $request->company . '%');
    //     }

    //     $contacts = $query->where('is_active', true)
    //         ->orderBy('name')
    //         ->paginate(20)
    //         ->appends(request()->query());

    //     $teams = auth()->user()->teams;
    //     $companies = Contact::where('created_by', auth()->id())
    //         ->whereNotNull('company')
    //         ->distinct()
    //         ->pluck('company', 'company');

    //     return view('contacts.index', compact('contacts', 'teams', 'teamId', 'companies'));
    // }


    // public function index(Request $request)
    // {
    //     $teamId = $request->get('team_id');
    //     $query = Contact::with(['team', 'createdBy']);

    //     if ($teamId) {
    //         $team = Team::findOrFail($teamId);
    //         $this->authorize('manageContacts', $team);
    //         $query->where('team_id', $teamId);
    //     } else {
    //         $query->where('created_by', auth()->id());
    //     }

    //     $contacts = $query->where('is_active', true)
    //         ->orderBy('name')
    //         ->paginate(20);

    //     $teams = auth()->user()->teams;

    //     return view('contacts.index', compact('contacts', 'teams', 'teamId'));
    // }

    public function create(Request $request)
    {
        $teams = auth()->user()->teams;
        $selectedTeam = $request->get('team_id');

        return view('contacts.create', compact('teams', 'selectedTeam'));
    }

    public function store(StoreContactRequest $request)
    {
        $contact = Contact::create(array_merge($request->validated(), [
            'created_by' => auth()->id(),
        ]));

        return redirect()->route('contacts.index', ['team_id' => $contact->team_id])
            ->with('success', 'Contact created successfully!');
    }

    public function show(Contact $contact)
    {
        $this->authorize('view', $contact);
        $contact->load(['team', 'createdBy', 'bookingAttendances.booking']);

        return view('contacts.show', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        $this->authorize('update', $contact);
        $teams = auth()->user()->teams;

        return view('contacts.edit', compact('contact', 'teams'));
    }

    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $contact->update($request->validated());

        return redirect()->route('contacts.show', $contact)
            ->with('success', 'Contact updated successfully!');
    }

    public function destroy(Contact $contact)
    {
        $this->authorize('delete', $contact);
        $contact->update(['is_active' => false]);

        return redirect()->route('contacts.index')
            ->with('success', 'Contact archived successfully!');
    }

    /**
     * Get all contacts for attendee import
     *
     * Fetches all contacts accessible to the current user for use in
     * the booking attendee import functionality.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllContacts()
    {
        // Get contacts accessible to the user (own contacts + team contacts)
        $contacts = Contact::where(function ($query) {
            $query->where('created_by', auth()->id())
                ->orWhereHas('team', function ($teamQuery) {
                    $teamQuery->whereHas('members', function ($memberQuery) {
                        $memberQuery->where('user_id', auth()->id())
                            ->where('status', 'active');
                    });
                });
        })
            ->where('is_active', true)
            ->select(['id', 'name', 'email', 'company', 'job_title'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'contacts' => $contacts,
            'total' => $contacts->count()
        ]);
    }
}
