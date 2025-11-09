<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use Faker\Core\Blood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BloodRequestController extends Controller
{
    //
    public function getResults(Request $request, $query)
    {
        info($request->all());
        $search = $request->input('search', null);
        $willing = $request->input('willing', null);
        $status = $request->input('verification_status', null);
        $history = $request->input('donation_history', null);
        $last_donated = $request->input('last_donated', null);
        $blood_type = $request->input('blood_type', null);

        if ($search) {
            $query = $query->whereAny(['exact_location', 'contact_number', 'city'], 'LIKE', '%' . $search . '%')
                ->orWhereHas('user', function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search . '%');
                });
        }

        if ($willing) {
            $query = $query->where('will_donate', $willing);
        }
        if ($blood_type) {
            $query = $query->where('blood_type', $blood_type);
        }
        if ($status) {
            $query = $query->where('verification_status', $status);
        }

        return $query;
    }

    public function index(Request $request)
    {
        // I have to sort in the order of nearest requests based on logged in user
        // If that users' location is null, then all requests are shown
        // If request has no query parameter (radiusInKm), then all requests are shown

        $bloodRequest = BloodRequest::query();
        if (Auth::check()) {
            $latitude = Auth::user()->latitude;
            $longitude = Auth::user()->longitude;

            $radiusInKm = request('radiusInKm') ?? null;

            if ($radiusInKm)
                $bloodRequest = $bloodRequest->with('user', 'bloodBank')->nearby($latitude, $longitude, $radiusInKm)->with('verificationPhoto');
            else
                $bloodRequest = $bloodRequest->with('user', 'bloodBank')->nearby()->with('verificationPhoto');
        } else
            $bloodRequest = $bloodRequest->with('user', 'bloodBank')->nearby();

        $bloodRequest = $this->getResults($request, $bloodRequest)->orderBy('updated_at', 'desc')->paginate(30);

        return response()->json([
            'status' => 'success',
            'data' => $bloodRequest
        ]);
    }

    public function yourRequests(Request $request)
    {
        $bloodRequests = BloodRequest::withoutGlobalScopes()->where('user_id', Auth::user()->id);
        $bloodRequests = $this->getResults($request, $bloodRequests)->orderBy('updated_at', 'desc')->paginate(30);
        return response()->json([
            'status' => 'success',
            'data' => $bloodRequests
        ]);
    }

    public function allRequests(Request $request)
    {
        info("all requests called");
        info($request->all());
        $bloodRequests = BloodRequest::withoutGlobalScope('active')->with('user', 'bloodBank', 'verificationPhoto');
        $bloodRequests = $this->getResults($request, $bloodRequests)->orderBy('updated_at', 'desc')->paginate(30);
        return response()->json([
            'status' => 'success',
            'data' => $bloodRequests
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

        $validated = $request->validate([
            'blood_type' => 'required',
            'quantity' => 'required|numeric|min:1',
            'date_time' => 'required|date',
            'exact_location' => 'required',
            'contact_number' => 'required|numeric|digits_between:7,15',
            'city' => 'required',
            'country' => 'required',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'verification_photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $validated['user_id'] = $user_id;
        $bloodRequest = BloodRequest::create($validated);

        if ($request->hasFile('verification_photo')) {
            $bloodRequest->storeUpload($request->file('verification_photo'), 'public');
        }

        $bloodRequest->refresh();

        return response()->json([
            'status' => 'success',
            'data' => $bloodRequest
        ]);
    }

    public function finish($id)
    {
        // if blood request is fulfilled, it goes to this route
        $bloodRequest = BloodRequest::find($id);
        // $bloodRequest->active_status = false;
        $bloodRequest->update();
        return response()->json([
            'status' => 'success',
            'data' => $bloodRequest
        ]);
    }

    public function adminApproveRequest($id)
    {
        // Admin approves the request as legal and verified not scam
        $request = BloodRequest::withoutGlobalScopes()->find($id);
        // $request->active_status = true;
        $request->verification_status = 'approved';
        $request->update();
        return response()->json([
            'status' => 'success',
            'data' => $request
        ]);
    }

    public function adminRejectRequest($id)
    {
        // Admin can reject the request if found suspicious and not legal
        $request = BloodRequest::find($id);
        // $request->active_status = false;
        $request->verification_status = 'rejected';
        $request->update();
        return response()->json([
            'status' => 'success',
            'data' => $request
        ]);
    }

    public function cancelRequest($id)
    {
        // User can cancel the request saying it wasn't necessary
        $request = BloodRequest::find($id);
        // $request->active_status = false;
        $request->status = 'cancelled';
        $request->update();
        return response()->json([
            'status' => 'success',
            'data' => $request
        ]);
    }

    public function completeRequest($id)
    {
        // User can complete the request saying it was necessary
        $request = BloodRequest::find($id);
        // $request->active_status = false;
        $request->status = 'completed';
        $request->update();
        return response()->json([
            'status' => 'success',
            'data' => $request
        ]);
    }


    public function edit(BloodRequest $bloodRequest)
    {
        // This is to check the blood request details and permission for editing
        $blood_request = BloodRequest::where('id', $bloodRequest->id)->with('verificationPhoto')->first();
        return response()->json([
            'status' => 'success',
            'data' => $blood_request
        ]);
    }

    public function update(Request $request, $id)
    {
        $bloodRequest = BloodRequest::withoutGlobalScopes()->find($id);
        $validated = $request->validate([
            'blood_type' => 'required',
            'quantity' => 'required',
            'date_time' => 'required',
            'exact_location' => 'required',
            'contact_number' => 'required',
            'city' => 'required',
            'country' => 'required',
            'verification_photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'verification_status' => 'sometimes|string|in:pending,approved,rejected'
        ]);

        $bloodRequest->update($validated);

        if ($request->hasFile('verification_photo')) {
            $bloodRequest->storeUpload($request->file('verification_photo'), 'public');
        }
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
        $bloodRequest = BloodRequest::withoutGlobalScopes()->find($id);
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
