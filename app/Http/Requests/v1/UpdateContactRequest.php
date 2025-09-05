<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('contact'));
    }

    public function rules(): array
    {
        $contactId = $this->route('contact')->id;

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts,email,' . $contactId,
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'team_id' => 'nullable|exists:teams,id',
            'timezone' => 'nullable|string|max:50',
        ];
    }
}
