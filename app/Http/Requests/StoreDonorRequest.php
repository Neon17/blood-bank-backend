<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreDonorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'contact_number' => 'required|numeric|digits_between:7,15',
            'blood_type' => 'required|string|in:A+,A-,O+,O-,B+,B-,AB+,AB-',
            'address' => 'required|string|max:250',
            'date_of_birth' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if (Carbon::parse($value)->age < 18)
                        $fail("You must be at least 18 years old to register and verify as donor");
                }
            ],
            'weight' => 'required|numeric|min:45',
            'height' => 'required|numeric|max:600',
            'last_donated_date' => 'required|date',
            'medical_conditions' => 'nullable|string',
            'current_medication' => 'nullable|string',
            'current_health_status' => 'required|string',

            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ];
    }
}
