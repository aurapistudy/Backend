<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo-container">
            <div class="logo-circle">
                <img src="{{ asset('images/image.png') }}" alt="Ruma Logo">
            </div>
            <div class="logo-text">Ruma</div>
        </div>
    </div>

    @include('components.sidebar')

    <div class="logout-btn" onclick="handleLogout()">
        <i data-lucide="log-out"></i>
        <span>Keluar</span>
    </div>
</aside>
