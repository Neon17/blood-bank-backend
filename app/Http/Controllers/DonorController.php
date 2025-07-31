<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DonorController extends Controller
{
    //
    public function index()
    {
        $donorApplication = Donor::all();
        return response()->json([
            'status' => 'success',
            'total' => count($donorApplication),
            'donor_applications' => $donorApplication
        ], 200, [
            'Content-Type' => 'text/json'
        ]);
    }

    public function show(Donor $donor)
    {
        if (!$donor) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Please select valid donor Id to show donor application'
            ], 400);
        }
        return response()->json([
            'status' => 'success',
            'data' => $donor
        ], 200);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
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
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'validation error',
                'errors' => $validator->errors()
            ], 422, [
                'Content-Type' => 'text/json'
            ]);
        }

        $data = $validator->validated();
        $data["user_id"] = $request->user()->id;

        $donorApplication = Donor::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $donorApplication
        ], 201, [
            'Content-Type' => 'text/json'
        ]);
    }

    public function edit(Donor $donor)
    {
        // for checking permission issue

        $donorApplication = $donor;
        if (!$donorApplication) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Donor Application Not Found'
            ], 404, [
                'Content-Type' => 'text/json'
            ]);
        }
        if (($donorApplication->user->id == Auth::id()) || (Auth::user()->role === 'ADMIN')) {
            return response()->json([
                'status' => 'success',
                'data' => $donorApplication
            ], 200, [
                'Content-Type' => 'text/json'
            ]);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => "Unauthorized to edit this Donor Application"
            ], 401, [
                'Content-Type' => 'text/json'
            ]);
        }
    }

    public function update(Request $request, Donor $donor)
    {
        $donorApplication = $donor;
        if (!$donorApplication) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Donor Application Not Found'
            ], 404, [
                'Content-Type' => 'text/json'
            ]);
        }
        $id = Auth::id();
        info ("donor id is $donor->user_id and Auth id is $id");
        // Authorization check
        if (!($donor->user_id !== Auth::id() || Auth::user()->role !== 'ADMIN')) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Unauthorized to edit this Donor Application'
            ], 403);
        }


        $validator = Validator::make($request->all(), [
            'contact_number' => 'sometimes|numeric|digits_between:7,15',
            'blood_type' => 'sometimes|string|in:A+,A-,O+,O-,B+,B-,AB+,AB-',
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

            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'validation Error',
                'errors' => $validator->errors()
            ], 422, [
                'Content-Type' => 'text/json'
            ]);
        }

        $donorApplication = Donor::find($donor->id);
        $donorApplication->update($validator->validated());
        return response()->json([
            'status' => 'success',
            'data' => $donorApplication
        ], 200, [
            'Content-Type' => 'text/json'
        ]);
    }

    public function changeStatus(Request $request, Donor $donor)
    {
        // includes status change from pending to approved or wrong
        // also includes admin message

        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:pending,approved,wrong',
            'message' => 'nullable|string|max:250'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        $updatedDonor = $donor->update([
            'status' => $data['status'],
            'message' => $data['message'] ?? null
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $updatedDonor
        ]);
    }

    public function destroy(Donor $donor)
    {
        $donorApplication = $donor;
        if (!$donorApplication) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Donor Application Not Found'
            ], 404, [
                'Content-Type' => 'text/json'
            ]);
        }

        if (($donorApplication->user && $donorApplication->user->id == Auth::id()) || (Auth::user()->role === 'ADMIN')) {
            // ok authorized
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => "Unauthorized to delete this Donor Application"
            ], 401, [
                'Content-Type' => 'text/json'
            ]);
        }

        if (($donorApplication->status === 'approved') && (Auth::user()->role !== 'ADMIN')) {
            return response()->json([
                'status' => 'fail',
                'message' => "Unauthorized to delete this Donor Application. Only Admin can delete verified donor application"
            ], 401, [
                'Content-Type' => 'text/json'
            ]);
        }
        $application = Donor::find($donor->id);
        Donor::destroy($donor->id);
        return response()->json([
            'status' => 'success',
            'data' => $application
        ]);
    }
}
