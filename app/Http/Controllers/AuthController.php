<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //
    public function signin(Request $request)
    {
        return 'signin';
    }

    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'confirmPassword' => 'required|same:password'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'address' => $request->address || null,
            'dob' => $request->dob || null
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

    public function login(Request $request)
    {
        return 'login';
    }
}
