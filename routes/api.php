<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BloodRequestController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', [UserController::class, 'test']);

// now do user signup and create token
// create route to regenerate sanctum token

Route::get('/signin', function () {
    return response()->json([
        'status' => 'Authenticated Required',
        'message' => 'Please login or signup to view this route'
    ], 403);
})->name('login');
Route::post('/login', [AuthController::class, 'signin']);
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/logout', [AuthController::class, 'login']);
Route::post('/makeMeDonor', [UserController::class, 'makeMeDonor'])->middleware('auth:sanctum');
Route::post('/removeMeDonor', [UserController::class, 'removeMeDonor'])->middleware('auth:sanctum');
Route::match(['put', 'post'], '/updateMe', [UserController::class, 'updateMe'])->middleware('auth:sanctum');

// Route::resource('/blood/requests', BloodRequestController::class);
Route::get('/blood/requests', [BloodRequestController::class, 'index'])->middleware('optionalSanctum');
Route::post('/blood/requests', [BloodRequestController::class, 'store'])->middleware('auth:sanctum');
Route::get('/blood/requests/{id}', [BloodRequestController::class, 'show']);
Route::get('/blood/requests/{id}/edit', [BloodRequestController::class, 'edit'])->middleware('auth:sanctum');
Route::patch('/blood/requests/{id}', [BloodRequestController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/blood/requests/{id}', [BloodRequestController::class, 'destroy'])->middleware('auth:sanctum');
Route::get('/donors', [UserController::class, 'donors'])->middleware('optionalSanctum');
Route::post('/blood/requests/{id}/finish', [BloodRequestController::class, 'finish'])->middleware('auth:sanctum');

Route::get('/users', [UserController::class, 'index'])->middleware(['auth:sanctum', 'isAdmin']);

Route::middleware('optionalSanctum')->group(function () {
    Route::get('/blood/donors', [DonorController::class, 'index']); // Public
});

// Group for authenticated users
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/blood/donors', [DonorController::class, 'store']);
    Route::get('/blood/donors/{donor}', [DonorController::class, 'show']);
    Route::put('/blood/donors/{donor}', [DonorController::class, 'update']);
    Route::delete('/blood/donors/{donor}', [DonorController::class, 'destroy']);

    // Only admin can change verification status
    Route::patch('/blood/donors/{donor}/status', [DonorController::class, 'changeStatus'])
        ->middleware('isAdmin');
});

Route::get('/sanctum/csrf-cookie', function () {
    try {
        return response()->json(['message' => 'CSRF token cookie set']);
    } catch (\Throwable $th) {
        return response()->json([
            'status' => 'error',
            'message' => $th->getMessage()
        ], 500);
    }
});
