<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\StoreGroupMemberRequest;
use App\Http\Requests\v1\UpdateGroupMemberRequest;
use App\Http\Resources\v1\GroupMemberResource;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\TeamMember;
use App\Models\Contact;

class GroupMemberController extends Controller
{
    public function index(Group $group)
    {
        $this->authorize('view', $group);
        $members = $group->members()->with(['member', 'addedBy'])->get();
        return GroupMemberResource::collection($members);
    }

    public function store(StoreGroupMemberRequest $request, Group $group)
    {
        $memberType = $request->member_type === 'team_member'
            ? TeamMember::class
            : Contact::class;

        $member = $memberType::findOrFail($request->member_id);

        $groupMember = GroupMember::create([
            'group_id' => $group->id,
            'member_id' => $member->id,
            'member_type' => $memberType,
            'role' => $request->role,
            'joined_at' => now(),
            'added_by' => auth()->id(),
        ]);

        return new GroupMemberResource($groupMember->load(['group', 'member', 'addedBy']));
    }

    public function show(Group $group, GroupMember $groupMember)
    {
        $this->authorize('view', $group);
        return new GroupMemberResource($groupMember->load(['group', 'member', 'addedBy']));
    }

    public function update(UpdateGroupMemberRequest $request, Group $group, GroupMember $groupMember)
    {
        $groupMember->update($request->validated());
        return new GroupMemberResource($groupMember->load(['group', 'member', 'addedBy']));
    }

    public function destroy(Group $group, GroupMember $groupMember)
    {
        $this->authorize('update', $group);
        $groupMember->delete();
        return response()->json(['message' => 'Member removed from group successfully']);
    }
}
