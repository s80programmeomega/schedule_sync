<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\StoreTeamRequest;
use App\Http\Requests\v1\UpdateTeamRequest;
use App\Http\Resources\v1\TeamResource;
use App\Models\Team;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    public function index()
    {
        $teams = auth()->user()->teams()->with(['owner', 'activeMembers'])->get();
        return TeamResource::collection($teams);
    }

    public function store(StoreTeamRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('team-logos', 'public');
        }

        $team = Team::create(array_merge($data, [
            'owner_id' => auth()->id(),
        ]));

        return new TeamResource($team->load(['owner', 'activeMembers']));
    }

    public function show(Team $team)
    {
        $this->authorize('view', $team);
        return new TeamResource($team->load(['owner', 'activeMembers']));
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
        return new TeamResource($team->load(['owner', 'activeMembers']));
    }

    public function destroy(Team $team)
    {
        $this->authorize('delete', $team);
        $team->delete();

        return response()->json(['message' => 'Team deleted successfully']);
    }
}
