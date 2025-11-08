<?php

namespace App\Http\Controllers;

use App\Models\BloodInventory;
use App\Models\BloodBank;
use Illuminate\Http\Request;

class BloodInventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bloodInventories = BloodInventory::with('bloodBank')
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $bloodInventories
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bloodBanks = BloodBank::where('is_active', true)->get();
        $bloodTypes = BloodInventory::BLOOD_TYPES;

        return response()->json([
            'status' => 'success',
            'data' => [
                'bloodBanks' => $bloodBanks,
                'bloodTypes' => $bloodTypes
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'blood_bank_id' => 'required|exists:blood_banks,id',
            'blood_type' => 'required|in:' . implode(',', BloodInventory::BLOOD_TYPES),
            'quantity' => 'required|integer|min:0'
        ]);

        // Check if inventory already exists for this blood bank and type
        $existingInventory = BloodInventory::where('blood_bank_id', $validated['blood_bank_id'])
            ->where('blood_type', $validated['blood_type'])
            ->first();

        if ($existingInventory) {
            // Update quantity if exists
            $existingInventory->increment('quantity', $validated['quantity']);
            return response()->json([
                'status' => 'success',
                'data' => $existingInventory
            ]);
        }

        $bloodInventory = BloodInventory::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $bloodInventory
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BloodInventory $bloodInventory)
    {
        $validated = $request->validate([
            'blood_bank_id' => 'required|exists:blood_banks,id',
            'blood_type' => 'required|in:' . implode(',', BloodInventory::BLOOD_TYPES),
            'quantity' => 'required|integer|min:0'
        ]);

        $bloodInventory =$bloodInventory->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $bloodInventory
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BloodInventory $bloodInventory)
    {
        $bloodInventory = $bloodInventory->delete();

        return response()->json([
            'status' => 'success',
            'data' => $bloodInventory
        ]);
    }

    /**
     * Show inventory by blood bank
     */
    public function byBloodBank(BloodBank $bloodBank)
    {
        $bloodInventories = $bloodBank->bloodInventories()->get();
        return response()->json([
            'status' => 'success',
            'data' => $bloodInventories
        ]);
    }

    /**
     * Search blood availability
     */
    public function search(Request $request)
    {
        $bloodType = $request->get('blood_type');
        $bloodBankId = $request->get('blood_bank_id');

        $query = BloodInventory::with('bloodBank')
            ->where('quantity', '>', 0);

        if ($bloodType) {
            $query->where('blood_type', $bloodType);
        }

        if ($bloodBankId) {
            $query->where('blood_bank_id', $bloodBankId);
        }

        $bloodInventories = $query->latest()->paginate(10);
        $bloodBanks = BloodBank::where('is_active', true)->get();
        $bloodTypes = BloodInventory::BLOOD_TYPES;

        return response()->json([
            'status' => 'success',
            'data' => [
                'bloodInventories' => $bloodInventories,
                'bloodBanks' => $bloodBanks,
                'bloodTypes' => $bloodTypes
            ]
        ]);
    }
}
