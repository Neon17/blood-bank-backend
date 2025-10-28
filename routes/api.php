<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BloodRequestController;
use App\Http\Controllers\DonationProgramController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\UserController;

Route::get('/user', [UserController::class, 'profile'])->middleware('auth:sanctum');

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

// Only admin can see all requests active, inactive or scam
Route::get('/blood/requests/all', [BloodRequestController::class, 'allRequests'])
    ->middleware(['auth:sanctum', ]);

Route::get('/blood/requests/yours', [BloodRequestController::class, 'yours'])->middleware('auth:sanctum');

Route::get('/blood/requests/{id}', [BloodRequestController::class, 'show']);
Route::get('/blood/requests/{id}/edit', [BloodRequestController::class, 'edit'])->middleware('auth:sanctum');


Route::patch('/blood/requests/{id}', [BloodRequestController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/blood/requests/{id}', [BloodRequestController::class, 'destroy'])->middleware('auth:sanctum');
Route::get('/donors', [UserController::class, 'donors'])->middleware('optionalSanctum');
Route::post('/blood/requests/{id}/finish', [BloodRequestController::class, 'finish'])->middleware('auth:sanctum');

Route::get('/users', [UserController::class, 'index'])->middleware(['auth:sanctum']);

// logged in user photo update for profile and donor profile
Route::patch('/profile/photo/update', [UserController::class, 'updatePhoto'])->middleware('auth:sanctum');
Route::patch('/donor/photo/update', [UserController::class, 'updatePhoto'])->middleware('auth:sanctum');


Route::middleware('optionalSanctum')->group(function () {
    Route::get('/blood/donors', [DonorController::class, 'index']); // Public
});

Route::prefix('/blogs')->middleware('optionalSanctum')->group(function () {
    Route::get('/', [BlogController::class, 'index']);
    Route::get('/{blog}', [BlogController::class, 'show'])->can('view', 'blog');
    Route::post('/', [BlogController::class, 'store'])->middleware('auth:sanctum', 'can:create,App\Models\Blog' );
    Route::put('/{blog}', [BlogController::class, 'update'])->middleware('auth:sanctum', )->can('update', 'blog');
    Route::delete('/{blog}', [BlogController::class, 'destroy'])->middleware('auth:sanctum', 'can:delete,blog');
});

Route::prefix('/donation-programs')->middleware('optionalSanctum')->group(function () {
    Route::get('/', [DonationProgramController::class, 'index']);
    Route::get('/{id}', [DonationProgramController::class, 'show']);
    Route::post('/', [DonationProgramController::class, 'store'])->middleware('auth:sanctum', );
    Route::put('/{id}', [DonationProgramController::class, 'update'])->middleware('auth:sanctum', );
    Route::delete('/{id}', [DonationProgramController::class, 'destroy'])->middleware('auth:sanctum', );
});

// Group for authenticated users
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/blood/donors', [DonorController::class, 'store']);
    Route::get('/blood/donors/me', [DonorController::class, 'me']);
    Route::get('/blood/donors/{donor}', [DonorController::class, 'show']);
    Route::put('/blood/donors/{donor}', [DonorController::class, 'update']);
    Route::delete('/blood/donors/{donor}', [DonorController::class, 'destroy']);

    Route::get('/blood/donors/{donor}/edit', [DonorController::class, 'edit']);

    // Only admin can change verification status
    Route::patch('/blood/donors/{donor}/status', [DonorController::class, 'changeStatus'])
        ->middleware();

    Route::patch('/blood/requests/{request}/approve', [BloodRequestController::class, 'adminApproveRequest'])
        ->middleware();
    Route::patch('/blood/requests/{request}/reject', [BloodRequestController::class, 'adminRejectRequest'])
        ->middleware();
    Route::patch('/blood/requests/{request}/cancel', [BloodRequestController::class, 'cancelRequest']);
    Route::patch('/blood/requests/{request}/complete', [BloodRequestController::class, 'completeRequest']);
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
