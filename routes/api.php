<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BloodRequestController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', [UserController::class, 'test']);

// now do user signup and create token
// create route to regenerate sanctum token

Route::get('/signin', function() {
    return response()->json([
        'status' => 'Authenticated Required',
        'message' => 'Please login or signup to view this route'
    ], 403);
})->name('login');
Route::post('/signin', [AuthController::class, 'signin']);
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/logout', [AuthController::class, 'login']);

Route::get('/blood/requests', [BloodRequestController::class, 'index']);
Route::post('/blood/requests', [BloodRequestController::class, 'store'])->middleware('auth:sanctum');
Route::get('/blood/requests/{id}', [BloodRequestController::class, 'show']);
Route::delete('/blood/requests/{id}', [BloodRequestController::class, 'delete'])->middleware('auth:sanctum');

Route::get('/users', [UserController::class, 'index']);

Route::get('/sanctum/csrf-cookie', function(){
    try {
        return response()->json(['message' => 'CSRF token cookie set']);
    } catch (\Throwable $th) {
        return response()->json([
            'status' => 'error',
            'message' => $th->getMessage()
        ], 500);
    }
});