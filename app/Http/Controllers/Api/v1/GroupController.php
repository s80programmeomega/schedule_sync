<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\StoreGroupRequest;
use App\Http\Requests\v1\UpdateGroupRequest;
use App\Http\Resources\v1\GroupResource;
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

        $groups = $query->where('is_active', true)->paginate(12);

        return GroupResource::collection($groups);
    }

    public function store(StoreGroupRequest $request)
    {
        $group = Group::create(array_merge($request->validated(), [
            'created_by' => auth()->id(),
        ]));

        return new GroupResource($group->load(['team', 'createdBy']));
    }

    public function show(Group $group)
    {
        $this->authorize('view', $group);
        return new GroupResource($group->load(['team', 'createdBy', 'teamMembers.user', 'contacts']));
    }

    public function update(UpdateGroupRequest $request, Group $group)
    {
        $group->update($request->validated());
        return new GroupResource($group->load(['team', 'createdBy']));
    }

    public function destroy(Group $group)
    {
        $this->authorize('delete', $group);
        $group->update(['is_active' => false]);

        return response()->json(['message' => 'Group archived successfully']);
    }
}
