<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonationController extends Controller
{
    protected function getResults($request, $query) {
        return $query;
    }

    public function index(Request $request)
    {
        $donations = Donation::query();
        $donations = $this->getResults($request, $donations);
        $donations = $donations->with(['user', 'user.profilePhoto', 'uploadable'])->paginate(30);
        info($donations);

        return response()->json([
            'status' => 'success',
            'data' => $donations
        ]);
    }

    public function me(Request $request) {
        $donations = Donation::where('user_id', Auth::id())->get();

        return response()->json([
            'status' => 'success',
            'data' => $donations
        ]);
    }

    public function show(Donation $donation)
    {
        return response()->json([
            'status' => 'success',
            'data' => $donation
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'quantity' => 'required',
            'user_id' => 'required|exists:users,id',
            'blood_request_id' => 'required|exists:blood_requests,id',
            'date' => 'required|date',
            'time' => 'required|date',
            'status' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'contact_info' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'verification_photo' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        $donation = Donation::create($validated);
        if ((Auth::user()->role === 'blood_bank') || (Auth::user()->role === 'admin')) {
            $donation->verification_status = 'approved';
            $donation->update();
        }

        if ($request->hasFile('verification_photo')) {
            $donation->storeUpload($request->file('verification_photo'), 'public');
        }

        return response()->json([
            'status' => 'success',
            'data' => $donation
        ]);
    }

    public function update(Request $request, Donation $donation)
    {
        $validated = $request->validate([
            'quantity' => 'required',
            'user_id' => 'required|exists:users,id',
            'blood_request_id' => 'required|exists:blood_requests,id',
            'date' => 'required|date',
            'time' => 'required|date',
            'status' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'contact_info' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'verification_photo' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        $donation->update($validated);
        if ((Auth::user()->role === 'blood_bank') || (Auth::user()->role === 'admin')) {
            if (isset($request->verification_status)){
                $donation->verification_status = $request->verification_status;
                $donation->update();
            }
        }

        if ($request->hasFile('verification_photo')) {
            $donation->storeUpload($request->file('verification_photo'), 'public');
        }

        return response()->json([
            'status' => 'success',
            'data' => $donation
        ]);
    }   

    public function destroy(Donation $donation)
    {
        $donation->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Donation deleted successfully'
        ]);
    }
}
