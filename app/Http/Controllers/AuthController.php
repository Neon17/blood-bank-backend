<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function signin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Credentials'
            ], 401);
        }

        if (Auth::attempt($credentials)) {
            $token = $user->createToken('api-token')->plainTextToken;
            return response()->json([
                'user' => Auth::user(),
                'token' => $token,
                'message' => 'Authenticated'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid credentials',
            'password match' => ($user->password === $request->password)
        ], 401);
    }

    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'confirmPassword' => 'required|same:password'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'address' => $request->address || null,
            'dob' => $request->dob || null,
            'role' => 'admin'
        ]);
        if ($user) {
            $token = $user->createToken('api-token')->plainTextToken;
            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'token' => $token,
                'user' => $user
            ], 201);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'User creation failed'
        ], 400);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'User logged out successfully'
        ], 200);
    }
}
