<?php

namespace App\Http\Controllers;

use App\Models\DonationProgram;
use Illuminate\Http\Request;

class DonationProgramController extends Controller
{
    public function index() {
        $donation_programs = DonationProgram::all();

        return response()->json([
            'status' => 'success',
            'total' => $donation_programs->count(),
            'data' => $donation_programs
        ]);
    }

    public function show($id) {
        $donation_program = DonationProgram::find($id);
        return response()->json([
            'status' => 'success',
            'data' => $donation_program
        ]);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required|string|max:50',
            'organized_by' => 'required|string|max:255',
            'contact_info' => 'required|string|max:255',
        ]);

        $donation_program = DonationProgram::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $donation_program
        ], 201);   
    }

    public function update(Request $request, $id) {
        $donation_program = DonationProgram::find($id);
        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'string',
            'location' => 'string|max:255',
            'date' => 'date',
            'time' => 'string|max:50',
            'organized_by' => 'string|max:255',
            'contact_info' => 'string|max:255',
        ]);

        $donation_program->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $donation_program
        ]);
    }

    public function destroy($id) {
        $donation_program = DonationProgram::find($id);
        if (!$donation_program) {
            return response()->json([
                'status' => 'error',
                'message' => 'Donation Program not found'
            ], 404);
        }
        $donation_program->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Donation Program deleted successfully'
        ]);
    }
}
