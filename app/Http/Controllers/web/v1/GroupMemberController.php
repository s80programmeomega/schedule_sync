<?php

namespace App\Http\Controllers\web\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\StoreGroupMemberRequest;
use App\Http\Requests\v1\UpdateGroupMemberRequest;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\TeamMember;
use App\Models\Contact;

class GroupMemberController extends Controller
{

  // public function index(Group $group)
  // {
  //     $this->authorize('view', $group);

  //     $members = $group->members()->with(['member', 'addedBy'])->get();

  //     $availableTeamMembers = $group->team?->activeMembers()->with('user')->get() ?? collect();

  //     $availableContacts = ($group->team ?? auth()->user())
  //         ->contacts()
  //         ->where('is_active', true)
  //         ->get();

  //     return view('groups.members.index', compact('group', 'members', 'availableTeamMembers', 'availableContacts'));
  // }


  public function index(Group $group)
  {
    $this->authorize('view', $group);

    $members = $group->members()->with(['member', 'addedBy'])->get();
    $availableTeamMembers = $group->team
      ? $group->team->activeMembers()->with('user')->get()
      : collect();
    $availableContacts = $group->team
      ? $group->team->contacts()->where('is_active', true)->get()
      : auth()->user()->contacts()->where('is_active', true)->get();

    return view('groups.members.index', compact('group', 'members', 'availableTeamMembers', 'availableContacts'));
  }

  public function create(Group $group)
  {
    $this->authorize('update', $group);

    $availableTeamMembers = $group->team
      ? $group->team->activeMembers()->with('user')->get()
      : collect();
    $availableContacts = $group->team
      ? $group->team->contacts()->where('is_active', true)->get()
      : auth()->user()->contacts()->where('is_active', true)->get();

    return view('groups.members.create', compact('group', 'availableTeamMembers', 'availableContacts'));
  }

  public function edit(Group $group, GroupMember $groupMember)
  {
    $this->authorize('update', $group);
    return view('groups.members.edit', compact('group', 'groupMember'));
  }


  public function store(StoreGroupMemberRequest $request, Group $group)
  {
    $memberType = $request->member_type === 'team_member'
      ? TeamMember::class
      : Contact::class;

    $member = $memberType::findOrFail($request->member_id);

    GroupMember::create([
      'group_id' => $group->id,
      'member_id' => $member->id,
      'member_type' => $memberType,
      'role' => $request->role,
      'joined_at' => now(),
      'added_by' => auth()->id(),
    ]);

    return back()->with('success', 'Member added to group successfully!');
  }

  public function update(UpdateGroupMemberRequest $request, Group $group, GroupMember $groupMember)
  {
    $groupMember->update($request->validated());
    return back()->with('success', 'Member role updated successfully!');
  }

  public function destroy(Group $group, GroupMember $groupMember)
  {
    $this->authorize('update', $group);
    $groupMember->delete();
    return back()->with('success', 'Member removed from group successfully!');
  }
}
