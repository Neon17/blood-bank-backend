<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDonorRequest extends FormRequest
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
            'contact_number' => 'sometimes|numeric|digits_between:7,15',
            'blood_group' => 'sometimes|string|in:A+,A-,O+,O-,B+,B-,AB+,AB-',
            'address' => 'sometimes|string|max:250',
            'date_of_birth' => [
                'sometimes',
                'date',
                function ($attribute, $value, $fail) {
                    if (Carbon::parse($value)->age < 18) {
                        $fail('You must be at least 18 years old to register as a donor.');
                    }
                }
            ],
            'weight' => 'sometimes|numeric|min:45',
            'height' => 'sometimes|numeric|max:600',
            'last_donated_date' => 'sometimes|date',
            'medical_conditions' => 'nullable|string',
            'current_medication' => 'nullable|string',
            'current_health_status' => 'sometimes|string',
            'verification_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            'verification_status' => 'sometimes|string|in:pending,approved,rejected',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
        ];
    }
}
