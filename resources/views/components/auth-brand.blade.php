<div class="auth-brand">
    <img
        src="{{ $appBranding->image_url }}"
        alt="{{ $appBranding->title }} Logo"
        class="auth-brand-logo"
    >
    <h1 class="login-title">{{ $heading ?? ('Masuk ke ' . $appBranding->title) }}</h1>
    <p class="login-subtitle">{{ $subtitle ?? $appBranding->subtitle }}</p>
</div>
