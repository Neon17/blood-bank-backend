<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDonorRequest;
use App\Http\Requests\UpdateDonorRequest;
use App\Models\Donor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\select;

class DonorController extends Controller
{
    //
    public function index(Request $request)
    {
        $blood_group = $request->query('blood_group');
        $radius = $request->query('radius');
        $latitude = $request->query('latitude');
        $longitude = $request->query('longitude');
        $donorApplication = null;

        info($request->query());

        if ($blood_group) {
            info("everything defined");
            $donorApplication = Donor::with('user')->where('blood_group', $blood_group)->nearby($latitude, $longitude, $radius)->get();
        }  
        else {
            info("everything undefined");
            $donorApplication = Donor::with('user')->nearby($latitude, $longitude, $radius)->get();
        }

        return response()->json([
            'status' => 'success',
            'total' => count($donorApplication),
            'data' => $donorApplication
        ], 200, [
            'Content-Type' => 'text/json'
        ]);
    }

    public function show(Donor $donor)
    {
        return response()->json([
            'status' => 'success',
            'data' => $donor
        ], 200);
    }

    public function me() {
        $donorApplication = Donor::where('user_id', Auth::id())->get();
        return response()->json([
            'status' => 'success',
            'total' => count($donorApplication),
            'data' => $donorApplication[0]
        ], 200, [
            'Content-Type' => 'text/json'
        ]);
    }

    public function store(StoreDonorRequest $request)
    {
        $data = $request->validated();
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

    public function update(UpdateDonorRequest $request, Donor $donor)
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

        // Authorization check
        if (!($donor->user_id !== Auth::id() || Auth::user()->role !== 'ADMIN')) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Unauthorized to edit this Donor Application'
            ], 403);
        }

        $donorApplication = Donor::find($donor->id);
        $donorApplication->update($request->validated());
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

        $updatedDonor = Donor::where('id', $donor->id)->update([
            'verification_status' => $data['status'],
            'admin_message' => $data['message']??null
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
