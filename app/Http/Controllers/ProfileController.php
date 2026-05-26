<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function index()
    {
        $user = Auth::user();
        $user->load(['siswa', 'guru', 'materiAsGuru']);
        if (request()->wantsJson() || request()->is('api/*')) {
            return response()->json(['user' => $user]);
        }

        return view('dashboard.profile', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('pengguna', 'email')->ignore($user->id),
            ],
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'nama.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain',
            'foto_profil.image' => 'Foto profil harus berupa file gambar.',
            'foto_profil.mimes' => 'Format foto profil harus JPEG, PNG, JPG, atau GIF.',
            'foto_profil.max' => 'Ukuran foto profil terlalu besar. Maksimal 2 MB. Silakan kompres atau pilih gambar yang lebih kecil.',
        ]);

        $updateData = [
            'nama' => $validated['nama'],
            'email' => $validated['email'],
        ];

        // Handle foto profil upload
        if ($request->hasFile('foto_profil')) {
            // Create uploads/profiles directory if it doesn't exist
            $uploadPath = public_path('uploads/profiles');
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            // Delete old foto if exists
            if ($user->foto_profil && File::exists(public_path($user->foto_profil))) {
                File::delete(public_path($user->foto_profil));
            }

            // Upload new foto
            $file = $request->file('foto_profil');
            $fileName = time() . '_' . $user->id . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);
            
            // Save path relative to public folder
            $updateData['foto_profil'] = 'uploads/profiles/' . $fileName;
        }

        $user->update($updateData);
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Profile berhasil diperbarui!', 'user' => $user->fresh()]);
        }

        return redirect()->route('profile.index')
            ->with('success', 'Profile berhasil diperbarui!');
    }

    /**
     * Upload foto profil separately.
     */
    public function uploadFoto(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'foto_profil' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'foto_profil.required' => 'Foto profil wajib diupload',
            'foto_profil.image' => 'Foto profil harus berupa file gambar.',
            'foto_profil.mimes' => 'Format foto profil harus JPEG, PNG, JPG, atau GIF.',
            'foto_profil.max' => 'Ukuran foto profil terlalu besar. Maksimal 2 MB. Silakan kompres atau pilih gambar yang lebih kecil.',
        ]);

        // Create uploads/profiles directory if it doesn't exist
        $uploadPath = public_path('uploads/profiles');
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        // Delete old foto if exists
        if ($user->foto_profil && File::exists(public_path($user->foto_profil))) {
            File::delete(public_path($user->foto_profil));
        }

        // Upload new foto
        $file = $request->file('foto_profil');
        $fileName = time() . '_' . $user->id . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($uploadPath, $fileName);
        
        // Save path relative to public folder
        $user->update([
            'foto_profil' => 'uploads/profiles/' . $fileName
        ]);
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Foto profil berhasil diupload!', 'foto_profil' => $user->foto_profil]);
        }

        return redirect()->route('profile.index')
            ->with('success', 'Foto profil berhasil diupload!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'kata_sandi_lama' => 'required|string',
            'kata_sandi_baru' => 'required|string|min:6',
            'kata_sandi_konfirmasi' => 'required|string|same:kata_sandi_baru',
        ], [
            'kata_sandi_lama.required' => 'Kata sandi lama wajib diisi',
            'kata_sandi_baru.required' => 'Kata sandi baru wajib diisi',
            'kata_sandi_baru.min' => 'Kata sandi baru minimal 6 karakter',
            'kata_sandi_konfirmasi.required' => 'Konfirmasi kata sandi wajib diisi',
            'kata_sandi_konfirmasi.same' => 'Konfirmasi kata sandi tidak cocok',
        ]);

        // Verify current password
        if (!Hash::check($validated['kata_sandi_lama'], $user->kata_sandi)) {
            return back()->withErrors([
                'kata_sandi_lama' => 'Kata sandi lama tidak benar.',
            ])->withInput();
        }

        // Update password
        $user->update([
            'kata_sandi' => Hash::make($validated['kata_sandi_baru']),
        ]);
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Kata sandi berhasil diubah!']);
        }

        return redirect()->route('profile.index')
            ->with('success', 'Kata sandi berhasil diubah!');
    }
}

