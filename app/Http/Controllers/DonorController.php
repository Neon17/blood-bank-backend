<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDonorRequest;
use App\Http\Requests\UpdateDonorRequest;
use App\Http\Resources\DonorResource;
use App\Models\Donor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DonorController extends Controller
{
    //
    public function index(Request $request)
    {
        $blood_group = $request->query('blood_group');
        $radius = $request->query('radius');
        $latitude = $request->query('latitude');
        $longitude = $request->query('longitude');
        $search = $request->query('search', null);

        $query = Donor::with(['user', 'user.profilePhoto']); 

        if (Auth::user()->role === 'ADMIN') {
            $query = $query->withoutGlobalScopes();
        }

        if ($radius >0 && $latitude && $longitude) {
            $query = $query->with(['user', 'user.profilePhoto'])
                ->nearby($latitude, $longitude, $radius);
        } else {
            $query = $query->with(['user', 'user.profilePhoto'])->nearby();
        }

        if ($search) {
            $query = $query->whereAny(['contact_number', 'address'], 'LIKE', '%' . $search . '%');
        }


        if ($blood_group) {
            $query = $query->where('blood_group', $blood_group);
        }

        $query = $query->orderBy('updated_at', 'desc');
        $donors = $query->paginate(30);

        return response()->json([
            'status' => 'success',
            'data' => [
                'current_page' => $donors->currentPage(),
                'data' => DonorResource::collection($donors),
                'first_page_url' => $donors->url(1),
                'from' => $donors->firstItem(),
                'last_page' => $donors->lastPage(),
                'last_page_url' => $donors->url($donors->lastPage()),
                'links' => $donors->linkCollection()->toArray(),
                'next_page_url' => $donors->nextPageUrl(),
                'path' => $donors->path(),
                'per_page' => $donors->perPage(),
                'prev_page_url' => $donors->previousPageUrl(),
                'to' => $donors->lastItem(),
                'total' => $donors->total(),
            ]
        ], 200);
    }

    public function show(Donor $donor)
    {
        return response()->json([
            'status' => 'success',
            'data' => $donor
        ], 200);
    }

    public function me()
    {
        $donorApplication = Donor::withoutGlobalScopes()->where('user_id', Auth::id())->get();
        return response()->json([
            'status' => 'success',
            'total' => count($donorApplication),
            'data' => $donorApplication[0] ?? null
        ], 200, [
            'Content-Type' => 'text/json'
        ]);
    }

    public function store(StoreDonorRequest $request)
    {
        $data = $request->validated();

        $donor = Donor::where('user_id', Auth::id())->first();

        if ($donor) {
            return response()->json([
                'status' => 'fail',
                'message' => 'You have already submitted a Donor Application'
            ], 403);
        }

        $data["user_id"] = $request->user()->id;

        $donorApplication = Donor::create($data);
        if ($request->hasFile('verification_photo')) {
            $donorApplication->storeUpload($request->file('verification_photo'), 'public');
        }
        if (Auth::user()->role === 'admin') {
            $donorApplication->status = 'APPROVED';
            $donorApplication->save();
        }
        if ((Auth::user()->role === 'admin') || (Auth::user()->role === 'blood_bank')) {
            $donorApplication->eligible_to_donate = true;
        }

        $donorApplication->refresh();

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
        if (($donorApplication->user->id == Auth::id()) || (Auth::user()->role === 'admin')) {
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
        if (!($donor->user_id !== Auth::id() || Auth::user()->role !== 'admin')) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Unauthorized to edit this Donor Application'
            ], 403);
        }

        $donorApplication = Donor::find($donor->id);
        if ($request->hasFile('verification_photo')) {
            $donorApplication->storeUpload($request->file('verification_photo'), 'public');
        }

        $donorApplication->update($request->validated());
        if (Auth::user()->role !== 'admin') {
            $donor->eligible_to_donate = false;
        }
        if ((Auth::user()->role === 'admin') || (Auth::user()->role === 'blood_bank')) {
            if (isset($request->eligible_to_donate)) {
                $donor->eligible_to_donate = $request->eligible_to_donate;
            }
        }
        return response()->json([
            'status' => 'success',
            'data' => $donorApplication
        ], 200, [
            'Content-Type' => 'text/json'
        ]);
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $donor = Donor::find(Auth::id());

        if ($request->hasFile('profile_photo')) {
            $donor->storeUpload($request->file('profile_photo'), 'public');
        }

        if (Auth::user()->role !== 'admin') {
            $donor->eligible_to_donate = false;
        }
        $donor->update();

        return response()->json([
            'status' => 'success',
            'data' => $donor
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
            'admin_message' => $data['message'] ?? null
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

        if (($donorApplication->user && $donorApplication->user->id == Auth::id()) || (Auth::user()->role === 'admin')) {
            // ok authorized
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => "Unauthorized to delete this Donor Application"
            ], 401, [
                'Content-Type' => 'text/json'
            ]);
        }

        if (($donorApplication->status === 'approved') && (Auth::user()->role !== 'admin')) {
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
