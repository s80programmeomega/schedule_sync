<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Team;

class StoreContactRequest extends FormRequest
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
            'email' => 'required|email|unique:contacts,email',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'team_id' => 'nullable|exists:teams,id',
            'timezone' => 'nullable|string|max:50',
        ];
    }
}