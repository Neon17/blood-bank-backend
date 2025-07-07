<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // all CRUD operations for User

    public function index()
    {
        $user = User::get();
        return $user;
    }

    public function updateMe(Request $request) {
        $user = User::find(Auth::id());

        $request->validate([
            'name' => 'required',
            'email' => 'required',
        ]);

        // write all editable fields
        $user->name = $request->name;
        $user->email = $request->email;
        $user->address = $request->address;
        $user->dob = $request->dob;
        $user->city = $request->city;
        $user->country = $request->country;
        $user->current_city = $request->current_city;
        $user->will_donate = $request->will_donate?? false;
        
        $user->latitude = $request->lat;
        $user->longitude = $request->lng;

        $user->update();
        return response()->json([
            'status' => 'success',
            'data' => $user
        ], 200, [
            'Content-Type' => 'text/json'
        ]);
    }

    public function donors()
    {
        $users = null;

        info("blood donors index hit");
        if (Auth::check()){
            $latitude = Auth::user()->latitude;
            $longitude = Auth::user()->longitude;
    
            $radiusInKm = request('radiusInKm') ?? null;
    
            if ($radiusInKm)
                $users = User::donors()->nearby($latitude, $longitude, $radiusInKm)->get();
            else 
                $users = User::donors()->get();
        }
        else {
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
        //we have to test this
        return response()->json(Auth::user(), 200);
    }
}
