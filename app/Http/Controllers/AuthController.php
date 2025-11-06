<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function user(Request $request)
    {
        $user = User::findOrFail($request->user()->id);
        $user = $user->load('roles');
        return response()->json(['user' => $user]);
    }

    public function login(Request $request)
    {
        // user input validation
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // user email query
        $user = User::where('email', $request->email)->first();

        // user email and password verification
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'invalid_credentials',
                'message' => 'Credentials are incorrect.'
            ], 401);
        }

        // user session
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        // remove session
        $request->user()->tokens()->delete();

        // logout message
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
