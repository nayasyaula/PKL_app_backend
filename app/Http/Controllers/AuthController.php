<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();

        // Tentukan waktu kedaluwarsa token
        $expiresAt = Carbon::now('Asia/Jakarta')->addHours(1); // Token kedaluwarsa dalam 1 jam

        // Buat token dengan waktu kedaluwarsa
        $token = $user->createToken('Personal Access Token', ['*'], $expiresAt);

        // Pilih format waktu kedaluwarsa yang diinginkan
        // $expiresAtTimestamp = $expiresAt->timestamp; // UNIX Timestamp
        // $expiresAtISO = $expiresAt->toIso8601String(); // ISO 8601
        $expiresAtFormatted = $expiresAt->format('Y-m-d H:i:s'); // Custom Format

        return response()->json([
            'token' => $token->plainTextToken,
            'expires_at' => $expiresAtFormatted, // Ganti dengan $expiresAtISO atau $expiresAtFormatted sesuai format yang diinginkan
            'user' => $user
        ], 200);
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
        Log::info('Registration Request: ', $request->all()); // Logging untuk debugging

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

        return response()->json(['success' => true, 'message' => 'Registration successful']);
    }
}
