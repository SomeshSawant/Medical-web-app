<?php

namespace App\Http\Requests;

use App\Models\MedicalCase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', MedicalCase::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'case_number' => [
                'required',
                'string',
                'max:50',
                'unique:cases,case_number',
            ],
            'patient_reference' => [
                'required',
                'string',
                'max:100',
            ],
            'surgeon_name' => [
                'required',
                'string',
                'max:100',
            ],
            'implant_type' => [
                'required',
                'string',
                'max:100',
            ],
            'status' => [
                'required',
                'string',
                Rule::in(MedicalCase::STATUSES),
            ],
            'priority' => [
                'required',
                'string',
                Rule::in(MedicalCase::PRIORITIES),
            ],
            'surgery_date' => [
                'nullable',
                'date',
            ],
        ];
    }

    /**
     * Custom error messages for attributes.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'case_number.required' => 'Case Number is required.',
            'case_number.unique' => 'This Case Number is already in use.',
            'patient_reference.required' => 'Patient Reference dummy code is required.',
            'surgeon_name.required' => 'Surgeon Name is required.',
            'implant_type.required' => 'Implant Type is required.',
            'status.required' => 'Case status is required.',
            'status.in' => 'Please select a valid status from the allowed list.',
            'priority.required' => 'Priority level is required.',
            'priority.in' => 'Please select a valid priority (Low, Medium, High).',
            'surgery_date.date' => 'Surgery Date must be a valid calendar date.',
        ];
    }
}
