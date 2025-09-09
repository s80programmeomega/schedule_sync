<?php

namespace App\Http\Controllers\web\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\StoreTeamRequest;
use App\Http\Requests\v1\UpdateTeamRequest;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Timezone;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Auth::user()->teams()->get();
        // $teams = Auth::user()->teams()->with(['owner', 'activeMembers.user'])->get();
        return view('teams.index', compact('teams'));
    }

    public function show(Team $team)
    {
        $this->authorize('view', $team);
        $team->load(['activeMembers.user', 'eventTypes', 'contacts', 'groups']);
        $stats = $team->getStats();

        return view('teams.show', compact('team', 'stats'));
    }

    public function create()
    {
        $timezones = Timezone::orderBy('display_name')->get();
        return view('teams.create', compact('timezones'));
    }

    public function store(StoreTeamRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('team-logos', 'public');
        }

        $team = Team::create(array_merge($data, [
            'owner_id' => Auth::id(),
        ]));

        if (!Auth::user()->default_team_id) {
            Auth::user()->update(['default_team_id' => $team->id]);
        }

        return redirect()->route('teams.show', $team)
            ->with('success', 'Team created successfully!');
    }

    public function edit(Team $team)
    {
        $this->authorize('update', $team);
        return view('teams.edit', compact('team'));
    }

    public function update(UpdateTeamRequest $request, Team $team)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            if ($team->logo) {
                Storage::disk('public')->delete($team->logo);
            }
            $data['logo'] = $request->file('logo')->store('team-logos', 'public');
        }

        $team->update($data);

        return redirect()->route('teams.show', $team)
            ->with('success', 'Team updated successfully!');
    }

    public function destroy(Team $team)
    {
        $this->authorize('delete', $team);
        $team->delete();

        return redirect()->route('teams.index')
            ->with('success', 'Team deleted successfully!');
    }

    // public function getMembers(Team $team)
    // {
    //     $members = $team->activeMembers()->with('user')->get();
    //     return response()->json(['members' => $members]);
    // }


    /**
     * Get team members for attendee import
     *
     * This method fetches all active team members with their user information
     * for use in the booking attendee import functionality.
     *
     * @param Team $team The team to fetch members from
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMembers(Team $team)
    {
        // Verify user has access to this team
        if (!$team->hasMember(auth()->user())) {
            abort(403, 'You do not have access to this team');
        }

        // Fetch active team members with user data
        $members = $team->activeMembers()
            ->with(['user:id,name,email'])
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'user_id' => $member->user_id,
                    'role' => $member->role,
                    'user' => [
                        'id' => $member->user->id,
                        'name' => $member->user->name,
                        'email' => $member->user->email,
                    ]
                ];
            });

        return response()->json([
            'success' => true,
            'members' => $members,
            'team' => [
                'id' => $team->id,
                'name' => $team->name,
            ]
        ]);
    }
}
