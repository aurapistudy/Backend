<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Dashboard Ruma - Platform Edukasi Modern">
    <title>Dashboard - Ruma</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    @include('components.dashboard-shell-styles')
    <style>
        .stat-card,
        .chart-card {
            border: 1px solid rgba(0,0,0,0.04);
        }
.user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }
        
        .user-icon {
            width: 32px;
            height: 32px;
            background: var(--color-white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        
        .user-name {
            color: #FFFFFF;
            font-weight: 600;
            font-size: 0.95rem;
        }
.greeting {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--color-text);
            margin-bottom: 1.5rem;
        }

        .sub-greeting {
            color: var(--color-text-light);
            margin-top: 0.35rem;
            font-size: 0.98rem;
        }
.stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: var(--color-white);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border-left: 4px solid var(--color-primary);
            transition: transform 0.25s ease, box-shadow 0.25s ease;

        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--color-primary);
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }
        
        .stat-number {
            font-size: 2.4rem;
            font-weight: 800;
            color: var(--color-text);
            margin-bottom: 0.5rem;
            line-height: 1;
        }
        
        .stat-label {
            font-size: 1rem;
            color: var(--color-text-light);
            font-weight: 500;
        }
        
        .stat-icon {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            width: 44px;
            height: 44px;
            background: var(--color-primary-light);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            opacity: 0.45;
        }

        .stat-icon i {
            width: 22px;
            height: 22px;
        }

        .quick-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .quick-card {
            background: var(--color-white);
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            border: 1px solid rgba(0,0,0,0.04);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            display: flex;
            gap: 0.85rem;
            align-items: flex-start;
        }

        .quick-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #FFF9E6;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--color-accent);
        }

        .quick-icon i {
            width: 20px;
            height: 20px;
        }

        .quick-title {
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .quick-desc {
            color: var(--color-text-light);
            font-size: 0.92rem;
        }

        .action-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 2rem;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.7rem 1.1rem;
            border-radius: 12px;
            background: var(--color-white);
            border: 1px solid rgba(0,0,0,0.08);
            color: var(--color-text);
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: all 0.2s ease;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0,0,0,0.12);
        }

        .action-btn i {
            width: 18px;
            height: 18px;
        }
        
        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            background: var(--color-primary);
            color: var(--color-white);
            border: none;
            width: 48px;
            height: 48px;
            border-radius: 12px;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
