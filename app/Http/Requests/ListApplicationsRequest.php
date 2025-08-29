<?php

namespace App\Http\Requests;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ListApplicationsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('sort_order')) {
            $this->merge([
                'sort_order' => Str::lower($this->input('sort_order')),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'plan_type' => ['nullable', Rule::in(['nbn', 'opticomm', 'mobile'])],
            'per_page' => ['nullable', 'integer', 'between:1,100'],
            'sort_by' => ['nullable', Rule::in(['created_at'])],
            'sort_order' => ['nullable', Rule::in(['asc', 'desc'])]
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'plan_type.in' => 'The plan type must be one of: ' . implode(', ', ['nbn', 'opticomm', 'mobile']),
        ];
    }
}
