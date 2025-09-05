<?php

namespace App\Http\Controllers\web\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\StoreContactRequest;
use App\Http\Requests\v1\UpdateContactRequest;
use App\Models\Contact;
use App\Models\Team;
use Illuminate\Http\Request;

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

        $contacts = $query->where('is_active', true)
            ->orderBy('name')
            ->paginate(20);

        $teams = auth()->user()->teams;

        return view('contacts.index', compact('contacts', 'teams', 'teamId'));
    }

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
}
