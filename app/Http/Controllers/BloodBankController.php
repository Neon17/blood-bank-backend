<?php

namespace App\Http\Controllers;

use App\Models\BloodBank;
use Illuminate\Http\Request;

class BloodBankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bloodBanks = BloodBank::latest()->paginate(10);
        return response()->json([
            'status' => 'success',
            'data' => $bloodBanks
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //just for permission
        return response()->json([
            'status' => 'success',
            'data' => []
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone_number' => 'required|string|max:15',
            'email' => 'nullable|email',
            'contact_person' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        $bloodBank = BloodBank::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $bloodBank
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BloodBank $bloodBank)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone_number' => 'required|string|max:15',
            'email' => 'nullable|email',
            'contact_person' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        $bloodBank->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $bloodBank
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BloodBank $bloodBank)
    {
        $bloodBank = $bloodBank->delete();

        return response()->json([
            'status' => 'success',
            'data' => $bloodBank
        ]);
    }
}