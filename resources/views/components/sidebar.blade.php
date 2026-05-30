<style>
    body:not(.siswa-layout) {
        scroll-behavior: smooth;
    }
    body:not(.siswa-layout).admin-sidebar-open {
        overflow: hidden;
    }

    body:not(.siswa-layout) .mobile-menu-toggle {
        display: none !important;
    }

    body:not(.siswa-layout) .admin-menu-toggle-inline {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        border: none;
        background: rgba(255, 255, 255, 0.14);
        color: #FFFFFF;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 0;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.18);
    }

    body:not(.siswa-layout) .admin-header-left {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
    }

    body:not(.siswa-layout) .admin-sidebar-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        backdrop-filter: blur(3px);
        z-index: 1190;
        display: none;
    }

    body:not(.siswa-layout) .admin-sidebar-overlay.active {
        display: block;
    }

    @media (max-width: 1024px) {
        body:not(.siswa-layout) .admin-menu-toggle {
            display: inline-flex;
        }
    }

@media (max-width: 900px) {
    body:not(.siswa-layout) .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: min(82vw, 320px);
        max-width: 320px;
        height: 100vh;
        transform: translateX(-100%);
        transition: transform 0.28s ease;
        z-index: 1200;
    }

    body:not(.siswa-layout).admin-sidebar-open .sidebar {
        transform: translateX(0);
    }

    body:not(.siswa-layout) .main-content,
    body:not(.siswa-layout) .main-content.full {
        margin-left: 0;
    }

    body:not(.siswa-layout) .user-info,
    body:not(.siswa-layout) .user-info-top {
        max-width: calc(100vw - 150px);
    }

    body:not(.siswa-layout) .admin-menu-toggle-inline {
        display: inline-flex;
        flex: 0 0 auto;
    }
}
</style>

<div class="admin-sidebar-overlay" id="adminSidebarOverlay"></div>

