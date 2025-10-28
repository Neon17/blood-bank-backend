<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    public function index()
    {

        // How to make User as Admin, creating entry in admin table and linking it to user
        
        $this->authorize('viewAny', Auth::user());
        $user = User::with('profilePhoto')->paginate(30);

        return response()->json([
            'status' => 'success',
            'data' => $user
        ], 200);
    }

    public function updateMe(Request $request)
    {
        $user = User::find(Auth::id());

        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'address' => 'required',
            'blood_group' => 'required|in:A+,A-,O+,O-,B+,B-,AB+,AB-',
            'dob' => 'required|date',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // write all editable fields
        $user->name = $request->name;
        $user->email = $request->email;
        $user->address = $request->address;
        $user->dob = $request->dob;
        $user->city = $request->city;
        $user->country = $request->country;
        $user->current_city = $request->current_city;
        $user->will_donate = $request->will_donate ?? false;
        if ($request->blood_group) {
            $user->blood_group = $request->blood_group;
        }

        $user->latitude = $request->lat;
        $user->longitude = $request->lng;

        if ($request->hasFile('profile_photo')) {
            $user->storeUpload($request->file('profile_photo'), 'public');
        }

        $user->update();
        return response()->json([
            'status' => 'success',
            'data' => $user
        ], 200, [
            'Content-Type' => 'text/json'
        ]);
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = User::find(Auth::id());

        if ($request->hasFile('profile_photo')) {
            $user->storeUpload($request->file('profile_photo'), 'public');
        }

        $user->update();
        $user->refresh()->with('profilePhoto');

        return response()->json([
            'status' => 'success',
            'data' => $user
        ], 200, [
            'Content-Type' => 'text/json'
        ]);
    }

    public function donors(Request $request)
    {
        $users = null;

        if ($request->query('search')) {
            $donorApplication = User::where('name', 'like', '%' . $request->search . '%')
                ->where('will_donate', '1')->get();
            return response()->json([
                'status' => 'success',
                'total' => count($donorApplication),
                'data' => $donorApplication
            ], 200, [
                'Content-Type' => 'text/json'
            ]);
        }

        if (Auth::check()) {
            $latitude = Auth::user()->latitude;
            $longitude = Auth::user()->longitude;

            $radiusInKm = request('radiusInKm') ?? null;

            if ($radiusInKm)
                $users = User::donors()->nearby($latitude, $longitude, $radiusInKm)->get();
            else
                $users = User::donors()->get();
        } else {
            $users = User::donors()->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $users
        ], 200, [
            'Content-Type' => 'text/json'
        ]);
    }

    public function makeMeDonor()
    {
        $user = User::find(Auth::id());
        $user->is_donor = true;
        $user->save();
        return response()->json([
            'status' => 'success',
            'data' => $user
        ], 200, [
            'Content-Type' => 'text/json'
        ]);
    }

    public function removeMeDonor()
    {
        $user = User::find(Auth::id());
        $user->is_donor = false;
        $user->save();
        return response()->json([
            'status' => 'success',
            'data' => $user
        ], 200, [
            'Content-Type' => 'text/json'
        ]);
    }

    public function test()
    {
        return response()->json([
            'status' => '200',
            'message' => 'Successful Integration of Laravel with Nextjs'
        ], 200, [
            'Content-Type' => 'text/json'
        ]);
    }

    public function profile()
    {
        $user = User::where('id', Auth::id())->with('profilePhoto')->first();

        $formatted_user = new UserResource( $user );
        return response()->json($formatted_user, 200);
    }
}
