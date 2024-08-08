<?php

namespace App\Http\Controllers;

use App\Models\User;
use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UserControllerApi extends Controller
{

    public function profile(Request $request)
    {
        $user = $request->user();
        Log::info('User authenticated', ['user' => $user]);

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = $user->id;
        Log::info('User ID: ' . $userId);

        $user = User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $profileImageBase64 = null;

        return response()->json([
            'name' => $user->name,
            'sekolah' => $user->sekolah,
            'profile' => $user->profile,
            'email' => $user->email,
            'telp' => $user->telp,
            'tempat_lahir' => $user->tempat_lahir,
            'tanggal_lahir' => $user->tanggal_lahir,
            'jenis_kelamin' => $user->jenis_kelamin,
            'status' => $user->status,
            'jurusan' => $user->jurusan,
            'agama' => $user->agama,
            'alamat' => $user->alamat
        ]);
    }

    public function uploadProfileImage(Request $request)
    {
        Log::info('Request received', ['request' => $request->all()]);
    
        $request->validate([
            'profile_image' => 'required|string', // Validasi string base64
        ]);
    
        $user = Auth::user();
    
        if ($user) {
            // Simpan gambar base64 ke database
            $image = $request->input('profile_image');
            $user->profile = $image;
            $user->save();
    
            return response()->json(['message' => 'Profile image uploaded successfully'], 200);
        } else {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
    }
    


    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'telp' => 'nullable|string|max:15',
            'tanggal_lahir' => 'nullable|date',
            'tempat_lahir' => 'nullable|string|max:255',
            'jenis_kelamin' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'agama' => 'nullable|string|max:255',
            'alamat' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update($request->all());

        return response()->json($user);
    }
}
