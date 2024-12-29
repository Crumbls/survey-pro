<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// app/Http/Requests/StoreSurveyRequest.php
class StoreSurveyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Add proper authorization logic
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'tenant_id' => [
                Rule::requiredIf(fn() => auth()->user()->tenants->count() > 1),
                'exists:tenants,id'
            ],
        ];
    }
}
