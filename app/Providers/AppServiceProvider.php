<?php

namespace App\Providers;

use App\Models\LandingItem;
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
    }
}
