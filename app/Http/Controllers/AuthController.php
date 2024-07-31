<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Log;
class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            // Log::info('user: ' . $user);
            // $sessionId = $request->cookie('laravel_session');
            // $token = $request->cookie('XSRF-TOKEN');
            // Log::info('token: ' . $token);
            // Log::info('session: ' . $sessionId);
        
            $token = $user->createToken('Personal Access Token')->plainTextToken;
            // Kembalikan session token sebagai bagian dari respons JSON
            return response()->json([
                'token' => $token,
                'user' => $user
            ], 200);
            // $token = $user->createToken('Personal Access Token')->plainTextToken;

            // return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json(['message' => 'Logged out'], 200);
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $token = $user->createToken('Personal Access Token')->plainTextToken;

        return response()->json(['token' => $token], 201);
    }
}
