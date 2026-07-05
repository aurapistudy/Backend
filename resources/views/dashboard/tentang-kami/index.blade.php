<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - Ruma Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    @include('components.dashboard-shell-styles')
    <style>
        .about-header {
            background: linear-gradient(135deg, var(--color-primary) 0%, #F59E0B 100%);
            color: white;
            padding: 3rem 2rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            text-align: center;
            box-shadow: 0 4px 12px rgba(248, 184, 3, 0.3);
        }

        .about-header h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
        }

        .about-header p {
            font-size: 1.1rem;
            opacity: 0.95;
            max-width: 600px;
            margin: 0 auto;
        }

        .about-icon {
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        .content-section {
            background: var(--color-white);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0,0,0,0.04);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            color: var(--color-text);
        }

        .section-title i {
            color: var(--color-accent);
            font-size: 2rem;
        }

        .section-subtitle {
            color: var(--color-text-light);
            font-size: 0.95rem;
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .section-content {
            color: var(--color-text);
            line-height: 1.8;
            font-size: 0.95rem;
        }

        .section-content p {
            margin-bottom: 1rem;
        }

        .section-content strong {
            color: var(--color-text);
            font-weight: 700;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .feature-card {
            background: #F9FAFB;
            border: 1px solid var(--color-gray);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: var(--color-accent);
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            background: #FFF7D6;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: var(--color-accent);
        }

        .feature-title {
            font-weight: 700;
            color: var(--color-text);
            margin-bottom: 0.5rem;
        }

        .feature-desc {
            color: var(--color-text-light);
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .vision-mission-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .vm-card {
            background: linear-gradient(135deg, #F9FAFB 0%, #FFF9E6 100%);
            border: 2px solid var(--color-accent);
            border-radius: 12px;
            padding: 2rem;
            position: relative;
        }

        .vm-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--color-accent);
            border-radius: 12px 12px 0 0;
        }

        .vm-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--color-text);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .vm-title i {
            color: var(--color-accent);
            font-size: 1.5rem;
        }

        .vm-content {
            color: var(--color-text);
            line-height: 1.7;
            font-size: 0.95rem;
        }

        .highlight-box {
            background: #FFF9E6;
            border-left: 4px solid var(--color-accent);
            padding: 1rem;
            border-radius: 8px;
            margin: 1.5rem 0;
        }

        .highlight-box strong {
            color: var(--color-text);
        }

        @media (max-width: 768px) {
            .about-header h2 {
                font-size: 1.8rem;
            }

            .about-header {
                padding: 2rem 1.5rem;
            }

            .content-section {
                padding: 1.5rem;
            }

            .vision-mission-grid {
                grid-template-columns: 1fr;
            }

            .section-title {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <main class="main-content">
            
            <header class="header-bar">
                <h1 class="header-title">Tentang Kami</h1>
            </header>
            
            <div class="content-area">
                
                <!-- Header Section -->
                <div class="about-header">
                    <h2>
                        <div class="about-icon">📚</div>
                        Ruma
                    </h2>
                    <p>Platform pembelajaran interaktif yang dirancang khusus untuk mendukung pendidikan inklusif dan berkualitas bagi semua siswa.</p>
                </div>

                <!-- About Section -->
                <div class="content-section">
                    <div class="section-title">
                        <i data-lucide="briefcase"></i>
                        Tentang Ruma
                    </div>
                    <div class="section-content">
                        <p>Ruma adalah aplikasi pembelajaran digital yang inovatif dan komprehensif. Kami berkomitmen untuk memberikan pengalaman belajar yang menyenangkan, efektif, dan dapat diakses oleh semua kalangan.</p>
                        
                        <p>Dengan fitur-fitur canggih seperti materi pembelajaran terstruktur, kuis interaktif, panduan komprehensif, dan sistem pembacaan yang aksesibel, Ruma membantu siswa mencapai potensi maksimal mereka.</p>

                        <div class="highlight-box">
                            <strong>💡 Inovasi Kami:</strong> Menggabungkan teknologi terkini dengan prinsip-prinsip pedagogis yang terbukti efektif untuk menciptakan lingkungan belajar yang optimal.
                        </div>
                    </div>
                </div>
           
                <!-- Contact CTA -->
                <div class="content-section" style="text-align: center; background: linear-gradient(135deg, #FFF9E6 0%, #FFFBF0 100%);">
                    <div class="section-title" style="justify-content: center; margin-bottom: 1rem;">
                        <i data-lucide="mail"></i>
                        Dikembangkan oleh:
                    </div>
                    <p>Program Studi Sarjana Terapan Teknik Informatika</p>
                      <p>Universitas Harkat Negeri</p>
                        <p>Aura Pitaloka| 22090026 <p>
                  </div>

            </div>
        </main>
    </div>
    
    @include('components.modal')
    
    <script>
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
    </script>
    
    <script>
        lucide.createIcons();
    </script>


    
</body>
</html>