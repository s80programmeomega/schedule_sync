<?php

namespace App\Http\Controllers\web\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\StoreGroupRequest;
use App\Http\Requests\v1\UpdateGroupRequest;
use App\Models\Group;
use App\Models\Team;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $teamId = $request->get('team_id');
        $query = Group::with(['team', 'createdBy']);

        if ($teamId) {
            $team = Team::findOrFail($teamId);
            $this->authorize('view', $team);
            $query->where('team_id', $teamId);
        } else {
            $query->where('created_by', auth()->id());
        }

        $groups = $query->where('is_active', true)->paginate(7);
        $teams = auth()->user()->teams;

        return view('groups.index', compact('groups', 'teams', 'teamId'));
    }

    public function create(Request $request)
    {
        $teams = auth()->user()->teams;
        $selectedTeam = $request->get('team_id');

        return view('groups.create', compact('teams', 'selectedTeam'));
    }

    public function store(StoreGroupRequest $request)
    {
        $group = Group::create(array_merge($request->validated(), [
            'created_by' => auth()->id(),
        ]));

        return redirect()->route('groups.show', $group)
            ->with('success', 'Group created successfully!');
    }

    public function show(Group $group)
    {
        $this->authorize('view', $group);
        $group->load(['team', 'createdBy', 'teamMembers.user', 'contacts']);
        $members = $group->members;


        return view('groups.show', compact('group', 'members'));
    }

    public function edit(Group $group)
    {
        $this->authorize('update', $group);
        $teams = auth()->user()->teams;

        return view('groups.edit', compact('group', 'teams'));
    }

    public function update(UpdateGroupRequest $request, Group $group)
    {
        $group->update($request->validated());

        return redirect()->route('groups.show', $group)
            ->with('success', 'Group updated successfully!');
    }

    public function destroy(Group $group)
    {
        $this->authorize('delete', $group);
        $group->update(['is_active' => false]);

        return redirect()->route('groups.index')
            ->with('success', 'Group archived successfully!');
    }

    // public function getMembers(Group $group)
    // {
    //     $members = $group->members()->with('member')->get();
    //     return response()->json(['members' => $members]);
    // }


    // /**
    //  * Get team members for attendee import
    //  *
    //  * This method fetches all active team members with their user information
    //  * for use in the booking attendee import functionality.
    //  *
    //  * @param Team $team The team to fetch members from
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function getMembers(Team $team)
    // {
    //     // Verify user has access to this team
    //     if (!$team->hasMember(auth()->user())) {
    //         abort(403, 'You do not have access to this team');
    //     }

    //     // Fetch active team members with user data
    //     $members = $team->activeMembers()
    //         ->with(['user:id,name,email'])
    //         ->get()
    //         ->map(function ($member) {
    //             return [
    //                 'id' => $member->id,
    //                 'user_id' => $member->user_id,
    //                 'role' => $member->role,
    //                 'user' => [
    //                     'id' => $member->user->id,
    //                     'name' => $member->user->name,
    //                     'email' => $member->user->email,
    //                 ]
    //             ];
    //         });

    //     return response()->json([
    //         'success' => true,
    //         'members' => $members,
    //         'team' => [
    //             'id' => $team->id,
    //             'name' => $team->name,
    //         ]
    //     ]);
    // }

    // Add to GroupController
    /**
     * Get group members for attendee import
     *
     * This method fetches all group members (both team members and contacts)
     * for use in the booking attendee import functionality.
     *
     * @param Group $group The group to fetch members from
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMembers(Group $group)
    {
        // Verify user has access to this group
        if (
            $group->created_by !== auth()->id() &&
            !$group->team?->hasMember(auth()->user())
        ) {
            abort(403, 'You do not have access to this group');
        }

        // Fetch all group members with their related data
        $groupMembers = $group->members()
            ->with(['member'])
            ->get()
            ->map(function ($groupMember) {
                $member = $groupMember->member;

                // Handle different member types (TeamMember or Contact)
                if ($member instanceof \App\Models\TeamMember) {
                    return [
                        'id' => $groupMember->id,
                        'member_id' => $member->id,
                        'member_type' => 'team_member',
                        'name' => $member->user->name,
                        'email' => $member->user->email,
                        'role' => $member->role,
                    ];
                } elseif ($member instanceof \App\Models\Contact) {
                    return [
                        'id' => $groupMember->id,
                        'member_id' => $member->id,
                        'member_type' => 'contact',
                        'name' => $member->name,
                        'email' => $member->email,
                    ];
                }

                return null;
            })
            ->filter() // Remove null values
            ->values(); // Re-index array and convert to plain array

        return response()->json([
            'success' => true,
            'members' => $groupMembers->toArray(), // Convert to array
            'group' => [
                'id' => $group->id,
                'name' => $group->name,
            ]
        ]);
    }
}
