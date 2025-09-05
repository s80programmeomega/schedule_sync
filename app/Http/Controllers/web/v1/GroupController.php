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
}
