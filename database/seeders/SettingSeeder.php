<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'gemini_api_key'   => env('GEMINI_API_KEY', ''),
            'gemini_image_model' => env('GEMINI_IMAGE_MODEL', 'gemini-2.5-flash-image'),
            'hf_api_token'     => env('HF_API_TOKEN', ''),
            'hf_image_model'   => env('HF_IMAGE_MODEL', 'black-forest-labs/FLUX.1-schnell'),
        ];

        foreach ($defaults as $key => $value) {
            if ($value !== '') {
                Setting::set($key, $value);
            }
        }
    }
}