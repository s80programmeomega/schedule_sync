<?php

namespace App\Http\Controllers\web\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\StoreTeamMemberRequest;
use App\Http\Requests\v1\UpdateTeamMemberRequest;
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

        return view('teams.members.index', compact('team', 'members'));
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

        return back()->with('success', 'Invitation sent successfully!');
    }

    public function update(UpdateTeamMemberRequest $request, Team $team, TeamMember $member)
    {
        $member->update($request->validated());
        return back()->with('success', 'Member role updated successfully!');
    }

    public function destroy(Team $team, TeamMember $member)
    {
        $this->authorize('manageMembers', $team);

        if ($member->role === 'owner') {
            return back()->withErrors(['error' => 'Cannot remove team owner.']);
        }

        $member->delete();
        return back()->with('success', 'Member removed successfully!');
    }

    public function acceptInvitation(Request $request, $token)
    {
        $member = TeamMember::where('invitation_token', $token)->first();

        if (!$member || !$member->isInvitationValid()) {
            return redirect()->route('dashboard.index')
                ->withErrors(['error' => 'Invalid or expired invitation.']);
        }

        if ($member->acceptInvitation()) {
            return redirect()->route('teams.show', $member->team)
                ->with('success', 'Welcome to the team!');
        }

        return redirect()->route('dashboard.index')
            ->withErrors(['error' => 'Failed to accept invitation.']);
    }
}
