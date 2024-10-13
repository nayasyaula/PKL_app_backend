<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        return view('auth.profile'); // Pastikan ada view profile.blade.php
    }

    public function showChangePasswordForm()
    {
        return view('auth.change-password'); // Pastikan ada view auth/change-password.blade.php
    }

    public function changePassword(Request $request)
    {
        // Validasi input
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed|min:6',
        ]);

        // Cek apakah password saat ini benar
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'The current password is invalid']);
        }

        // Update password
        Auth::user()->update(['password' => Hash::make($request->new_password)]);

        return redirect()->route('profile')->with('success', 'Password changed successfully');
    }

    public function showEditProfileForm()
    {
        return view('auth.edit-profile'); // Buat view ini untuk form ubah nama dan email
    }

    public function updateProfile(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
    
        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Hapus gambar profil yang lama jika ada
            if ($user->profile_picture) {
                \Storage::delete('public/' . $user->profile_picture);
            }
    
            // Simpan gambar baru
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }
    
        $user->save();
    
        return redirect()->route('profile')->with('success', 'Profile updated successfully.');
}

}
