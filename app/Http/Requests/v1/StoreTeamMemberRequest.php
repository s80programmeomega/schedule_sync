<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class StoreTeamMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manageMembers', $this->route('team'));
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'role' => 'required|in:admin,member,viewer',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $team = $this->route('team');

            if (!$team->canAddMembers()) {
                $validator->errors()->add('email', 'Team has reached maximum member limit.');
            }

            if ($this->email) {
                $user = User::where('email', $this->email)->first();
                if ($user && $team->hasMember($user)) {
                    $validator->errors()->add('email', 'User is already a team member.');
                }
            }
        });
    }
}
