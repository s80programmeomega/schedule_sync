<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\StoreTeamMemberRequest;
use App\Http\Requests\v1\UpdateTeamMemberRequest;
use App\Http\Resources\v1\TeamMemberResource;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TeamInvitation;

class TeamMemberController extends Controller
{
    public function index(Team $team)
    {
        $this->authorize('viewMembers', $team);
        $members = $team->members()->with(['user', 'invitedBy'])->get();

        return TeamMemberResource::collection($members);
    }

    public function store(StoreTeamMemberRequest $request, Team $team)
    {
        $user = User::where('email', $request->email)->first();

        $teamMember = $team->members()->create([
            'user_id' => $user->id,
            'role' => $request->role,
            'status' => 'pending',
            'invited_by' => auth()->id(),
        ]);

        Mail::to($user)->send(new TeamInvitation($teamMember));

        return new TeamMemberResource($teamMember->load(['user', 'team', 'invitedBy']));
    }

    public function show(Team $team, TeamMember $member)
    {
        $this->authorize('viewMembers', $team);
        return new TeamMemberResource($member->load(['user', 'team', 'invitedBy']));
    }

    public function update(UpdateTeamMemberRequest $request, Team $team, TeamMember $member)
    {
        $member->update($request->validated());
        return new TeamMemberResource($member->load(['user', 'team', 'invitedBy']));
    }

    public function destroy(Team $team, TeamMember $member)
    {
        $this->authorize('manageMembers', $team);

        if ($member->role === 'owner') {
            return response()->json(['error' => 'Cannot remove team owner.'], 422);
        }

        $member->delete();
        return response()->json(['message' => 'Member removed successfully']);
    }
}