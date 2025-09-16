<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use Faker\Core\Blood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BloodRequestController extends Controller
{
    //

    public function index()
    {
        // I have to sort in the order of nearest requests based on logged in user
        // If that users' location is null, then all requests are shown
        // If request has no query parameter (radiusInKm), then all requests are shown

        info("blood requests index hit");
        if (Auth::check()) {
            $latitude = Auth::user()->latitude;
            $longitude = Auth::user()->longitude;

            $radiusInKm = request('radiusInKm') ?? null;

            if ($radiusInKm)
                $bloodRequest = BloodRequest::with('user', 'bloodBank')->nearby($latitude, $longitude, $radiusInKm)->get();
            else
                $bloodRequest = BloodRequest::with('user', 'bloodBank')->get();
        } else
            $bloodRequest = BloodRequest::with('user', 'bloodBank')->get();

        return response()->json([
            'status' => 'success',
            'data' => $bloodRequest
        ]);
    }

    public function store(Request $request)
    {
        // should we use attach($user_id) here?
        // to store blood requests, you have to fillup the contact number details

        $user_id = $request->user()->id; // comes from Authorization Bearer Token in Sanctum
        if (!$user_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $request->validate([
            'blood_type' => 'required',
            'quantity' => 'required|numeric|min:1',
            'date_time' => 'required|date',
            'exact_location' => 'required',
            'contact_number' => 'required|numeric|digits_between:7,15',
            'city' => 'required',
            'country' => 'required',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $bloodRequest = new BloodRequest();
        $bloodRequest->blood_type = $request->blood_type;
        $bloodRequest->quantity = $request->quantity;
        $bloodRequest->date_time = $request->date_time;
        $bloodRequest->exact_location = $request->exact_location;

        // Trim and round the decimal places before assigning
        $bloodRequest->latitude = round($request->latitude, 8);
        $bloodRequest->longitude = round($request->longitude, 8);

        $bloodRequest->contact_number = $request->contact_number;
        $bloodRequest->city = $request->city;
        $bloodRequest->state = $request->state;
        $bloodRequest->user_id = $user_id;
        $bloodRequest->save();

        return response()->json([
            'status' => 'success',
            'data' => $bloodRequest
        ]);
    }

    public function finish($id)
    {
        // if blood request is fulfilled, it goes to this route
        $bloodRequest = BloodRequest::find($id);
        $bloodRequest->active_status = false;
        $bloodRequest->update();
        return response()->json([
            'status' => 'success',
            'data' => $bloodRequest
        ]);
    }

    public function edit(BloodRequest $bloodRequest)
    {
        return response()->json([
            'status' => 'success',
            'data' => $bloodRequest
        ]);
    }

    public function update(Request $request, $id)
    {
        $bloodRequest = BloodRequest::find($id);
        $request->validate([
            'blood_type' => 'required',
            'quantity' => 'required',
            'date_time' => 'required',
            'exact_location' => 'required',
            'contact_number' => 'required',
            'city' => 'required',
            'country' => 'required'
        ]);

        $bloodRequest->update($request->all());
        return response()->json([
            'status' => 'success',
            'data' => $bloodRequest
        ]);
    }

    public function show($id)
    {
        // $id is Blood Request Id
        $bloodRequest = BloodRequest::with('user', 'bloodBank')->find($id);
        return response()->json([
            'status' => 'success',
            'data' => $bloodRequest
        ]);
    }

    public function destroy($id)
    {
        $bloodRequest = BloodRequest::find($id);
        if (!$bloodRequest) {
            return response()->json([
                'status' => 'error',
                'message' => 'Blood Request not found'
            ], 404);
        }
        $bloodRequest->delete();
        return response()->json([
            'status' => 'success',
            'data' => $bloodRequest
        ]);
    }
}
