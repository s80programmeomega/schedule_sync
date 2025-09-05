<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\StoreContactRequest;
use App\Http\Requests\v1\UpdateContactRequest;
use App\Http\Resources\v1\ContactResource;
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

        return ContactResource::collection($contacts);
    }

    public function store(StoreContactRequest $request)
    {
        $contact = Contact::create(array_merge($request->validated(), [
            'created_by' => auth()->id(),
        ]));

        return new ContactResource($contact->load(['team', 'createdBy']));
    }

    public function show(Contact $contact)
    {
        $this->authorize('view', $contact);
        return new ContactResource($contact->load(['team', 'createdBy']));
    }

    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $contact->update($request->validated());
        return new ContactResource($contact->load(['team', 'createdBy']));
    }

    public function destroy(Contact $contact)
    {
        $this->authorize('delete', $contact);
        $contact->update(['is_active' => false]);

        return response()->json(['message' => 'Contact archived successfully']);
    }
}
