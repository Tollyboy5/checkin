<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'staff_id' => ['required', 'integer', 'exists:staff,id'],
            'work_date' => ['required', 'date'],
            'checked_in_at' => ['nullable', 'date_format:H:i'],
            'checked_out_at' => ['nullable', 'date_format:H:i'],
            'check_in_ip' => ['nullable', 'string', 'max:45'],
            'check_out_ip' => ['nullable', 'string', 'max:45'],
        ];
    }
}
