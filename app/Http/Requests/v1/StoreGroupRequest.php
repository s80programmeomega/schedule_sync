<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Team;

class StoreGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->team_id) {
            $team = Team::find($this->team_id);
            return $team && $team->userCan(auth()->user(), 'manage_contacts');
        }
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'type' => 'required|in:team_members,contacts,mixed',
            'team_id' => 'nullable|exists:teams,id',
        ];
    }

    public function messages(): array
    {
        return [
            'color.regex' => 'Color must be a valid hex color code (e.g., #FF5733).',
        ];
    }
}