@media (max-width: 768px) {.mobile-menu-toggle { display: flex; align-items: center; justify-content: center; } .stats-grid { grid-template-columns: 1fr; } .greeting { font-size: 1.5rem; }}
/* Overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        
        .sidebar-overlay.active {
            display: block;
        }
        
        /* Charts Section */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .chart-card {
            background: var(--color-white);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        
        .chart-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }
        
        .chart-header {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--color-gray-light);
        }
        
        .chart-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--color-text);
            margin-bottom: 0.5rem;
        }
        
        .chart-subtitle {
            font-size: 0.9rem;
            color: var(--color-text-light);
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        @media (max-width: 768px) {.charts-grid { grid-template-columns: 1fr; } .chart-container { height: 250px; }}
        </style>
</head>
<body>
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle" onclick="toggleSidebar()">
        ☰
    </button>
    
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header Bar -->
            <header class="header-bar">
                <h1 class="header-title">Dashboard</h1>
                <div class="user-info">
                    <div class="user-icon">
                        <i data-lucide="user"></i>
                    </div>
                    <div class="user-name">{{ $user->nama }} • {{ ucfirst($user->peran) }}</div>
                </div>
            </header>
            
            <!-- Content Area -->
            <div class="content-area">
                @php
                    $avgWaktu = $totalSesiBaca ? round($totalWaktuBaca / $totalSesiBaca, 1) : 0;
                    $lastWaktu = count($waktuBacaPerHari ?? []) ? end($waktuBacaPerHari) : 0;
                    $lastSesi = count($aktivitasPerHari ?? []) ? end($aktivitasPerHari) : 0;
                    $topMateri = count($materiLabels ?? []) ? $materiLabels[0] : 'Belum ada';
                @endphp
                <div class="greeting">
                    {{ $greeting }}, {{ $user->nama }}!
                    <div class="sub-greeting">Ringkasan aktivitas belajar dan pengelolaan konten terbaru.</div>
                </div>

                <div class="action-bar">
                    <a class="action-btn" href="{{ route('materi.index', [], false) }}">
                        <i data-lucide="book-plus"></i> Kelola Materi
                    </a>
                    <a class="action-btn" href="{{ route('kuis.index', [], false) }}">
                        <i data-lucide="check-square"></i> Kelola Kuis
                    </a>
                    <a class="action-btn" href="{{ route('pengguna.index', [], false) }}">
                        <i data-lucide="users"></i> Kelola Pengguna
                    </a>
                    <a class="action-btn" href="{{ route('ulasan.index', [], false) }}">
                        <i data-lucide="message-square"></i> Lihat Ulasan
                    </a>
                </div>
                
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i data-lucide="book-open"></i>
                        </div>
                        <div class="stat-number">{{ $totalMateri }}</div>
                        <div class="stat-label">Materi Tersedia</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i data-lucide="users"></i>
                        </div>
                        <div class="stat-number">{{ $totalPenggunaAktif }}</div>
                        <div class="stat-label">User Aktif</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i data-lucide="bookmark"></i>
                        </div>
                        <div class="stat-number">{{ $totalSesiBaca }}</div>
                        <div class="stat-label">Sesi Baca</div>
                    </div>

                    <div class="stat-card">
                       <div class="stat-icon">
                            <i data-lucide="clock"></i>
                        </div>
                        <div class="stat-number">{{ $totalWaktuBaca }}</div>
                        <div class="stat-label">Total Waktu Baca (menit)</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i data-lucide="message-square"></i>
                        </div>
                        <div class="stat-number">{{ $totalUlasan ?? 0 }}</div>
                        <div class="stat-label">Total Ulasan</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i data-lucide="check-square"></i>
                        </div>
                        <div class="stat-number">{{ $totalKuis ?? 0 }}</div>
                        <div class="stat-label">Total Kuis</div>
                    </div>
                </div>

                <div class="quick-grid">
                    <div class="quick-card">
                        <div class="quick-icon"><i data-lucide="timer"></i></div>
                        <div>
                            <div class="quick-title">Rata-rata Waktu/Sesi</div>
                            <div class="quick-desc">{{ $avgWaktu }} menit per sesi</div>
                        </div>
                    </div>
                    <div class="quick-card">
                        <div class="quick-icon"><i data-lucide="activity"></i></div>
                        <div>
                            <div class="quick-title">Aktivitas Terakhir</div>
                            <div class="quick-desc">{{ $lastSesi }} sesi • {{ $lastWaktu }} menit</div>
                        </div>
                    </div>
                    <div class="quick-card">
                        <div class="quick-icon"><i data-lucide="star"></i></div>
                        <div>
                            <div class="quick-title">Materi Populer</div>
                            <div class="quick-desc">{{ $topMateri }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Charts Section -->
                <div class="charts-grid">
                    <!-- Chart 1: Waktu Baca per Hari -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3 class="chart-title">Waktu Baca per Hari (7 Hari Terakhir)</h3>
                            <p class="chart-subtitle">Total waktu membaca dalam menit</p>
                        </div>
                        <div class="chart-container">
                            <canvas id="waktuBacaChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Chart 2: Aktivitas Membaca per Hari -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3 class="chart-title">Aktivitas Membaca per Hari</h3>
                            <p class="chart-subtitle">Jumlah sesi membaca per hari</p>
                        </div>
                        <div class="chart-container">
                            <canvas id="aktivitasChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Chart 3: Materi Paling Banyak Dibaca -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3 class="chart-title">Materi Paling Banyak Dibaca</h3>
                            <p class="chart-subtitle">Top 5 materi yang paling sering dibaca</p>
                        </div>
                        <div class="chart-container">
                            <canvas id="materiPopulerChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Card 4: Insight 7 Hari -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3 class="chart-title">Insight 7 Hari Terakhir</h3>
                            <p class="chart-subtitle">Ringkasan tren aktivitas belajar</p>
                        </div>
                        @php
                            $totalWaktuMingguan = array_sum($waktuBacaPerHari ?? []);
                            $totalSesiMingguan = array_sum($aktivitasPerHari ?? []);
                            $avgWaktuHarian = count($waktuBacaPerHari ?? []) ? round($totalWaktuMingguan / count($waktuBacaPerHari), 1) : 0;
                            $avgSesiHarian = count($aktivitasPerHari ?? []) ? round($totalSesiMingguan / count($aktivitasPerHari), 1) : 0;
                            $maxWaktu = count($waktuBacaPerHari ?? []) ? max($waktuBacaPerHari) : 0;
                            $maxSesi = count($aktivitasPerHari ?? []) ? max($aktivitasPerHari) : 0;
                            $topHariWaktu = $maxWaktu && count($labelsHari ?? []) ? $labelsHari[array_search($maxWaktu, $waktuBacaPerHari)] : '-';
                            $topHariSesi = $maxSesi && count($labelsHari ?? []) ? $labelsHari[array_search($maxSesi, $aktivitasPerHari)] : '-';
                        @endphp
                        <div style="display:grid; gap:0.75rem;">
                            <div class="quick-card" style="margin:0;">
                                <div class="quick-icon"><i data-lucide="clock"></i></div>
                                <div>
                                    <div class="quick-title">Total Waktu Mingguan</div>
                                    <div class="quick-desc">{{ $totalWaktuMingguan }} menit • rata-rata {{ $avgWaktuHarian }} menit/hari</div>
                                </div>
                            </div>
                            <div class="quick-card" style="margin:0;">
                                <div class="quick-icon"><i data-lucide="activity"></i></div>
                                <div>
                                    <div class="quick-title">Total Aktivitas Mingguan</div>
                                    <div class="quick-desc">{{ $totalSesiMingguan }} sesi • rata-rata {{ $avgSesiHarian }} sesi/hari</div>
                                </div>
                            </div>
                            <div class="quick-card" style="margin:0;">
                                <div class="quick-icon"><i data-lucide="trending-up"></i></div>
                                <div>
                                    <div class="quick-title">Hari Paling Aktif</div>
                                    <div class="quick-desc">{{ $topHariSesi }} • {{ $maxSesi }} sesi</div>
                                </div>
                            </div>
                            <div class="quick-card" style="margin:0;">
                                <div class="quick-icon"><i data-lucide="sparkles"></i></div>
                                <div>
                                    <div class="quick-title">Puncak Waktu Baca</div>
                                    <div class="quick-desc">{{ $topHariWaktu }} • {{ $maxWaktu }} menit</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    {{-- Include Modal Component --}}
    @include('components.modal')
    
    <script>
        // Mobile sidebar toggle (uses shell body.admin-sidebar-open)
        function toggleSidebar() {
            const overlay = document.getElementById('sidebarOverlay');
            document.body.classList.toggle('admin-sidebar-open');
            overlay.classList.toggle('active');
        }
        
        // Close sidebar when clicking outside on mobile
        document.getElementById('sidebarOverlay').addEventListener('click', function() {
            toggleSidebar();
        });
        
        // Handle logout
        function handleLogout() {
            showModal({
                type: 'logout',
                title: 'Konfirmasi Logout',
                message: 'Apakah Anda yakin ingin keluar dari akun Anda?',
                icon: 'log-out',
                confirmText: 'Ya, Keluar',
                isDanger: false,
                onConfirm: function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("logout", [], false) }}';
                    form.innerHTML = '@csrf';
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
        
        // Active nav item is handled by sidebar component
        
        // Add smooth animations
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
            
            // Initialize Charts
            initCharts();
        });
        
        // Initialize Charts
        function initCharts() {
            // Chart 1: Waktu Baca per Hari (Line Chart)
            const waktuBacaCtx = document.getElementById('waktuBacaChart');
            if (waktuBacaCtx) {
                new Chart(waktuBacaCtx, {
                    type: 'line',
                    data: {
                        labels: @json($labelsHari),
                        datasets: [{
                            label: 'Waktu Baca (menit)',
                            data: @json($waktuBacaPerHari),
                            borderColor: '#F8B803',
                            backgroundColor: 'rgba(248, 184, 3, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#F8B803',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value + ' menit';
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
            
            // Chart 2: Aktivitas Membaca per Hari (Bar Chart)
            const aktivitasCtx = document.getElementById('aktivitasChart');
            if (aktivitasCtx) {
                new Chart(aktivitasCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($labelsHari),
                        datasets: [{
                            label: 'Jumlah Sesi',
                            data: @json($aktivitasPerHari),
                            backgroundColor: 'rgba(248, 184, 3, 0.8)',
                            borderColor: '#F8B803',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
            
            // Chart 3: Materi Paling Banyak Dibaca (Horizontal Bar Chart)
            const materiPopulerCtx = document.getElementById('materiPopulerChart');
            if (materiPopulerCtx && @json(count($materiLabels)) > 0) {
                new Chart(materiPopulerCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($materiLabels),
                        datasets: [{
                            label: 'Jumlah Baca',
                            data: @json($materiData),
                            backgroundColor: [
                                'rgba(248, 184, 3, 0.8)',
                                'rgba(230, 165, 0, 0.8)',
                                'rgba(255, 193, 7, 0.8)',
                                'rgba(255, 235, 59, 0.8)',
                                'rgba(255, 245, 157, 0.8)'
                            ],
                            borderColor: '#F8B803',
                            borderWidth: 2,
                            borderRadius: 8,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            } else if (materiPopulerCtx) {
                // Show message if no data
                materiPopulerCtx.parentElement.innerHTML = '<div style="text-align: center; padding: 2rem; color: var(--color-text-light);">Belum ada data materi yang dibaca</div>';
            }
            
        }
    </script>
    <script>
    lucide.createIcons();
</script>

</body>
</html>

