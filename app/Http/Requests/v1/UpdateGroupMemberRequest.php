<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('groupMember')->group);
    }

    public function rules(): array
    {
        return [
            'role' => 'nullable|string|max:50',
        ];
    }
}
