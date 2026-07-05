<?php

namespace App\Providers;

use App\Models\LandingItem;
use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $appUrl = (string) config('app.url', '');
        if ($this->app->environment('production') || str_starts_with($appUrl, 'https://')) {
            URL::forceScheme('https');
        }

        View::composer('*', function ($view) {
            $branding = null;

            if (Schema::hasTable('landing_items')) {
                $branding = LandingItem::section('branding')
                    ->active()
                    ->orderBy('sort_order')
                    ->first();
            }

            $view->with('appBranding', (object) [
                'title' => $branding?->title ?: 'Ruma',
                'subtitle' => $branding?->subtitle ?: 'Platform edukasi modern untuk belajar lebih mudah dan terarah',
                'description' => $branding?->description,
                'image_url' => $branding?->image_path
                    ? URL::route('media.public.show', ['path' => $branding->image_path], true)
                    : asset('images/image.png'),
            ]);
        });

        // Sinkronkan API key/model dari tabel `settings` ke config runtime,
        // supaya semua service (Gemini/HuggingFace) otomatis pakai value
        // terbaru dari dashboard, bukan cuma dari .env.
        $this->syncSettingsToConfig();
    }

    private function syncSettingsToConfig(): void
    {
        try {
            if (!Schema::hasTable('settings')) {
                return;
            }

            $geminiKey   = Setting::get('gemini_api_key');
            $geminiModel = Setting::get('gemini_image_model');
            $hfToken     = Setting::get('hf_api_token');
            $hfModel     = Setting::get('hf_image_model');

            if (!empty($geminiKey)) {
                config(['services.gemini.api_key' => $geminiKey]);
            }
            if (!empty($geminiModel)) {
                config(['services.gemini.image_model' => $geminiModel]);
            }
            if (!empty($hfToken)) {
                config(['services.huggingface.api_token' => $hfToken]);
            }
            if (!empty($hfModel)) {
                config(['services.huggingface.image_model' => $hfModel]);
            }
        } catch (\Throwable $e) {
            // Diamkan saja — biarkan fallback ke env() kalau DB/tabel bermasalah
            // (misal saat migrate fresh atau saat install pertama kali).
        }
    }
}