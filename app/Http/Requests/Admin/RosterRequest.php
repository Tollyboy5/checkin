<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RosterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roster = $this->route('roster');

        return [
            'staff_id' => ['required', 'integer', 'exists:staff,id'],
            'shift_id' => ['required', 'integer', 'exists:shifts,id'],
            'roster_date' => [
                'required',
                'date',
                Rule::unique('rosters', 'roster_date')
                    ->where('staff_id', $this->input('staff_id'))
                    ->ignore($roster),
            ],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}
