<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

class StoreGroupMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('group'));
    }

    public function rules(): array
    {
        return [
            'member_type' => 'required|in:team_member,contact',
            'member_id' => 'required|integer',
            'role' => 'nullable|string|max:50',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $group = $this->route('group');
            $memberType = $this->member_type === 'team_member'
                ? 'App\Models\TeamMember'
                : 'App\Models\Contact';

            // Check if member already exists in group
            $exists = $group->members()
                ->where('member_type', $memberType)
                ->where('member_id', $this->member_id)
                ->exists();

            if ($exists) {
                $validator->errors()->add('member_id', 'This member is already in the group.');
            }
        });
    }
}
