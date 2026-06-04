<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $staff = $this->route('staff');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('staff', 'name')->ignore($staff)],
            'shift_id' => ['nullable', 'integer', 'exists:shifts,id'],
            'pin' => [$staff ? 'nullable' : 'required', 'string', 'min:4', 'max:20'],
            'active' => ['required', 'boolean'],
        ];
    }
}
