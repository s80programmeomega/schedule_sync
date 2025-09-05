<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manageMembers', $this->route('team'));
    }

    public function rules(): array
    {
        return [
            'role' => 'required|in:admin,member,viewer',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $member = $this->route('member');

            if ($member->role === 'owner') {
                $validator->errors()->add('role', 'Cannot change owner role.');
            }
        });
    }
}
