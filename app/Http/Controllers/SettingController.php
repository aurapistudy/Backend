<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Tampilkan halaman form pengaturan API.
     */
    public function edit()
{
    $settings = [
        'gemini_api_key'      => Setting::get('gemini_api_key', ''),
        'gemini_image_model'  => Setting::get('gemini_image_model', ''),
        'hf_api_token'        => Setting::get('hf_api_token', ''),
        'hf_image_model'      => Setting::get('hf_image_model', ''),
    ];

    return view('dashboard.landing.settings', compact('settings'));
}
    /**
     * Simpan perubahan pengaturan API.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'gemini_api_key'     => 'nullable|string|max:255',
            'gemini_image_model' => 'nullable|string|max:255',
            'hf_api_token'       => 'nullable|string|max:255',
            'hf_image_model'     => 'nullable|string|max:255',
        ]);

        foreach ($validated as $key => $value) {
            if ($value !== null && $value !== '') {
                Setting::set($key, $value);
            }
        }

        return redirect()
            ->route('settings.edit')
            ->with('success', 'Pengaturan API berhasil diperbarui.');
    }
}