<!-- Sidebar Navigation -->
<nav class="sidebar-nav">
    <div class="nav-item" data-route="dashboard">
        <a href="/dashboard" data-testid="nav-dashboard" aria-label="Dashboard">
            <span class="nav-icon">
                <i data-lucide="layout-dashboard"></i>
            </span>
            <span>Dashboard</span>
        </a>
    </div>
    
    <div class="nav-item" data-route="materi">
        <a href="{{ route('materi.index', [], false) }}" data-testid="nav-materi" aria-label="Materi Pembelajaran">
            <span class="nav-icon">
                <i data-lucide="book-open"></i>
            </span>
            <span>Mata Pelajaran</span>
        </a>
    </div>
    
    @if(auth()->user()?->isAdmin())
    <div class="nav-item" data-route="fiksi">
        <a href="{{ route('fiksi.index', [], false) }}" data-testid="nav-fiksi" aria-label="Fiksi">
            <span class="nav-icon">
                <i data-lucide="bookmark"></i>
            </span>
            <span>Fiksi</span>
        </a>
    </div>
    @endif

    @if(auth()->user()?->isStaff())
    <div class="nav-item" data-route="aac">
        <a href="{{ route('aac.index', [], false) }}" data-testid="nav-aac" aria-label="AAC">
            <span class="nav-icon">
                <i data-lucide="message-circle"></i>
            </span>
            <span>AAC</span>
        </a>
    </div>
    @endif

    @if(auth()->user()?->isAdmin())
    <div class="nav-item" data-route="pengguna">
        <a href="{{ route('pengguna.index', [], false) }}" data-testid="nav-pengguna" aria-label="Pengguna">
            <span class="nav-icon">
                <i data-lucide="users"></i>
            </span>
            <span>Pengguna</span>
        </a>
    </div>
    
    <div class="nav-item" data-route="level">
        <a href="{{ route('level.index', [], false) }}" data-testid="nav-level" aria-label="Level">
            <span class="nav-icon">
                <i data-lucide="layers"></i>
            </span>
            <span>Level</span>
        </a>
    </div>

    <div class="nav-item" data-route="tahun-akademik">
        <a href="{{ route('tahun-akademik.index', [], false) }}" data-testid="nav-tahun-akademik" aria-label="Tahun Akademik">
            <span class="nav-icon">
                <i data-lucide="calendar-range"></i>
            </span>
            <span>Tahun Akademik</span>
        </a>
    </div>
    
    <div class="nav-item" data-route="landing">
        <a href="{{ route('landing.index', [], false) }}" data-testid="nav-landing" aria-label="Landing">
            <span class="nav-icon">
                <i data-lucide="layout-template"></i>
            </span>
            <span>Landing</span>
        </a>
    </div>

    <div class="nav-item" data-route="panduan">
    <a href="{{ route('panduan.index', [], false) }}" data-testid="nav-panduan" aria-label="Panduan">
        <span class="nav-icon">
            <i data-lucide="book-text"></i>
        </span>
        <span>Panduan</span>
    </a>
    </div>

    <div class="nav-item" data-route="ulasan">
        <a href="{{ route('ulasan.index', [], false) }}" data-testid="nav-ulasan" aria-label="Ulasan">
            <span class="nav-icon">
                <i data-lucide="message-square"></i>
            </span>
            <span>Ulasan</span>
        </a>
    </div>
    @endif

    <div class="nav-item" data-route="kuis">
        <a href="{{ route('kuis.index', [], false) }}" data-testid="nav-kuis" aria-label="Kuis">
            <span class="nav-icon">
                <i data-lucide="check-square"></i>
            </span>
            <span>Kuis</span>
        </a>
    </div>

    <div class="nav-item" data-route="kuis-hasil">
        <a href="{{ route('kuis.hasil.index', [], false) }}" data-testid="nav-kuis-hasil" aria-label="Hasil Kuis">
            <span class="nav-icon">
                <i data-lucide="clipboard-check"></i>
            </span>
            <span>Hasil Kuis</span>
        </a>
    </div>

    @if(auth()->user()?->isGuruMapel())
    <div class="nav-item" data-route="guru-riwayat">
        <a href="{{ route('guru.riwayat-tahun-akademik', [], false) }}" data-testid="nav-guru-riwayat" aria-label="Riwayat Tahun Akademik">
            <span class="nav-icon">
                <i data-lucide="history"></i>
            </span>
            <span>Riwayat TA</span>
        </a>
    </div>
    @endif
    
    <div class="nav-item" data-route="profile">
        <a href="/dashboard/profile" data-testid="nav-profile" aria-label="Profile">
            <span class="nav-icon">
                <i data-lucide="user"></i>
            </span>
            <span>Profile</span>
        </a>
    </div>
</nav>


<script>
// Set active nav item based on current URL
(function() {
    function setActiveNavItem() {
        const currentPath = window.location.pathname;
        const navItems = document.querySelectorAll('.nav-item');
        
        // Remove all active classes first
        navItems.forEach(item => item.classList.remove('active'));
        
        // Determine which menu should be active based on URL path
        let activeRoute = null;
        
        // Check in order of specificity (most specific first)
        // Must check specific routes BEFORE checking /dashboard to avoid conflicts
        
        if (currentPath.includes('/dashboard/landing') || currentPath.includes('/landing')) {
            activeRoute = 'landing';
        }
        else if (currentPath.includes('/dashboard/panduan') || currentPath.includes('/panduan')) {
            activeRoute = 'panduan';
        }
        else if (currentPath.includes('/dashboard/ulasan') || currentPath.includes('/ulasan')) {
            activeRoute = 'ulasan';
        }
        else if (currentPath.includes('/dashboard/kuis-hasil') || currentPath.includes('/kuis-hasil')) {
            activeRoute = 'kuis-hasil';
        }
        else if (currentPath.includes('/dashboard/guru/riwayat-tahun-akademik') || currentPath.includes('/guru/riwayat-tahun-akademik')) {
            activeRoute = 'guru-riwayat';
        }
        else if (currentPath.includes('/dashboard/kuis') || currentPath.includes('/kuis')) {
            activeRoute = 'kuis';
        }
        else if (currentPath.includes('/dashboard/level') || currentPath.includes('/level')) {
            activeRoute = 'level';
        }
        else if (currentPath.includes('/dashboard/tahun-akademik') || currentPath.includes('/tahun-akademik')) {
            activeRoute = 'tahun-akademik';
        }
        else if (currentPath.includes('/dashboard/aac') || currentPath.includes('/aac')) {
            activeRoute = 'aac';
        }
        else if (currentPath.includes('/dashboard/pengguna') || currentPath.includes('/pengguna')) {
            activeRoute = 'pengguna';
        } 
        else if (currentPath.includes('/dashboard/fiksi') || currentPath.includes('/fiksi')) {
            activeRoute = 'fiksi';
        }
        else if (currentPath.includes('/dashboard/materi') || currentPath.includes('/materi')) {
            activeRoute = 'materi';
        } 
        else if (currentPath.includes('/dashboard/profile') || currentPath.includes('/profile')) {
            activeRoute = 'profile';
        }
        // Dashboard must be checked LAST and only for exact match
        else if (currentPath === '/dashboard' || currentPath === '/dashboard/') {
            activeRoute = 'dashboard';
        }
        
        // Set active class
        if (activeRoute) {
            const activeItem = document.querySelector(`.nav-item[data-route="${activeRoute}"]`);
            if (activeItem) {
                activeItem.classList.add('active');
                console.log('Active route set to:', activeRoute, 'for path:', currentPath);
            } else {
                console.warn('Active item not found for route:', activeRoute);
            }
        } else {
            console.warn('No active route determined for path:', currentPath);
        }
    }
    
    // Run immediately and also on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setActiveNavItem);
    } else {
        setActiveNavItem();
    }
})();
</script>

