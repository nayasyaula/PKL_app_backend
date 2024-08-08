<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Log credentials for debugging (remove in production)
        Log::info('Login Attempt:', $credentials);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            $expiresAt = Carbon::now('Asia/Jakarta')->addHours(1);

            $token = $user->createToken('Personal Access Token', ['*'], $expiresAt);

            $expiresAtFormatted = $expiresAt->format('Y-m-d H:i:s');

            return response()->json([
                'token' => $token->plainTextToken,
                'expires_at' => $expiresAtFormatted,
                'user' => $user
            ], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            // Revoke all tokens...
            $user->tokens->each(function ($token, $key) {
                $token->delete();
            });
            return response()->json(['message' => 'Successfully logged out']);
        } else {
            return response()->json(['error' => 'An error occurred while logging out.'], 500);
        }
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8', // Pastikan validasi ini ada
            'telp' => 'required|string|max:15',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Perempuan,Laki-laki',
            'status' => 'required|string|max:255',
            'jurusan' => 'required|string|max:255',
            'sekolah' => 'required|string|max:255',
            'agama' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
        ]);

        Log::info('Validated Data: ', $validatedData);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $token = $user->createToken('Personal Access Token')->plainTextToken;

        return response()->json(['token' => $token], 201);
    }
}
