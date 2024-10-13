<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Ambil kredensial dari request
        $credentials = $request->only('email', 'password');

        // Log kredensial untuk debugging (hapus log ini di produksi)
        Log::info('Login Attempt:', ['email' => $credentials['email'], 'password' => $credentials['password']]);

        // Cek kredensial dan autentikasi
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Buat token personal access
            $token = $user->createToken('Personal Access Token')->plainTextToken;

            // Kembalikan response dengan token dan data pengguna
            return response()->json([
                'token' => $token,
                'user' => $user
            ], 200);
        } else {
            // Jika kredensial salah, kembalikan response error
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
            'password_confirmation' => 'required|string|min:8',
            'telp' => 'nullable|string|max:15',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:Perempuan,Laki-laki',
            'status' => 'nullable|string|max:255',
            'jurusan' => 'nullable|string|max:255',
            'sekolah' => 'nullable|string|max:255',
            'agama' => 'nullable|string|max:255',
            'alamat' => 'nullable|string|max:500',
        ]);

        Log::info('Validated Data: ', $validatedData);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => 'user',
            'telp' => $validatedData['telp'] ?? null,
            'tempat_lahir' => $validatedData['tempat_lahir'] ?? null,
            'tanggal_lahir' => $validatedData['tanggal_lahir'] ?? null,
            'jenis_kelamin' => $validatedData['jenis_kelamin'] ?? null,
            'status' => $validatedData['status'] ?? null,
            'jurusan' => $validatedData['jurusan'] ?? null,
            'sekolah' => $validatedData['sekolah'] ?? null,
            'agama' => $validatedData['agama'] ?? null,
            'alamat' => $validatedData['alamat'] ?? null,
        ]);

        $token = $user->createToken('Personal Access Token')->plainTextToken;

        return response()->json(['token' => $token], 201);
    }


    public function changePassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password changed successfully']);
    }

    public function verifyPassword(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = auth()->Auth::user()();

        if ($user && Hash::check($request->password, $user->password)) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Incorrect password.'], 401);
        }
    }
}
