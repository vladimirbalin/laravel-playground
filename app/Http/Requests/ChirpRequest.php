<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChirpRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'message' => 'required|string|max:255',
        ];
    }
}
