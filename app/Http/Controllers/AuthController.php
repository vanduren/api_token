<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed', // password_confirmation with password_confirmed
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            // 'password' => Hash::make($fields['password']),
            'password' => bcrypt($fields['password']),
        ]);

        $token = $user->createToken('maakt_niets_uit')->plainTextToken;
        Log::info('User created: ' . $user->name);
        Log::info('Token created: ' . $token);

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response()->json($response, Response::HTTP_CREATED);
    }

    public function logout(Request $request)
    {
        // Log::info('User logged out: ' . $request->user()->name);
        // Log::info('Token: ' . $request->user()->currentAccessToken());
        // $request->user()->currentAccessToken()->delete();
        // or all tokens of user
        auth()->user()->tokens()->delete();
        return response()->json(['message' => 'Successfully logged out'], Response::HTTP_OK);
    }

}
