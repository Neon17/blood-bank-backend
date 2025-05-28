<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use Illuminate\Http\Request;

class BloodRequestController extends Controller
{
    //

    public function index()
    {
        $bloodRequest = BloodRequest::with('user', 'bloodBank')->get();
        return response()->json([
            'status' => 'success',
            'data' => $bloodRequest
        ]);
    }

    public function store(Request $request)
    {
        // should we use attach($user_id) here?

        $user_id = $request->user()->id; // comes from Authorization Bearer Token in Sanctum
        if (!$user_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $request->validate([
            'blood_type' => 'required',
            'quantity' => 'required',
            'date_time' => 'required',
            'exact_location' => 'required',
            'contact_number' => 'required',
            'city' => 'required',
            'state' => 'required',
        ]);

        $bloodRequest = new BloodRequest();
        $bloodRequest->blood_type = $request->blood_type;
        $bloodRequest->quantity = $request->quantity;
        $bloodRequest->date_time = $request->date_time;
        $bloodRequest->exact_location = $request->exact_location;
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

    public function show($id)
    {
        // $id is Blood Request Id
        $bloodRequest = BloodRequest::with('user', 'bloodBank')->find($id);
        return response()->json([
            'status' => 'success',
            'data' => $bloodRequest
        ]);
    }

    public function delete($id)
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
