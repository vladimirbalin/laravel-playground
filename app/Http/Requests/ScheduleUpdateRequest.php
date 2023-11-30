<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'schedule' => 'array:mon,fri,sun',
            'schedule.*.start' => 'required|string',
            'schedule.*.end' => 'required|string',
            'schedule.mon' => 'required|array',
            'schedule.fri' => 'required|array',
            'schedule.sun' => 'required|array',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