<script>
// Admin sidebar toggle
(function() {
    if (document.body.classList.contains('siswa-layout')) {
        return;
    }

    function initAdminToggle() {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('adminSidebarOverlay');
        const header = document.querySelector('.header-bar');

        if (!sidebar) {
            return;
        }

        let toggle = document.querySelector('.admin-menu-toggle-inline');
        if (!toggle && header) {
            toggle = document.createElement('button');
            toggle.className = 'admin-menu-toggle-inline';
            toggle.setAttribute('aria-label', 'Toggle sidebar');
            toggle.setAttribute('type', 'button');
            toggle.innerHTML = '<i data-lucide="menu"></i>';
        }

        if (header && toggle) {
            const existingLeft = header.querySelector('.admin-header-left');
            if (!existingLeft) {
                const left = document.createElement('div');
                left.className = 'admin-header-left';
                const titleEl = header.querySelector('h1, .header-title');
                if (titleEl) {
                    header.insertBefore(left, header.firstElementChild);
                    left.appendChild(toggle);
                    left.appendChild(titleEl);
                } else {
                    header.insertBefore(toggle, header.firstElementChild);
                }
            }
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        if (!toggle) {
            return;
        }

        function isMobileViewport() {
            return window.innerWidth <= 900;
        }

        function setOpen(open) {
            if (!isMobileViewport()) {
                document.body.classList.remove('admin-sidebar-open');
                if (overlay) {
                    overlay.classList.remove('active');
                }
                return;
            }

            document.body.classList.toggle('admin-sidebar-open', open);
            if (overlay) {
                overlay.classList.toggle('active', open);
            }
        }

        function closeSidebar() {
            setOpen(false);
        }

        toggle.addEventListener('click', function() {
            const nextState = !document.body.classList.contains('admin-sidebar-open');
            setOpen(nextState);
        });

        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }

        window.addEventListener('resize', function() {
            if (!isMobileViewport()) {
                closeSidebar();
            }
        });

        document.querySelectorAll('.sidebar-nav a').forEach(function(link) {
            link.addEventListener('click', function() {
                closeSidebar();
            });
        });

        setOpen(false);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAdminToggle);
    } else {
        initAdminToggle();
    }
})();

// Log sidebar link clicks for debugging.
document.querySelectorAll('.sidebar-nav a').forEach(function(link) {
    link.addEventListener('click', function() {
        const currentUrl = window.location.href;
        const targetUrl = link.getAttribute('href') || '';
        const text = (link.textContent || '').trim();
        const params = new URLSearchParams({
            current_url: currentUrl,
            target_url: targetUrl,
            text: text,
            ts: new Date().toISOString()
        });
        const beacon = new Image();
        beacon.src = `/debug/nav-log?${params.toString()}`;
    });
});
</script>
